<?php

// includes *************************************************************************
include("../../nuts/config.inc.php");
include(WEBSITE_PATH."/nuts/headers.inc.php");
include("config.inc.php");


// configuration *************************************************************************
set_time_limit(0);
error_reporting(E_ALL);

if(isset($NEWSLETTER_MEMORY_LIMIT))
	ini_set('memory_limit', $NEWSLETTER_MEMORY_LIMIT);

// execution *************************************************************************
$plugin = new NutsCore();
$plugin->dbConnect();
$nuts = &$plugin;


// execution
$nuts->mailCharset('UTF-8');

$nuts->dbConnect();

$sql = "SELECT
				*
		FROM
				NutsNewsletter
		WHERE
				Deleted = 'NO' AND
				Draft = 'NO' AND
				SchedulerStart = 'NO' AND
				SchedulerDate <= NOW()
		ORDER BY
				ID";

$nuts->doQuery($sql);
$newsletters = $nuts->dbGetData();



// direct update status to lock
foreach($newsletters as $n)
{
	// update status
	$f = [];
	$f['SchedulerStart'] = 'YES';
	$f['SchedulerDateStart'] = 'NOW()';
	$nuts->dbUpdate('NutsNewsletter', $f, "ID={$n['ID']}");
}

if(!count($newsletters))
{
	die('No newsletter');
}

$mx_valid_domains = [];
$mx_error_domains = [];

foreach($newsletters as $n)
{
	echo "Newsletter #{$n['ID']}<br>";
	
	
	// mailing list treatment
	$nuts->mailFrom($n['uFrom'], $n['uFromLabel']);
	$nuts->mailSubject($n['Subject']);
	
	if($n['TemplateMode'] == 'NO')
	{
		$body = str_replace('src="/', 'src="'.WEBSITE_URL.'/', $n['Body']);
		$body = str_replace('url("/', 'url("'.WEBSITE_URL.'/', $body);
		$body = str_replace('url(/', 'url('.WEBSITE_URL.'/', $body);
		$body = str_replace('href="/', 'href="'.WEBSITE_URL.'/', $body);
		$body = str_replace('[BODY]', $body, $HTML_TEMPLATE);
	}
	else
	{
		$body = file_get_contents(WEBSITE_PATH.$n['TemplateFile']);
	}
	
	$body_original = $body;
	
	$mailinglistIDs = unserialize($n['MailinglistIDs']);
	$mailinglistIDs = join(', ', $mailinglistIDs);
	
	// $suscribers
	$sql = "SELECT
					ID, FirstName, LastName, Email, Language
			FROM
					NutsNewsletterMailingListSuscriber
			WHERE
					Deleted = 'NO' AND
					NutsNewsletterMailingListID IN($mailinglistIDs) AND
					Email != ''
			ORDER BY
				    RAND()";
	$nuts->doQuery($sql);
	
	$total_send = 0;
	$email_sended = [];
	$email_sended_error = [];
	while($suscriber = $nuts->dbFetch())
	{
		$suscriber['Email'] = trim($suscriber['Email']);
		$suscriber['Email'] = strtolower($suscriber['Email']);
		
		
		// replace [UNSUBSCRIBE_LINK]
		$enc = $suscriber['Email'].';'.$suscriber['ID'].';'.$n['ID'];
		$uri_unsuscribe = WEBSITE_URL.'/plugins/_newsletter/www/newsletter.php?action=unsuscribe&lang='.$suscriber['Language'].'&suscriber='.strrev(base64_encode($enc)).'&';
		$cur_body = str_replace('[UNSUBSCRIBE_LINK]', $uri_unsuscribe, $body_original);
		
		// replace [FIRSTNAME], [LASTNAME]
	    $cur_body = str_replace('[LASTNAME]', $suscriber['LastName'], $cur_body);
	    $cur_body = str_replace('[FIRSTNAME]', $suscriber['FirstName'], $cur_body);
	    
	    // add image tracer
		$uri_tracer = WEBSITE_URL.'/plugins/_newsletter/www/newsletter.php?action=affiliate&affiliateId='.strrev(base64_encode($enc));
		$cur_body .= '<img src="'.$uri_tracer.'" />';
	 
		// send data
		$nuts->mailTo($suscriber['Email']);
		$nuts->mailBody($cur_body, 'HTML');
		
		
		// check valid domain
		list($user_mail_login, $domain) = @explode('@', $suscriber['Email'], 2);
		
		/*if(in_array($domain, $mx_error_domains))
		{
			if(!in_array($email_sended_error, $suscriber['Email']))$email_sended_error[] = $suscriber['Email'];
			if(!in_array($email_sended, $suscriber['Email']))$email_sended[] = $suscriber['Email']; # mark as sent
		}
		elseif(!in_array($domain, $mx_valid_domains))
		{
			if(!checkdnsrr($domain))
			{
				$mx_error_domains[] = $domain;
				if(!in_array($email_sended_error, $suscriber['Email']))$email_sended_error[] = $suscriber['Email'];
				if(!in_array($email_sended, $suscriber['Email']))$email_sended[] = $suscriber['Email']; # mark as sent
			}
			else
			{
				$mx_valid_domains[] = $domain;
			}
		}*/

		
		// check validate email
		if(empty($suscriber['Email']) || !filter_var($suscriber['Email'], FILTER_VALIDATE_EMAIL))
		{
			if(!in_array($suscriber['Email'], $email_sended_error))$email_sended_error[] = $suscriber['Email'];
			if(!in_array($suscriber['Email'], $email_sended))$email_sended[] = $suscriber['Email']; # mark as sent
		}
		
		// send
		if(!in_array($suscriber['Email'], $email_sended))
		{
			if(!$nuts->mailSend())
			{
				$email_sended_error[] = $suscriber['Email'];
				$email_sended[] = $suscriber['Email']; # mark as sent
			}
			else
			{
				$email_sended[] = $suscriber['Email'];
				$total_send++;
			}
		}
		
		// realtime> treatment
		$qID = $nuts->dbGetQueryID();
		$f = [];
		$f['TotalSend'] = $total_send;
		$f['TotalError'] = count($email_sended_error);
		$nuts->dbUpdate('NutsNewsletter', $f, "ID={$n['ID']}");  # DEBUG
		$nuts->dbSetQueryID($qID);
		
		if($total_send > 0 && ($total_send % PLUGIN_NEWSLETTER_BREAK) == 0)
		{
			echo "+++++ PAUSE ".PLUGIN_NEWSLETTER_SLEEPTIME."s +++++++++<br>";
			sleep(PLUGIN_NEWSLETTER_SLEEPTIME);
		}
	}
	
	
	// finish
	$f = [];
	$f['SchedulerFinished'] = 'YES';
	$f['SchedulerDateEnd'] = 'NOW()';
	$f['TotalSend'] = $total_send;
	$f['TotalError'] = count($email_sended_error);
	$f['TotalErrorEmail'] = join('\n', $email_sended_error);
	
	$nuts->dbUpdate('NutsNewsletter', $f, "ID={$n['ID']}");  # DEBUG
	
	
	
	echo "Finished $total_send sended - ".count($email_sended_error)." errors<br>";

	// report AR
	if(!empty($n['SchedulerFinishEmail']))
	{
		$nuts->doQuery("SELECT * FROM NutsNewsletter WHERE ID = {$n['ID']}");
		$n = $nuts->dbFetch();
		
		$message = "<b>Newsletter #{$n['ID']} :</b> {$n['Subject']}<br><br>";
		$message .= "<b>Date start :</b> {$n['SchedulerDateStart']}<br>";
		$message .= "<b>Date end :</b> {$n['SchedulerDateEnd']}<br>";
		$message .= "<b>Total Email sent :</b> {$n['TotalSend']}<br>";
		$message .= "<b>Total Email error:</b> {$n['TotalError']}<br>";
		
		$n['SchedulerFinishEmail'] = str_replace(' ', '', $n['SchedulerFinishEmail']);
		$n['SchedulerFinishEmail'] = str_replace(',', ';', $n['SchedulerFinishEmail']);
		$tos = explode(';', $n['SchedulerFinishEmail']);
		
		foreach($tos as $to)
		{
			$to = trim($to);
			$to = strtolower($to);
			if(!empty($to))
			{
				$nuts->mailCharset('UTF-8');
				$nuts->mailFrom(NUTS_EMAIL_NO_REPLY);
				$nuts->mailTo($to);
				$nuts->mailSubject(WEBSITE_NAME."> Newsletter Reporting #{$n['ID']}");
				$nuts->mailBody($message, 'HTML');
				$nuts->mailSend();
			}
		}
	}
	
	
}


$nuts->dbClose();




