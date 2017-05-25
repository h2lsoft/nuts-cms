<?php
/**
 * Plugin page-content-view-fields - Form layout
 *
 * @version 1.0
 * @date 20/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table
$plugin->formDBTable(array('NutsPageContentViewField'));

if($_GET['ID'] == 0)
	$plugin->formActionAddParameter("NutsPageContentViewID={$_GET['NutsPageContentViewID']}");


// fields
$plugin->formAddFieldText('Name', $lang_msg[1], "notEmpty|alphaNumeric", "ucfirst");
$plugin->formAddFieldText('Label', "", true, "ucfirst");

$opts = array('TEXT', 'TEXTAREA', 'HTMLAREA', 'DATE', 'DATETIME', 'SELECT', 'SELECT-SQL', 'BOOLEAN', 'BOOLEANX', 'FILEMANAGER', 'FILEMANAGER_IMAGE', 'FILEMANAGER_MEDIA', 'COLORPICKER');
$plugin->formAddFieldSelect('Type', "", true, $opts);
$plugin->formAddFieldTextArea('SpecialOption', $lang_msg[7], false, "tabby", "", "", nl2br($lang_msg[3]));

$plugin->formAddFieldText('CssStyle', "Css style", false, "lower");
$plugin->formAddFieldText('Value', $lang_msg[4], false);
$plugin->formAddFieldText('Help', $lang_msg[5], false, "ucfirst");
$plugin->formAddFieldText('TextAfter', $lang_msg[6], false);
$plugin->formAddFieldBooleanX('HrAfter', $lang_msg[2], true);



if($_GET['ID'] != 0)
{
	$plugin->formAddFieldHidden('NutsPageContentViewID', '', true);
}

if($_POST)
{
	// form assignation
	if($_GET['ID'] == 0)
	{
		// get max position
		$_POST['NutsPageContentViewID'] = $_GET['NutsPageContentViewID'];
		$_POST['Position'] = $plugin->formGetMaxPosition('Position', 'NutsPageContentViewID', $_POST['NutsPageContentViewID']);
		$_POST['Position'] += 1;
	}

}

