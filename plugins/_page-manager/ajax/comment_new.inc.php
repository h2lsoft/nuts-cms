<?php

$IP = $nuts->getIp();
$IP_long = ip2long($IP);
$CID = $nuts->dbInsert('NutsPageComment', array(
	'Date' => 'NOW()',
	'NutsPageID' => $_GET['ID'],
	'NutsUserID' => $_SESSION['ID'],
	'Name' => $_POST['CommentName'],
	'Message' => nl2br(ucfirst($_POST['CommentText'])),
	'Email' => $_SESSION['Email'],
	'Website' => WEBSITE_URL,
	'Visible' => 'NO',
	'Suscribe' => 'YES',
	'IP' => $IP_long
), array(), true);

$plugin->trace('comment_new', (int)$CID, print_r($_POST, true));

nutsTrigger('page-manager::comment_new', true, "action new on comment");

// launch exec for all suscribers
$uri = WEBSITE_URL.'/plugins/_comments/www/exec.php?action=';
$action= base64_encode("do=show&email_admin={$_SESSION['Email']}&show&ID=$CID&lang={$_POST['Language']}&NutsPageID={$_GET['ID']}&Email={$_SESSION['Email']}");

$fp = file_get_contents($uri.strrev($action));
exit(1);




?>