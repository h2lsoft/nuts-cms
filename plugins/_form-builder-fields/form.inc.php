<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
$multiLanguage = isWebsiteMultiLang();

$plugin->formDBTable(array('NutsFormField'));

if($_GET['ID'] == 0)
	$plugin->formActionAddParameter("NutsFormID={$_GET['NutsFormID']}");

// fields
$plugin->formAddFieldSelect('Type', $lang_msg[4], true, array('text', 'textarea', 'checkbox', 'radio', 'select', 'select-multiple', 'password', 'php', 'file', 'section', 'html'));
$plugin->formAddFieldText('Name', $lang_msg[2], 'alphaNumeric');
$plugin->formAddFieldText('Label', $lang_msg[3], false);
$plugin->formAddFieldText('Attributes', $lang_msg[6], false, "", "", "", "", $lang_msg[7]);
$plugin->formAddFieldTextArea('Value', $lang_msg[10], false, "", 'height:100px', "", $lang_msg[11]);
$plugin->formAddFieldTextArea('PhpCode', 'Php code', false, "php", 'height:100px', "", $lang_msg[12]);
$plugin->formAddFieldBooleanX('Required', $lang_msg[5], false);
$plugin->formAddFieldBooleanX('Email', '', false);

// special for file
$plugin->formAddFieldText('FilePath', $lang_msg[13], false, "", "", "", "", $lang_msg[14]);
$plugin->formAddFieldText('FileAllowedExtensions', $lang_msg[15], false, "", "", "", "", $lang_msg[16]);
$plugin->formAddFieldText('FileAllowedMimes', $lang_msg[17], false, "", "", "", "", $lang_msg[18]);
$plugin->formAddFieldText('FileMaxSize', $lang_msg[19], false, "", "", "", "", $lang_msg[20]);

if($multiLanguage)
	$plugin->formAddFieldBooleanX('I18N', '', false);

$plugin->formAddFieldText('TextAfter', $lang_msg[21], false, '', '', '', '', $lang_msg[22]);
$plugin->formAddFieldTextArea('OtherValidation', $lang_msg[8], false, "", 'height:100px', '', $lang_msg[9]);
$plugin->formAddFieldTextArea('HtmlCode', 'Html', false, false, "", 'height:100px', '', $lang_msg[23]);


if($_GET['ID'] != 0)
{
	$plugin->formAddFieldHidden('NutsFormID', '', true);
	// $plugin->formAddField('Position', "", 'text', 'notEmpty|onlyDigit', array('style' => 'width:50px; text-align:center'));
}

if($_POST)
{
	// form assignation
	if($_GET['ID'] == 0)
	{
		// get max position
		$_POST['NutsFormID'] = $_GET['NutsFormID'];
		$_POST['Position'] = $plugin->formGetMaxPosition('Position', 'NutsFormID', $_POST['NutsFormID']);
		$_POST['Position'] += 1;
	}

}



?>