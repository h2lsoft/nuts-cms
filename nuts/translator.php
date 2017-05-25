<?php
/**
 * Translator bridge
 * your website must be registered to allow translation
 */

header('content-type:text/plain; charset=utf-8');

$lngIn = @$_POST['lngIn'];
$lngout = @$_POST['lngOut'];
$txt = @$_POST['txt'];
$serverName = @$_SERVER['SERVER_NAME'];
$website_uri = @WEBSITE_URL;


$uri = "http://www.nuts-cms.com/tools/translator/?";
$uri .= "lngIn=".$lngIn;
$uri .= "&lngOut=".$lngout;
$uri .= "&txt=".urlencode($txt);
$uri .= "&serverName=".$serverName;
$uri .= "&website_uri=".$website_uri;


if(!($content = file_get_contents($uri)))
{
	die("Error: service not available (impossible to get contents)");
}
else
{
	die($content);
}


