<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/trt_ajax.inc.php');

// assign table to db
$plugin->listSetDbTable('NutsEDMGroup', "(SELECT COUNT(*) FROM NutsEDMGroupUser WHERE Deleted = 'NO' AND NutsEDMGroupID = NutsEDMGroup.ID) AS NbUsers");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Name', $lang_msg[1]);


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true, array());
$plugin->listAddCol('Name', $lang_msg[1], ';width:30px; white-space:nowrap;', true, array());
$plugin->listAddCol('Description', '', '', false, array());
$plugin->listAddCol('NbUsers', $lang_msg[2], 'center; width:30px', false, array());


// render list
$plugin->listCopyButton = false;
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $lang_msg;



	return $row;
}



?>