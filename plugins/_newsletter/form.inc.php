<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table PLUGIN_PATH
include_once(PLUGIN_PATH.'/config.inc.php');
# $plugin->formPercentRender = true;


$plugin->formDBTable(array('NutsNewsletter')); // put table here


$plugin->formAddFieldTextAjaxAutoComplete('Category', $lang_msg[19], true);


// fields

$plugin->formAddFieldsetStart('Info');
// $plugin->formAddFieldText('uFrom', $lang_msg[2], true, '', '', '', '', '', $NEWSLETTER_EXPEDITOR);
// $plugin->formAddFieldText('uFromLabel', $lang_msg[28], false, '', '', '', '', '', @$NEWSLETTER_EXPEDITOR_LABEL);
$plugin->formAddFieldTextAjaxAutoComplete('uFrom', $lang_msg[2], true, '', '', '', '', '', '', '', '', '', '', $NEWSLETTER_EXPEDITOR);
$plugin->formAddFieldTextAjaxAutoComplete('uFromLabel', $lang_msg[28], false, '', '', '', '', '', '', '', '', '', '', @$NEWSLETTER_EXPEDITOR_LABEL);


$plugin->formAddFieldText('Subject', $lang_msg[1], true, 'ucfirst', '', '', '', '', mb_convert_encoding($NEWSLETTER_SUBJECT, 'utf-8'));
$plugin->formAddFieldsetEnd();


$plugin->formAddFieldBoolean('TemplateMode', 'Template mode', true);
// $plugin->formAddException('TemplateMode');

$plugin->formAddFieldsetStart('Template');
$plugin->formAddFieldFileBrowser('TemplateFile', 'Template', false, 'nuts_newsletter', '', 'width:600px');
$plugin->formAddFieldsetEnd();

$plugin->formAddFieldsetStart('Body');
$plugin->formAddFieldHtmlArea('Body', $lang_msg[6], false, 'height:500px', $lang_msg[13]);
$plugin->formAddFieldsetEnd();


// test mode
$plugin->formAddFieldBoolean('ModeTest', $lang_msg[7], true);

$plugin->formAddFieldsetStart('TestTo', $lang_msg[7]);
$plugin->formAddFieldText('To', $lang_msg[8].' Test', false, '', '', '', '', $lang_msg[9], $NEWSLETTER_TO_TEST);
$plugin->formAddFieldsetEnd();


// list
$plugin->formAddFieldsetStart('MailingList', 'Mailing-List');

$nuts->doQuery("SELECT
						NutsNewsletterMailingList.ID,
						NutsNewsletterMailingList.Name,
						(SELECT COUNT(*) FROM NutsNewsletterMailingListSuscriber WHERE Deleted = 'NO' AND UnsuscribeNewletterID = 0 AND NutsNewsletterMailingListID = NutsNewsletterMailingList.ID) AS Count
				FROM
						NutsNewsletterMailingList
				WHERE
						Deleted = 'NO'
				ORDER BY Name");
$ml_options = array();
while($ml = $nuts->dbFetch())
{
	// $selected = '';
	
	$ml['Count'] = int_formatX($ml['Count']);
	$ml_options[] = array('label' => $ml['Name']." ({$ml['Count']})", 'value' => $ml['ID']);
}

$plugin->formAddFieldSelectMultiple('MailingList[]', 'Mailing-List', false, $ml_options, '', '', 'multiple size="5"', true, $lang_msg[12]);
$plugin->formAddFieldsetEnd();



$plugin->formAddFieldBoolean('Draft', $lang_msg[14], true);


// scheduler
$plugin->formAddFieldsetStart('SchedulerDate', $lang_msg[17]);
$plugin->formAddFieldDateTime('SchedulerDate', 'Date', false);
$plugin->formAddFieldTextAjaxAutoComplete('SchedulerFinishEmail', $lang_msg[18], false);
$plugin->formAddFieldsetEnd();


$plugin->formAddException('ModeTest');
$plugin->formAddException('MailingList');
$plugin->formAddException('To');




// form rules *************************************************************************
if($_POST)
{
	$_POST['Category'] = ucfirst($_POST['Category']);
	
	
	if($_POST['TemplateMode'] == 'YES')
	{
		$nuts->notEmpty('TemplateFile');

		// template file must be called index.html
		$f = basename($_POST['TemplateFile']);
		if($f != 'index.html')
			$nuts->addError('TemplateFile', $lang_msg[20]);
		
		
		if(!file_exists(WEBSITE_PATH.$_POST['TemplateFile']))
			$nuts->addError('TemplateFile', $lang_msg[21]);
	}
	else
	{
		$nuts->notEmpty('Body');
	}
	
	
	// prepare element
	if($_POST['TemplateMode'] == 'NO')
	{
		$_POST['Body'] = str_replace('<p', "\n<p", $_POST['Body']);
		$_POST['Body'] = str_replace('<br>', "<br>\n", $_POST['Body']);
		$_POST['Body'] = str_replace('</p>', "\n</p>", $_POST['Body']);
		
		$body = str_replace('src="/', 'src="'.WEBSITE_URL.'/', $_POST['Body']);
		$body = str_replace('url(/', 'url('.WEBSITE_URL.'/', $body);
		$body = str_replace('href="/', 'href="'.WEBSITE_URL.'/', $body);
		$body = str_replace('[BODY]', $body, $HTML_TEMPLATE);
	}
	else
	{
		$body = file_get_contents(WEBSITE_PATH.$_POST['TemplateFile']);
	}

	// check link
	if(strpos($body, '[UNSUBSCRIBE_LINK]') === false)
		$nuts->addError('Body', $lang_msg[10]);
	

	// mode test
	if($_POST['ModeTest'] == 'YES')
	{
		$nuts->notEmpty('To');
	}
	else
	{
		$nuts->notEmpty('MailingList');
	}
	
	
	// check limit by month
	if($_POST['ModeTest'] == 'NO')
	{
		$date = date('Y-m-%');
		$count_month = (int)Query::factory()->select("SUM(TotalSend)")->from('NutsNewsletter')->whereLike('SchedulerDate', "{$date}")->executeAndGetOne();
		
		if(isset($_POST['MailingList']) && is_array($_POST['MailingList']) && count($_POST['MailingList']) > 0)
		{
			$count_current_email = (int)Query::factory()->select("COUNT(*)")->from('NutsNewsletterMailingListSuscriber')->whereIn('NutsNewsletterMailingListID', $_POST['MailingList'])->executeAndGetOne();
			$count_month += $count_current_email;
		}
		
		
		if($count_month > PLUGIN_NEWSLETTER_MAXIMUM_SENT_BY_MONTH)
		{
			$nuts->addError('SchedulerDate', $lang_msg[29]." (max: ".PLUGIN_NEWSLETTER_MAXIMUM_SENT_BY_MONTH.")");
		}
	}
	
	
	
	

	// alright *****************************************************************************************
	if($nuts->formGetTotalError() == 0)
	{
		$nuts->mailCharset('UTF-8');
		$nuts->mailFrom($_POST['uFrom'], $_POST['uFromLabel']);
		$nuts->mailSubject($_POST['Subject']);
		
		// send email test
       	if($_POST['ModeTest'] == 'YES')
		{
			$nuts->mailTo($_POST['To']);
			$body = str_replace('[UNSUBSCRIBE_LINK]', '#test_mode', $body);
			$body = str_replace('[FIRSTNAME]', '#Firstname', $body);
			$body = str_replace('[LASTNAME]', '#Lastname', $body);
			
			$nuts->mailBody($body, 'HTML');
			$nuts->mailSend();
			
			$nuts->addError('ModeTest', $lang_msg[11]." => `{$_POST['To']}` "); # simulate to block
			
		}
	}
	
	// errors control
	if(!$nuts->formGetTotalError() && $_POST['Draft'] == 'NO')
	{
		$nuts->notEmpty('SchedulerDate');
		
		if($_POST['ModeTest'] == 'YES')
			$nuts->addError('ModeTest', $lang_msg[24]);
	}
	
	if(isset($_POST['MailingList']))
		$_POST['MailinglistIDs'] = serialize($_POST['MailingList']);
		
	// token verification
	if(!isset($NEWSLETTER_SCHEDULER_TOKEN) || empty($NEWSLETTER_SCHEDULER_TOKEN))
	{
		$nuts->addError('ModeTest', "Please configure or add NEWSLETTER_SCHEDULER_TOKEN in configuration");
	}
	
}



