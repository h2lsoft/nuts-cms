<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table PLUGIN_PATH
include_once(PLUGIN_PATH.'/config.inc.php');

$plugin->formPercentRender = true;


$plugin->formDBTable(array('NutsNewsletter')); // put table here

// fields
$plugin->formAddFieldText('uFrom', $lang_msg[2], true, '', '', '', '', '', $NEWSLETTER_EXPEDITOR);
$plugin->formAddFieldText('Subject', $lang_msg[1], true, '', '', '', '', '', mb_convert_encoding($NEWSLETTER_SUBJECT, 'utf-8'));
$plugin->formAddFieldHtmlArea('Body', $lang_msg[6], true, 'height:500px', $lang_msg[13]);

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
	$ml['Count'] = int_formatX($ml['Count']);
	$ml_options[] = array('label' => $ml['Name']." ({$ml['Count']})", 'value' => $ml['ID']);
}

$plugin->formAddFieldSelectMultiple('MailingList[]', 'Mailing-List', false, $ml_options, '', '', 'multiple size="5"', true, $lang_msg[12]);
$plugin->formAddFieldsetEnd();

$plugin->formAddException('ModeTest');
$plugin->formAddException('MailingList*');
$plugin->formAddException('To');

// form rules *************************************************************************
if($_POST)
{
	// prepare element
	$body = str_replace('src="/', 'src="'.WEBSITE_URL.'/', $_POST['Body']);
	$body = str_replace('url(/', 'url('.WEBSITE_URL.'/', $body);
	$body = str_replace('href="/', 'href="'.WEBSITE_URL.'/', $body);

	$body = str_replace('[BODY]', $body, $HTML_TEMPLATE);

	if(strpos($body, '[UNSUSCRIBE_LINK]') === false)
		$nuts->addError('Body', $lang_msg[10]);

	// mode test
	if($_POST['ModeTest'] == 'YES')
	{
		$nuts->notEmpty('To');
	}
	else
	{
		$nuts->notEmpty('MailingList[]');
	}

	// alright
	if($nuts->formGetTotalError() == 0)
	{
		$nuts->mailCharset('UTF-8');
		$nuts->mailFrom($_POST['uFrom']);
		$nuts->mailSubject($_POST['Subject']);


		// send email test
       	if($_POST['ModeTest'] == 'YES')
		{
			$nuts->mailTo($_POST['To']);
			$body = str_replace('[UNSUSCRIBE_LINK]', '#test_mode', $body);
			$body = str_replace('[FIRSTNAME]', '#Firstname', $body);
			$body = str_replace('[LASTNAME]', '#Lastname', $body);
			$nuts->addError('ModeTest', $lang_msg[11]);

			$nuts->mailBody($body, 'HTML');
			$nuts->mailSend();
		}
		else
		{
			// Warning ! big treatment is done after adding newsletter
		}
	}
}







