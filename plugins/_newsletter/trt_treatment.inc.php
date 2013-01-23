<?php
/**
 * Send newsletter
 */

/* @var $nuts NutsCore */
/* @var $plugin Plugin */

// init
set_time_limit(0);
ignore_user_abort(true);
include_once(PLUGIN_PATH.'/config.inc.php');

// execution
$nuts->mailCharset('UTF-8');
$nuts->mailFrom($_POST['uFrom']);
$nuts->mailSubject($_POST['Subject']);

$body = str_replace('src="/', 'src="'.WEBSITE_URL.'/', $_POST['Body']);
$body = str_replace('url(/', 'url('.WEBSITE_URL.'/', $body);
$body = str_replace('href="/', 'href="'.WEBSITE_URL.'/', $body);
$body = str_replace('[BODY]', $body, $HTML_TEMPLATE);
$body_original = $body;

$mailinglistIDs = join(',', $_POST['MailingList']);
for($i=0; $i < count($mailinglistIDs); $i++)
	$mailinglistIDs[$i] = (int)$mailinglistIDs[$i];

$sql = "SELECT ID, Email, Language FROM NutsNewsletterMailingListSuscriber WHERE NutsNewsletterMailingListID IN($mailinglistIDs) AND Deleted = 'NO' GROUP BY Email LIMIT {$_SESSION['FormPercentParams'][0]}, ".PLUGIN_NEWSLETTER_BREAK;
$nuts->doQuery($sql);
$total_send = $_SESSION['FormPercentParams'][0];
while($suscriber = $nuts->dbFetch())
{
	$_SESSION['FormPercentParams'][0]++;
	$percent = ($_SESSION['FormPercentParams'][0] / $_SESSION['FormPercentParams'][1]) * 100;
	$percent = (int)$percent;

	// replace [UNSUSCRIBE_LINK]
	$enc = $suscriber['Email'].';'.$suscriber['ID'].';'.$_SESSION['FormPercentRecordID'];
	$uri_unsuscribe = WEBSITE_URL.'/plugins/_newsletter/www/newsletter.php?action=unsuscribe&suscriber='.strrev(base64_encode($enc)).'&lang='.$suscriber['Language'];
	$cur_body = str_replace('[UNSUSCRIBE_LINK]', $uri_unsuscribe, $body_original);

	// add image tracer
	$uri_tracer = WEBSITE_URL.'/plugins/_newsletter/www/newsletter.php?action=affiliate&affiliateId='.strrev(base64_encode($enc));
	$cur_body .= '<img src="'.$uri_tracer.'" />';

	// send data
	$nuts->mailTo($suscriber['Email']);
	$nuts->mailBody($cur_body, 'HTML');
	if($nuts->mailSend())
	{
		$total_send++;
	}

}

if(!isset($percent))$percent = 100;

$vals =  array(
				'percent' => $percent,
				'start' => $_SESSION['FormPercentParams'][0],
				'end' => $_SESSION['FormPercentParams'][1]
			  );

$sql = "UPDATE NutsNewsletter SET TotalSend = $total_send WHERE ID = {$_SESSION['FormPercentRecordID']}";
$nuts->doQuery($sql);

sleep(PLUGIN_NEWSLETTER_SLEEPTIME);
die(json_encode($vals));



?>