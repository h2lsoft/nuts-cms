<?php
/**
 * Plugin useful_links - action List
 *
 * @version 1.0
 * @date 29/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/config.inc.php');

// assign table to db
$plugin->listSetDbTable("NutsUsefulLinks", "", "", "ORDER BY Position");

// search engine

// create fields
// $plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddColPosition('Position');
$plugin->listAddCol('Logo', ' ', 'center; width:40px;', false);
$plugin->listAddCol('Name', $lang_msg[1], '', false);
$plugin->listAddColImg('Visible', $lang_msg[4]);



// render list
$plugin->listRender(50, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

    $row['Position'] = $plugin->listGetPositionContents($row['ID']);
    $row['Logo'] = '<img src="/uploads/_useful-links/thumb_'.$row['LogoImage'].'?t='.time().'" style="height:60px;" class="image_preview" />';
    $row['Name'] = "<strong>{$row['Name']}</strong><br><a href='{$row['Url']}' target='_blank'>{$row['Url']}</a>";
	
	return $row;
}



