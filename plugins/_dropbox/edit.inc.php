<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/form.inc.php');

$plugin->formInitAdd(array('NutsGroup' => array(1, 6)));

$row = $plugin->formInit();

$tmp_str = $row['GroupAllowed'];
$tmp_str = str_replace('[', '', $tmp_str);
$tmp_arr = explode(']', $tmp_str);
$tmp_arr = array_map('trim', $tmp_arr);
$row['NutsGroup'] = $tmp_arr;

$plugin->formInitSetRow($row);


if($plugin->formValid())
{
	$CUR_ID = $plugin->formUpdate();
}

