<?php

/* @var $nuts NutsCore */

// includes *************************************************************************
include_once("../../../nuts/config.inc.php");
include_once(WEBSITE_PATH."/nuts/headers.inc.php");
include_once(NUTS_PLUGINS_PATH."/_comments/config.inc.php");


// controller *************************************************************************
if(!isset($_GET['action']))
{
	die("Error: action not found");
}
else
{
	$_GET['action'] = base64_decode(strrev($_GET['action']));
	$arr = explode("&", $_GET['action']);

	foreach($arr as $v){
		@list($key, $val) = @explode('=', $v);
		$_GET[$key] = $val;
	}

	// NutspageID
	$_GET['NutsPageID'] = (int)@$_GET['NutsPageID'];
	/*if(!$_GET['NutsPageID'])
		die("Error: NutsPageID parameter not found");*/

	// ID
	$_GET['ID'] = (int)@$_GET['ID'];
	if(!$_GET['ID'])
		die("Error: ID parameter not found");

	// action
	if(!isset($_GET['do']) || !in_array($_GET['do'], array('show', 'hide', 'delete','unsuscribe')))
		die("Error: do parameter not found");

	// lang
	if(!isset($_GET['lang']))
		die("Error: lang parameter not found");
}




// execution *************************************************************************
$nuts = new NutsCore();
$nuts->dbConnect();

// delete
if($_GET['do'] == 'delete')
{
	$nuts->dbSelect("SELECT ID FROM NutsPageComment WHERE Deleted = 'NO' AND NutsPageID = %s AND ID = %s", array($_GET['NutsPageID'], $_GET['ID']));
	if($nuts->dbNumRows() == 0)
	{
		echo "Error: Comment #{$_GET['ID']} not found";
	}
	else
	{
		$nuts->dbUpdate('NutsPageComment', array('Deleted' => 'YES'), "ID={$_GET['ID']}");
		echo "Comment #{$_GET['ID']} has been deleted";
	}
}
// show
elseif($_GET['do'] == 'show')
{
	$nuts->dbSelect("SELECT * FROM NutsPageComment WHERE Visible = 'NO' AND Deleted = 'NO' AND NutsPageID = %s AND ID = %s", array($_GET['NutsPageID'], $_GET['ID']));
	if($nuts->dbNumRows() == 0)
	{
		echo "Error: Comment #{$_GET['ID']} not found or already visible";
	}
	else
	{
		$rec = $nuts->dbFetch();

		$nuts->dbUpdate('NutsPageComment', array('Visible' => 'YES'), "ID={$_GET['ID']}");

		// send multiple email suscriber
		$nuts->dbSelect("SELECT DISTINCT Email FROM NutsPageComment WHERE Deleted = 'NO' AND NutsPageID = %s AND Suscribe = 'NO'", array($_GET['NutsPageID']));
		$unbanneds = array();
		while($row = $nuts->dbFetch())
			$unbanneds[] = $row['Email'];
		$unbanneds = array_unique($unbanneds);

		// get list suscriber
		$nuts->dbSelect("SELECT DISTINCT Email FROM NutsPageComment WHERE Deleted = 'NO' AND NutsPageID = %s AND Suscribe = 'YES'", array($_GET['NutsPageID']));
		$suscribers = array();
		while($row = $nuts->dbFetch())
		{
			if(!in_array($row['Email'], $unbanneds) && $row['Email'] != $_GET['email_admin'] && $rec['Email'] != $row['Email'])
				$suscribers[] = $row['Email'];
		}
		$suscribers = array_unique($suscribers);

		// get page information
		$sql = "SELECT ID, H1, Language FROM NutsPage WHERE ID = {$_GET['NutsPageID']}";
		$nuts->doQuery($sql);
		$page_info = $nuts->dbFetch();

		// dynamic language file
		if(file_exists(NUTS_PLUGINS_PATH."/_comments/www/{$page_info['Language']}.inc.php"))
			include(NUTS_PLUGINS_PATH."/_comments/www/{$page_info['Language']}.inc.php");
		else
			include(NUTS_PLUGINS_PATH."/_comments/www/en.inc.php");

		// send email
		$nuts->mailCharset('UTF8');
		$nuts->mailFrom($comments_email_notify_from);
		$nuts->mailSubject($comments_email_subject.' '.$p_lng['MailSubject']." `{$page_info['H1']}` (#{$page_info['ID']})");


		foreach($suscribers as $suscriber)
		{
			$body = str_replace('{Name}', $rec['Name'], $p_lng['SuscriberMailMessage']);

			// Gravatar
			$email = $rec['Email'];
			$default = (empty($comments_avatar_default_image_url)) ? WEBSITE_URL.'/plugins/_comments/www/anonymous.gif': $comments_avatar_default_image_url;
			$size = $comments_avatar_size;
			$grav_url = "https://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?d=".urlencode($default)."&s=".$size;
			$body = str_replace('{Gravatar}', $grav_url, $body);

			$body = str_replace('{Email}', $rec['Email'], $body);
			$body = str_replace('{CommentID}', $rec['ID'], $body);
			$body = str_replace('{H1}', $page_info['H1'], $body);
			$body = str_replace('{URI}', WEBSITE_URL."/{$page_info['Language']}/{$page_info['ID']}.html", $body);
			$body = str_replace('{Comment}', $rec['Message'], $body);
			$body = trim($body);

			$uri = WEBSITE_URL.'/plugins/_comments/www/exec.php?action=';
			$CID = $rec['ID'];

			// uri unsuscribe
			$action= base64_encode("do=unsuscribe&show&ID=$CID&lang={$page_info['Language']}&NutsPageID={$_GET['NutsPageID']}&Email=$suscriber");
			$body = str_replace('{UriUnsuscribe}', $uri.strrev($action), $body);

			$nuts->mailTo($suscriber);
			$nuts->mailBody($body, 'HTML');
			$nuts->mailSend();
		}

		echo "Comment #{$_GET['ID']} is now visible";
	}
}
// unsuscribe
elseif($_GET['do'] == 'unsuscribe')
{
	$nuts->dbSelect("SELECT * FROM NutsPageComment WHERE Visible = 'YES' AND Deleted = 'NO' AND NutsPageID = %s AND ID = %s", array($_GET['NutsPageID'], $_GET['ID']));
	if($nuts->dbNumRows() == 0)
	{
		echo "Error: Comment #{$_GET['ID']} not found";
	}
	else
	{
		$nuts->dbUpdate('NutsPageComment', array('Suscribe' => 'NO'), "Email=\"{$_GET['Email']}\" AND NutsPageID = {$_GET['NutsPageID']}");
		echo "You will not receive email comments from this feed";
	}
}




$nuts->dbClose();
























?>