<?php

// includes ******************************************************************************
include('nuts/config.inc.php');
include(WEBSITE_PATH.'/nuts/config_auto.inc.php');

$auto_include_paths = array('_funcs/generics/*.php', '_funcs/*.php', '_components/*.php', );
foreach($auto_include_paths as $auto_include_path)
{
	// includes functions
	$functions = glob(WEBSITE_PATH.'/nuts/'.$auto_include_path);
	if(is_array($functions))
	{
		foreach($functions as $function)
			include_once($function);
	}
}


include(NUTS_PHP_PATH.'/TPLN/TPLN.php');
include(NUTS_PHP_PATH.'/FirePHPCore/fb.php');
include('nuts/_inc/NutsCore.class.php');
include('nuts/_inc/Page.class.php');

FB::setEnabled(FirePHP_enabled);


// auto include files
$scripts = glob('x_includes/*.php');
if(is_array($scripts)){
	foreach($scripts as $scr)
		include_once($scr);
}

// auto starts session ?
if(NUTS_WWW_SESSION_INIT == true)
{
    @session_start();
}

// auto include specific files
$scripts = glob('x_includes/www/*.php');
if(is_array($scripts)){
	foreach($scripts as $scr)
		include_once($scr);
}


// templates *****************************************************************************
$timer = time();
$page = new Page();
	$nuts = &$page; // useful to use nuts variable instead of page
	$plugin = &$page; // useful to use nuts variable instead of page

// XSS protection
$_GET = $page->xssProtect($_GET);
$_POST = $page->xssProtect($_POST);

$page->write();