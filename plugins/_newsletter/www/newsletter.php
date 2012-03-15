<?php
/**
 * Script use for newsletter
 * action = affiate | unsuscribe | suscribe
 */

include_once("../../../nuts/config.inc.php");
include_once(WEBSITE_PATH."/nuts/headers.inc.php");
include_once("../config.inc.php");


// controller *************************************************************************
if(!isset($_GET['action']) || !in_array($_GET['action'], array('suscribe', 'affiliate', 'unsuscribe')))
	die("Error: action parameter");

// view
if($_GET['action'] == 'affiliate')
{
	if(!isset($_GET['affiliateId']))
		die("Error: affiliateId parameter");

	// check information
	$aff = explode(';', base64_decode(strrev($_GET['affiliateId'])));
	if(count($aff) != 3 || !email($aff[0]))
		die("Error: affiliateId parameter");

	$aff[1] = (int)$aff[1];
	$aff[2] = (int)$aff[2];
}

// unsuscribe
if($_GET['action'] == 'unsuscribe')
{
	if(!isset($_GET['suscriber']))
		die("Error: suscriber parameter");

	// check information
	$aff = explode(';', base64_decode(strrev($_GET['suscriber'])));
	if(count($aff) != 3 || !email($aff[0]))
		die("Error: suscriber parameter");

	$aff[1] = (int)$aff[1];
	$aff[2] = (int)$aff[2];
}

// suscribe
if($_GET['action'] == 'suscribe')
{	
	if(!isset($_POST['Email']) || !email($_POST['Email']))
		die("-1");

	if(!isset($_POST['Language']))
		die("-2");
	
	if(!isset($_POST['MailingListID']))
		die("-3");

	$_POST['MailingListID'] = (int)$_POST['MailingListID'];
	if($_POST['MailingListID'] == 0)
		die("-3");
}

// execution *************************************************************************
$nuts = new NutsCore();
$nuts->dbConnect();

// suscriber view
if($_GET['action'] == 'affiliate')
{
	$nuts->doQuery("DELETE FROM NutsNewsletterData WHERE NutsNewsletterMailingListSuscriberID = {$aff[1]} AND NutsNewsletterID = {$aff[2]}");
	$nuts->dbInsert("NutsNewsletterData", array(
													'Date' => 'NOW()',
													'NutsNewsletterMailingListSuscriberID' => $aff[1],
													'NutsNewsletterID' => $aff[2]));
}
// unsuscribe
elseif($_GET['action'] == 'unsuscribe')
{
	$nuts->dbUpdate("NutsNewsletterMailingListSuscriber", array(
													'Deleted' => 'YES',
													'UnsuscribeDate' => 'NOW()',													
													'UnsuscribeNewletterID' => $aff[2]), "ID={$aff[1]}");
}
// suscribe
elseif($_GET['action'] == 'suscribe')
{
	$nuts->dbSelect("SELECT 
							ID
					 FROM
							NutsNewsletterMailingListSuscriber
					 WHERE
							NutsNewsletterMailingListID = '%s' AND
							Deleted = 'NO' AND
							Email = '%s' AND
							Language = '%s'", array($_POST['MailingListID'], $_POST['Email'], $_POST['Language']));
	if($nuts->dbNumRows() == 0)
	{
		$nuts->dbInsert("NutsNewsletterMailingListSuscriber", array(
													'Date' => 'NOW()',
													'NutsNewsletterMailingListID' => $_POST['MailingListID'],
													'Email' => $_POST['Email'],
													'Language' => $_POST['Language']));
	}
}


$nuts->dbClose();

// suscriber view generate image 5 x 5
if($_GET['action'] == 'affiliate')
{
	header("Content-type: image/png");
	$im     = imagecreate(10, 10);
    $background_color = imagecolorallocate($im, $NEWSLETTER_IMG_TRACER_COLOR[0], $NEWSLETTER_IMG_TRACER_COLOR[1], $NEWSLETTER_IMG_TRACER_COLOR[2]);
	imagepng($im);
	imagedestroy($im);
}
// unsuscribe
elseif($_GET['action'] == 'unsuscribe')
{
	$nuts->redirect($NEWSLETTER_UNSUSCRIBE_URL_CONFIRMATION);
}
// suscribe
elseif($_GET['action'] == 'suscribe')
{
	die("ok");
}









?>