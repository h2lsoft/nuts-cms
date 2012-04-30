<?php

$for = 'HOME';
include(WEBSITE_PATH.'/nuts/_inc/trt_menu.inc.php');
// $plugin->trace();

// Maintenance **********************************************************************

// purge logs
include(WEBSITE_PATH."/plugins/_logs/config.inc.php");
if($cf_purge_days > 0)
{
	$gmtdate = nutsGetGMTDate();
	$sql = "DELETE FROM NutsLog WHERE DATE_ADD(DateGMT, INTERVAL $cf_purge_days DAY) <= '$gmtdate' ";
	$nuts->doQuery($sql);
}


$img_error = '<img src="img/icon-error.gif" align="absbottom" />';
$img_warning = '<img src="img/icon-tag-moderator.png" align="absbottom" />';

// right manager errors
if(in_array('_right-manager', $plugins_allowed))
{
    // plugin are not installed ? **********************************************
    $r = $plugin->getData("SELECT
									ID
							FROM
									NutsMenu
							WHERE
									Category = '' OR
									ISNULL(Category)
							LIMIT
									1");
    if(count($r) > 0)
        $menu = '<div id="home_updater">'.$img_error.' You have some plugins not installed correctly, please launch plugins module and after right manager module.</div>'.$menu;
}

// website maintenance *********************************************************
if(WEBSITE_MAINTENANCE && in_array('_control-center', $plugins_allowed))
{
    $menu = '<div id="home_updater">'.$img_warning.' Your website is in maintenance, please <a href="javascript:system_goto(\'index.php?mod=_control-center&do=exec\',\'content\');">run control center module</a>.</div>'.$menu;
}


// notification
$notifications = array();
foreach($plugins_allowed as $plugin_allowed)
{
    if(file_exists(NUTS_PLUGINS_PATH."/$plugin_allowed/notification.inc.php"))
        include_once(NUTS_PLUGINS_PATH."/$plugin_allowed/notification.inc.php");
}

if(count($notifications) > 0)
{
    $tmp = "<script>\n";
    foreach($notifications as $notification => $count)
        $tmp .= "pluginAddNotificationCounter('$notification', '$count');\n";
    $tmp .= "</script>\n";
}


$plugin->render = $menu;


?>