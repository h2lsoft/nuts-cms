<?php
/**
 * Plugin user-shortcuts - action List
 *
 * @version 1.0
 * @date 18/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsUserShortcut', "", "NutsUserID = {$_SESSION['NutsUserID']}", "ORDER BY Position");

// create fields
$plugin->listAddColPosition('Position', 'NutsUserID', $_SESSION['NutsUserID']);
$plugin->listAddCol('Plugin', '', '', true);


// render list
$plugin->listCopyButton = false;
$plugin->listRender(0, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

    $row['Position'] = $plugin->listGetPositionContents($row['ID']);




    // get plugin name translated
    $plugin_folder_name = $row['Plugin'];
    include(Plugin::getIncludeUserLanguagePath($plugin_folder_name));

    $row['Plugin'] = <<<EOF
        <img src="/plugins/{$plugin_folder_name}/icon.png" style="width:32px;" align="absmiddle" /> {$lang_msg[0]} ($plugin_folder_name)
EOF;






	return $row;
}


