<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsPressKit'));

// fields
$plugin->formAddFieldDate('Date', $lang_msg[1], true);
$plugin->formAddFieldTextAjaxAutoComplete('Category', $lang_msg[5], true);
$plugin->formAddFieldText('Title', $lang_msg[2], true, 'ucfirst');
$plugin->formAddFieldTextAjaxAutoComplete('Source', $lang_msg[3], true);
$plugin->formAddFieldFileBrowser('File', $lang_msg[4], true, "nuts_press_kit");


if($_POST)
{
	$_POST['Category'] = ucfirst($_POST['Category']);
	$_POST['Source'] = ucfirst($_POST['Source']);
}



?>