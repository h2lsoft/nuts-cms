<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/config.inc.php');
include(PLUGIN_PATH.'/common.inc.php');

// sql table
$plugin->formDBTable(array('NutsUser'));

// fields
$plugin->formAddFieldSelectSql('NutsGroupID', $lang_msg[1], true, '', '', "ID IN(".join(',',$allowed_groups).")");
$civs = array('Mr', 'Mme', 'Mlle', 'Miss', 'Pr', 'Dr');
$plugin->formAddFieldSelect('Gender', $lang_msg[24], false, $civs, 'width:5em', '', 'maxlength="5"');
$plugin->formAddFieldText('LastName', $lang_msg[2], true, 'ucfirst', 'width:40em', '', 'maxlength="50"');
$plugin->formAddFieldText('FirstName', $lang_msg[3], true, 'ucfirst', 'width:40em', '', 'maxlength="50"');
$plugin->formAddFieldText('Email', $lang_msg[4], 'unique|notEmpty', 'lower email', 'width:40em', '', 'maxlength="50"');
$plugin->formAddFieldSelect('Language', $lang_msg[7], true, $nuts_lang_options);

foreach($nuts_timezone_options as $opt)
	$time_options[] = array('label' => $opt['label'], 'value' => $opt['value']);
$plugin->formAddFieldSelect('Timezone', $lang_msg[9], true, $time_options);

// fieldset
$plugin->formAddFieldsetStart('User identification', $lang_msg[10]);
$plugin->formAddFieldText('Login', $lang_msg[5], 'unique|notEmpty', 'lower', 'width:10em', '', 'maxlength="15"');
$plugin->formAddFieldText('Password', $lang_msg[6], 'notEmpty|minLength,5', '', 'width:10em', '', 'maxlength="15"');
$plugin->formAddFieldsetEnd();
// end of fieldset

// fieldset
$plugin->formAddFieldsetStart('Avatar image');

$inputs = <<<EOF
 &nbsp;&nbsp; <input type="button" id="AvatarFacebook" value="Facebook" class="button" /> <input type="button" id="AvatarTwitter" value="Twitter" class="button" /> <input type="button" id="AvatarGravatar" value="Gravatar" class="button" />
EOF;

$plugin->formAddFieldText('Avatar', '<img id="avatar_image" style="max-width: 60px; max-height: 60px; margin-right: 15px; border: 1px solid #ccc; margin-top: -10px;" />', false, '', 'width:400px', $inputs, '');
$plugin->formAddFieldsetEnd();
// end of fieldset


$plugin->formAddFieldBoolean('Active', $lang_msg[8], true);
$plugin->formAddFieldBooleanX('IdentificationEmail', $lang_msg[12], true, $lang_msg[14]);
$plugin->formAddException('IdentificationEmail');

// info
$plugin->formAddFieldsetStart('Information');
$plugin->formAddFieldTextAjaxAutoComplete('Company', $lang_msg[15], false, 'countains');
$plugin->formAddFieldText('NTVA', $lang_msg[25], false);
$plugin->formAddFieldText('Address', $lang_msg[16], false, 'ucfirst');
$plugin->formAddFieldText('Address2', $lang_msg[17], false, 'ucfirst');
$plugin->formAddFieldText('Address3', $lang_msg[26], false, 'ucfirst');
$plugin->formAddFieldText('ZipCode', $lang_msg[18], false, 'zip_code');
$plugin->formAddFieldTextAjaxAutoComplete('City', $lang_msg[19], false);
$plugin->formAddFieldTextAjaxAutoComplete('Country', $lang_msg[20], false);
$plugin->formAddFieldText('Phone', $lang_msg[21], false, 'phone');
$plugin->formAddFieldText('PhoneStandard', $lang_msg[27], false, 'phone');
$plugin->formAddFieldText('Gsm', $lang_msg[22], false, 'gsm');
$plugin->formAddFieldText('Fax', $lang_msg[23], false, 'fax');
$plugin->formAddFieldTextAjaxAutoComplete('Job', '', false);
$plugin->formAddFieldTextArea('Note', '', false);
$plugin->formAddFieldsetEnd();
// end of info


// options
if($_GET['ID'])
{
	$nuts->doQuery("SELECT ID FROM NutsGroup WHERE ID = {$_SESSION['NutsGroupID']} AND BackofficeAccess = 'YES' AND FrontofficeAccess = 'YES'");
	if($nuts->dbNumRows() == 1)
	{
		$plugin->formAddFieldsetStart('Option');
		$plugin->formAddFieldBoolean('FrontOfficeToolbar', 'FrontOffice toolbar', false);
		$plugin->formAddFieldsetEnd();
	}
}
// end of options

include(PLUGIN_PATH."/custom.inc.php");


if($_POST)
{
	$nuts->alphaNumeric('Login', '_');
	$nuts->alphaNumeric('Password', '_-');
}

$plugin->formAddEndText("<script>var lang_msg_11 = '{$lang_msg[11]}';</script>");



?>