<?php

header('content-type:image/png');

// controller *************************************************************************
if(!isset($_GET['tag']) || !file_exists("./{$_GET['tag']}.png"))
{
	readfile('unknown.png');
}

if(!isset($_GET['label']))$_GET['label'] = '';
// execution *************************************************************************
if(empty($_GET['label']))
{
	readfile("{$_GET['tag']}.png");
}
else
{

	$text_indent = 130;
	if($_GET['tag'] == 'form')$text_indent = 110;
	elseif($_GET['tag'] == 'media')$text_indent = 115;
	elseif($_GET['tag'] == 'zone')$text_indent = 110;
	elseif($_GET['tag'] == 'region')$text_indent = 125;
	elseif($_GET['tag'] == 'block')$text_indent = 120;
	elseif($_GET['tag'] == 'plugin')$text_indent = 120;
	elseif($_GET['tag'] == 'survey')$text_indent = 125;

	// create a new image and add label text
	$string = utf8_decode(base64_decode($_GET['label']));
	$string = " $string ";

	$font = 3;
	$width = (imagefontwidth($font) * strlen($string)) + $text_indent;
	$height = 24;

	$im = imagecreate($width, $height);
	$background_color = imagecolorallocate ($im, 255, 255, 255);

	$text_color = imagecolorallocate($im, 141, 17, 122);
	imagestring($im, $font, $text_indent, 5,  $string, $text_color);

	$src = imagecreatefrompng("{$_GET['tag']}.png");
	imagecopy($im, $src, 0, 0, 0, 0, $text_indent, 24);

	imagepng($im);
	imagedestroy($im);

}










?>