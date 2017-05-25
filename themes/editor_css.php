<?php

header("content-type: text/css");


include('../nuts/config.inc.php');
include('../nuts/config_auto.inc.php');

if(!isset($_GET['t']) ||  empty($_GET['t']) || in_array($_GET['t'], array('.', '..')) || !file_exists($_GET['t'].'/style.css'))
	die("");

$style_path = $_GET['t'].'/style.css';
$out = file_get_contents($style_path);

$s = preg_quote("/*** rte start ***/");
$e = preg_quote("/*** rte end ***/");

preg_match("#$s(.*)$e#msU", $out, $matches);

if(count($matches) == 2)
{
	$out = $matches[1];
	$s = preg_quote("/*** rte start remove ***/");
	$e = preg_quote("/*** rte end remove ***/");

	$out = preg_replace("#$s(.*)$e#msU", '', $out);

	// protect url and absolute link
	$out = str_replace('url (', 'url(', $out);
	$out = str_replace('url(/', 'url (/', $out);
	$out = str_replace('url(http://', 'url (http://', $out);
	$out = str_replace('url(https://', 'url (https://', $out);
	$out = str_replace('url(', 'url('.$_GET['t'].'/', $out);

	// unprotect
	$out = str_replace('url (', 'url(', $out);


	// add embed code preview
	$out .= "\n\nobject {border:1px dashed #ffcc00; width:150px; height:100px; display: block; background-image: url(/nuts/img/no-preview-flash.png); background-repeat: no-repeat; background-align: center center;}\n\n";
	$out .= "img.nuts_tags:hover {}\n";
	$out .= "td, th {border:1px dashed #ccc;}\n";

	$out .= ".nuts_tags {border:2px solid #1570B8; border-radius:3px; margin:10px 5px; }\n";
	$out .= ".nuts_tags:hover, .nuts_tags:active {border:2px solid #666; cursor:move!important;}\n";
	$out .= ".nuts_tags:hover {opacity:0.8;}\n";

	echo trim($out);

}



