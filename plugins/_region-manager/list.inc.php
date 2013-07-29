<?php

/* @var $nuts NutsCore */
/* @var $plugin Plugin */

// assign table to db
$plugin->listSetDbTable('NutsRegion');

// search
$plugin->listSearchAddFieldSelectSql('Category', $lang_msg[26]);
$plugin->listSearchAddFieldText('Name', $lang_msg[1]);


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true); // with order by
$plugin->listAddCol('Category', $lang_msg[26], '; width:50px; white-space:nowrap;', true);
$plugin->listAddCol('Name', $lang_msg[1], '; width:50px; white-space:nowrap;', true);
$plugin->listAddCol('Description', $lang_msg[2], '', true);
$plugin->listAddCol('Code', $lang_msg[3]);

// render list
$plugin->listRender(20, 'hookdata');

function hookData($row)
{
	global $nuts;

	$row['Code'] = sprintf("<pre>{@NUTS	TYPE='REGION'	NAME='%s'}</pre>", $row['Name']);

	return $row;
}


?>