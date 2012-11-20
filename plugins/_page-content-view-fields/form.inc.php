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
$plugin->formAddFieldText('Name', "", "notEmpty|alphaNumeric", "ucfirst");
$plugin->formAddFieldText('Label', "", true, "ucfirst");

$opts = array('TEXT', 'TEXTAREA', 'HTMLAREA', 'DATE', 'DATETIME', 'SELECT', 'SELECT-SQL', 'BOOLEAN', 'BOOLEANX', 'FILEMANAGER', 'FILEMANAGER_IMAGE', 'FILEMANAGER_MEDIA', 'COLORPICKER');
$plugin->formAddFieldSelect('Type', "", true, $opts);

$help = "Option for your field :

 * select : must be your values each lines are an option put | to separate option and label (ex: OPTION|LABEL)
 * select-sql : must be the sql code with columns label and value
 * filemanager-image : can be the default folder opened
 * filemanager-media : can be the default folder opened

";
$plugin->formAddFieldTextArea('SpecialOption', "Special option", false, "tabby", "", "", nl2br($help));

$plugin->formAddFieldText('CssStyle', "Css style", false, "lower");
$plugin->formAddFieldText('Value', "Default value", false);
$plugin->formAddFieldText('Help', "Help message", false, "ucfirst");
$plugin->formAddFieldText('TextAfter', "Text after", false);
$plugin->formAddFieldBooleanX('HrAfter', "Hr after", true);



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


?>