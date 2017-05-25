<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');

// sql table
$plugin->formDBTable(array('NutsEmail'));

$langs = nutsGetOptionsLanguages();

$plugin->formAddFieldSelect('Language', $lang_msg[1], true, $langs);
$plugin->formAddFieldText('Expeditor', $lang_msg[4], false, "", "", "", "", $lang_msg[6]);
$plugin->formAddFieldTextAjaxAutoComplete('GroupName', $lang_msg[7], false);
$plugin->formAddFieldTextArea('Description', $lang_msg[8], false);
$plugin->formAddFieldText('Subject', $lang_msg[2], true, 'ucfirst');
$plugin->formAddFieldHtmlArea('Body', $lang_msg[3], true, 'height:300px;', $lang_msg[5]);

