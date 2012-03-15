<?php

/************************************************************************************************************* 
*						H2LSOFT,Inc - www.h2lsoft.com
*						______________________________
*
*	version: 1.0
*	author: H2LSOFT
*	ressourceID: 1
*	date: 13/12/2006
*	
*	resume:
*
*	changeLog:
*
*************************************************************************************************************/


$sn = 'PHPSESSID'; // default
if(isset($_GET['sn']))
{
	$sn = base64_decode($_GET['sn']);
	session_name($sn);
}
if($sn == 'PHPSESSID')$sn = '';
@session_start();


if(empty($sn))
	$session_tpln_captcha = $_SESSION['tpln_captcha'];
else
	$session_tpln_captcha = $_SESSION[$sn]['tpln_captcha'];	

$width = 120;
$height = 30;

// Set the enviroment variable for GD
putenv('GDFONTPATH=' . realpath('.'));

$fonts = array('arial.ttf', 'verdana.ttf', 'tahoma.ttf', 'georgia.ttf');


$bkg = imagecreatefromjpeg('bkg0.jpg');
$image = imagecreate($width, $height);

//We are making three colors, white, black and gray 
$white = ImageColorAllocate($image, 255, 255, 255); 
$black = ImageColorAllocate($image, 0, 0, 0); 
$grey = ImageColorAllocate($image, 204, 204, 204); 


imagefill($image, 0, 0, $white);
imagecopy($image, $bkg, 0, 0, 0, 0, $width, $height); 
imagedestroy($bkg);






$text_color = $white;




$captcha_len = strlen($session_tpln_captcha);
$angle = 1;
for($i=0; $i <  $captcha_len; $i++)
{
	$rnd = rand(0, count($fonts)-1);
	$police = $fonts[$rnd];
	@imagettftext($image, 14, 12*$angle, $i*($width/$captcha_len)+5, ($height/2)+5, $text_color, $police, $session_tpln_captcha[$i]);
	
	($angle == 1) ? $angle = -1 : $angle = 1;
}



header("Content-type: image/jpeg"); 
imagejpeg($image);
imagedestroy($image);



?>