<?php
/**
 * Plugin page-content-view-fields - action List
 * 
 * @version 1.0
 * @date 20/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable("NutsPageContentViewField", "", "NutsPageContentViewID={$_GET['NutsPageContentViewID']}", "ORDER BY Position");

// search engine
// $plugin->listSearchAddFieldSelectSql('NutsPageContentViewID', 'View');


// create fields

// add list position
$plugin->listAddColPosition('Position', 'NutsPageContentViewID', $_GET['NutsPageContentViewID']);

$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', '', '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Label', '', '; white-space:nowrap;', false);
$plugin->listAddCol('Type', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddColImg('HrAfter', 'Hr after');


$plugin->listAddButtonUrlAdd("NutsPageContentViewID={$_GET['NutsPageContentViewID']}");


// render list
$plugin->listCopyButton = false;
$plugin->listRender(0, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

    $row['Position'] = $plugin->listGetPositionContents($row['ID']);

	return $row;
}



?>