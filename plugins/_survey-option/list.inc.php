<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

/* @var $plugin Plugin */
$multiLanguage = isWebsiteMultiLang();

// assign table to db
$plugin->listSetDbTable("NutsSurveyOption", "", "AND NutsSurveyID={$_GET['NutsSurveyID']} ORDER BY Position");

// create fields

// add list position
$plugin->listAddColPosition('Position', 'NutsSurveyID', $_GET['NutsSurveyID']);
$plugin->listAddCol('Title', $lang_msg[1], '; white-space:nowrap;', false);


if($multiLanguage)
	$plugin->listAddColImg('I18N', "", "", false);

$plugin->listAddButtonUrlAdd("NutsSurveyID={$_GET['NutsSurveyID']}");

// render list
$plugin->listCopyButton = false;

$plugin->listRender(0, 'hookData');

function hookData($row)
{	
	global $plugin;

	$row['Position'] = $plugin->listGetPositionContents($row['ID']);

	return $row;
}



?>