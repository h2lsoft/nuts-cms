<?php
/**
 * Use this script to easily includes headers in your job files (like cron task)
 */
include(WEBSITE_PATH.'/nuts/config_auto.inc.php');
include(NUTS_PATH.'/_inc/func.inc.php');
include(NUTS_PATH.'/_inc/custom.inc.php');

include_once(NUTS_PATH.'/_inc/Utils.inc.php');
include_once(NUTS_PATH.'/_inc/Query.class.php');
include_once(NUTS_PATH.'/_inc/NutsORM.class.php');

include(NUTS_PHP_PATH.'/TPLN/TPLN.php');
include(NUTS_PATH.'/_inc/NutsCore.class.php');
include(NUTS_PHP_PATH.'/spyc/spyc.php');
include(NUTS_PHP_PATH.'/FirePHPCore/fb.php');

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


?>