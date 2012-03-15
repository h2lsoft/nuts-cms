<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table
$plugin->formDBTable(array('NutsZone'));

// fields
// $plugin->formAddField('Type', $lang_msg[1], 'select', true, array('options' => $nuts_zone_options));
$plugin->formAddFieldText('Name', $lang_msg[2], true, '', '', '', 'maxlength="50"');
$plugin->formAddFieldText('CssName', $lang_msg[3], 'notEmpty|alphaNumeric,_', '', '', '', 'maxlength="50"');
$plugin->formAddFieldTextArea('Description', $lang_msg[4], false);
$plugin->formAddFieldText('Url', '', false, '', '', '', '', $lang_msg[6]);
$plugin->formAddFieldBoolean('Navbar', $lang_msg[7], true, $lang_msg[8]);



?>