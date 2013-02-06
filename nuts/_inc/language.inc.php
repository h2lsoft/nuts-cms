<?php

if(count($a) >= 2 && $a[1] == $tmp_lang)
{
	include_once('index.php');
}
else
{
	// 404 error
	include('nuts/config.inc.php');
	include('nuts/config_auto.inc.php');
	include('library/php/TPLN/TPLN.php');
	include('nuts/_inc/func.inc.php');
	include('nuts/_inc/Page.class.php');

	$page = new Page();
	$page->error404();
}


?>