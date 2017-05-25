<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsUser'));

// fields
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
$plugin->formAddFieldText('Login', $lang_msg[5], false, 'lower', 'width:10em', '', 'maxlength="15" disabled');

if($profile_enable_password_change)
{
    $plugin->formAddFieldText('Password', $lang_msg[6], 'notEmpty|minLength,5', '', 'width:10em', '', 'maxlength="15" ');
}
else
{
    $plugin->formAddFieldText('Password', $lang_msg[6], false, '', 'width:10em', '', 'maxlength="15" disabled');
    $plugin->formAddException('Password');
}



$plugin->formAddFieldsetEnd();
// end of fieldset


// fieldset
$plugin->formAddFieldsetStart('Avatar Image');
$inputs = <<<EOF
 &nbsp;&nbsp; <b>{$lang_msg[27]} :</b>
    <input type="button" id="AvatarImage" value="Image..." class="button" />
    <input type="button" id="AvatarGravatar" value="Gravatar" class="button" />
EOF;

$plugin->formAddFieldText('Avatar', '<div class="thumb_preview"><img id="avatar_image" /></div>', false, '', 'width:300px', $inputs, '');
$plugin->formAddFieldsetStart('AvatarImageTmp');
$plugin->formAddFieldImage('AvatarTmp', '', false, NUTS_IMAGES_PATH.'/avatar', NUTS_IMAGES_URL.'/avatar',
                                                                                                            '1Mo',
                                                                                                            '',
                                                                                                            '',
                                                                                                            '',
                                                                                                            '',
                                                                                                            true,
                                                                                                            60,
                                                                                                            60,
                                                                                                            true,
                                                                                                            array(255,255,255),
                                                                                                            false);

$plugin->formAddFieldsetEnd();

$plugin->formAddFieldsetEnd();
// end of fieldset


$plugin->formAddException('Login');
$plugin->formAddException('AvatarFile');



include_once(PLUGIN_PATH.'/form_custom_fields.inc.php');


// options
if($_GET['ID'] && $profile_front_office_toolbar_fieldset)
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



if(!$_POST)
{
    $plugin->formAddEndText("
        <script>var lang_msg_11 = '{$lang_msg[11]}';</script>
        <script type=\"text/javascript\" src=\"/plugins/_user-profile/funcs.js\"></script>
        <script type=\"text/javascript\" src=\"/plugins/_user-profile/form_custom.js\"></script>
    ");

}
else
{
    if($profile_enable_password_change)
        $nuts->alphaNumeric('Password', '_-');
}

