<?php
/**
 * Use this script to easily includes headers in your job files (like cron task)
 */
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
include(NUTS_PATH.'/_inc/NutsCore.class.php');
include(NUTS_PHP_PATH.'/spyc/spyc.php');
include(NUTS_PHP_PATH.'/FirePHPCore/fb.php');

// orm autoloader
include(WEBSITE_PATH.'/nuts/_inc/orm_autoloader.inc.php');

// dynamic auto include files
$scripts = glob(WEBSITE_PATH.'/x_includes/*.php');
if(is_array($scripts))
{
	foreach($scripts as $scr)
		include_once($scr);
}

// auto include specific files
$scripts = glob(WEBSITE_PATH.'/x_includes/nuts/*.php');
if(is_array($scripts))
{
	foreach($scripts as $scr)
		include_once($scr);
}

