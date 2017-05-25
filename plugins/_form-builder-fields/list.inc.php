<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

$multiLanguage = isWebsiteMultiLang();

$plugin->mainTitleAddUrl("&NutsFormID={$_GET['NutsFormID']}");


// assign table to db
$plugin->listSetDbTable("NutsFormField", "", "NutsFormID={$_GET['NutsFormID']}", "ORDER BY Position");

// create fields

// add list position
$plugin->listAddColPosition('Position', 'NutsFormID', $_GET['NutsFormID']);

$plugin->listAddCol('Label', $lang_msg[3], ';', false);
$plugin->listAddCol('Name', $lang_msg[2], '; white-space:nowrap;', false);
$plugin->listAddCol('Type', $lang_msg[4], 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddColImg('Required', $lang_msg[5]);
$plugin->listAddColImg('Email', "Email", "", false);

if($multiLanguage)$plugin->listAddColImg('I18N', "I18N", "", false);

$plugin->listAddButtonUrlAdd("NutsFormID={$_GET['NutsFormID']}");


$plugin->listCopyButton = false;

// render list
$plugin->listRender(0, 'hookData');


function hookData($row)
{
	global $plugin;

	$row['Position'] = $plugin->listGetPositionContents($row['ID']);

	if($row['Type'] == 'SECTION')
		$row['Label'] = "<span style=\"padding:2px; background-color:orange; border:1px solid orange; border-radius:5px; -moz-border-radius:5px;\"><b>{$row['Label']}</b></span>";

	return $row;
}

