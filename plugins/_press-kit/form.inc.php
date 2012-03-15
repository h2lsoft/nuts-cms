<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsPressKit'));

// fields
$plugin->formAddFieldDate('Date', $lang_msg[1], true);
$plugin->formAddFieldText('Title', $lang_msg[2], true);
$plugin->formAddFieldTextAjaxAutoComplete('Source', $lang_msg[3], true);
$plugin->formAddFieldFileBrowser('File', $lang_msg[4], true, "nuts_press_kit");



?>