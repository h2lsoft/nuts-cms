<?php

// includes ******************************************************************************
include('nuts/config.inc.php');
include('nuts/config_auto.inc.php');
include(NUTS_PHP_PATH.'/TPLN/TPLN.php');
include(NUTS_PHP_PATH.'/FirePHPCore/fb.php');
include('nuts/_inc/NutsCore.class.php');
include('nuts/_inc/func.inc.php');
include('nuts/_inc/Page.class.php');
include('nuts/_inc/Utils.inc.php');
include('nuts/_inc/NutsORM.class.php');
include('nuts/_inc/Query.class.php');
include('nuts/_inc/NutsCRUD.class.php');
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

?>