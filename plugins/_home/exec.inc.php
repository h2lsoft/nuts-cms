<?php

$for = 'HOME';
$allow_notify = true;
$dashboard_notifications = array();
$dashboard_widgets = array();
include(WEBSITE_PATH.'/nuts/_inc/trt_menu.inc.php');

// maintenance *********************************************************************************************************
include(WEBSITE_PATH."/plugins/_logs/config.inc.php");
if($cf_purge_days > 0)
{
    $gmtdate = nutsGetGMTDate();
    $sql = "DELETE FROM NutsLog WHERE DATE_ADD(DateGMT, INTERVAL $cf_purge_days DAY) <= '$gmtdate' ";
    $nuts->doQuery($sql);
}

// website maintenance *************************************************************************************************
if(WEBSITE_MAINTENANCE && nutsUserHasRight('', '_control-center', 'exec'))
{
    $msg = "Your website is in maintenance, please run <a href=\"javascript:system_goto('index.php?mod=_control-center&do=exec', 'content');\">Control center</a>";
    if($_SESSION['Language'] == 'fr')
    {
        $msg = "Votre site est en maintenance, merci d'executer le module <a href=\"javascript:system_goto('index.php?mod=_control-center&do=exec', 'content');\">Centre de contrôle</a>";
    }

    Plugin::dashboardAddNotification('warning', $msg);
}

// notification available **********************************************************************************************
foreach($plugins_allowed as $plugin_allowed)
{
    if(file_exists(NUTS_PLUGINS_PATH."/$plugin_allowed/notification.inc.php"))
        include_once(NUTS_PLUGINS_PATH."/$plugin_allowed/notification.inc.php");
}

// widget available **********************************************************************************************
$sql = "SELECT
                DISTINCT Name
        FROM
                NutsMenu
        WHERE
                Deleted = 'NO' AND
                ID IN(SELECT DISTINCT NutsMenuID FROM NutsMenuRight WHERE NutsGroupID = {$_SESSION['NutsGroupID']})
        ORDER BY
                Name";
$nuts->doQuery($sql);
$all_plugins_allowed = $nuts->dbGetOneData();

foreach($all_plugins_allowed as $plugin_allowed)
{
    if(file_exists(NUTS_PLUGINS_PATH."/$plugin_allowed/widget.inc.php"))
        include_once(NUTS_PLUGINS_PATH."/$plugin_allowed/widget.inc.php");
}

// execution ***********************************************************************************************************
$nuts->open(PLUGIN_PATH.'/home.html');

// notifications
if(count($dashboard_notifications) == 0)
{
    $nuts->eraseBloc('notification');
}
else
{
    foreach($dashboard_notifications as $n)
    {
        $nuts->parse('notification.type', $n['type'], '|strtolower');
        $nuts->parse('notification.message', $n['message']);
        $nuts->loop('notification');
    }
}

// widgets
if(count($dashboard_widgets) == 0)
{
    $nuts->eraseBloc('widget');
}
else
{
    $widget_priorities = array('high', 'medium', 'low', 'final');
    foreach($widget_priorities as $widget_priority)
    {
        $cur_cols_style_index = 0;

        if(!isset($dashboard_widgets[$widget_priority]))$dashboard_widgets[$widget_priority] = array();
        foreach($dashboard_widgets[$widget_priority] as $cur_widget)
        {
            if($cur_widget['rows'] == '3-rows')
            {
                if($cur_cols_style_index == 3)
                    $cur_cols_style_index = 0;
                else
                    $cur_cols_style_index++;

                $cur_widget['rows'] .= ' dashboard-widget-row-3-rows-'.$cur_cols_style_index;
            }
            else
            {
                $cur_cols_style = '';
            }



            $nuts->parse('widget.id', $cur_widget['id']);
            $nuts->parse('widget.rows', $cur_widget['rows']);
            $nuts->parse('widget.style', $cur_widget['style']);
            $nuts->parse('widget.title', $cur_widget['title']);
            $nuts->parse('widget.content', $cur_widget['content']);
            $nuts->loop('widget');
        }

    }
}



$plugin->render = $nuts->output();



?>