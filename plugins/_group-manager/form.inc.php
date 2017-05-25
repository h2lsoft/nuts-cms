<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsGroup'));

// fields
//$plugin->formAddField('Logo', $lang_msg[6], 'image', false, array('path' => NUTS_IMAGES_PATH.'/logo', 'url' => NUTS_IMAGES_URL.'/logo', 'size' => '150ko'));
$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst', '', '', 'maxlength="50"');
$plugin->formAddFieldTextArea('Description', $lang_msg[2], false, 'ucfirst');

/*$options = array();
for($i=0; $i  < count($nuts_editor_options); $i++)
	$options[] = array('label' => $nuts_editor_options[$i], 'value' => $nuts_editor_options[$i]);
$plugin->formAddField('TinyMceConfig', $lang_msg[4], 'select', true, array('options' => $options));
*/

$plugin->formAddFieldBoolean('BackofficeAccess', $lang_msg[22], true, $lang_msg[24]);
$plugin->formAddFieldBoolean('FrontofficeAccess', $lang_msg[23], true, $lang_msg[25]);

// fieldset File Explorer
$plugin->formAddFieldsetStart('FileExplorer', $lang_msg[7]);
$plugin->formAddFieldBooleanX('AllowUpload', $lang_msg[8], true, $lang_msg[9]);
$plugin->formAddFieldBooleanX('AllowEdit', $lang_msg[10], true, $lang_msg[11]);
$plugin->formAddFieldBooleanX('AllowDelete', $lang_msg[12], true, $lang_msg[13]);
$plugin->formAddFieldBooleanX('AllowFolders', $lang_msg[14], true, $lang_msg[15]);
$plugin->formAddFieldsetEnd();
// end of fieldset


$plugin->formAddFieldText('Priority', $lang_msg[5], 'notEmpty|onlyDigit', '', "width:50px; text-align:center");

