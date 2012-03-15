<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->setListDbTable('NutsEmail');

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Language', $lang_msg[1]);
$plugin->listSearchAddFieldSelectSql('GroupName', $lang_msg[7]);
$plugin->listSearchAddFieldText('Subject', $lang_msg[2]);

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true, array());
$plugin->listAddColImg('Language', $lang_msg[1], '', true, NUTS_IMAGES_URL.'/flag/{Language}.gif');
$plugin->listAddCol('GroupName', $lang_msg[7], ';width:30px', true, array());
$plugin->listAddCol('Description', $lang_msg[8], '', false, array());
$plugin->listAddCol('Expeditor', $lang_msg[4], 'center; width:30px', true, array());
$plugin->listAddCol('Subject', $lang_msg[2], '', false, array());


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $lang_msg;

	$row['GroupName'] = ucfirst(strtolower($row['GroupName']));

	if(empty($row['Expeditor']))$row['Expeditor'] = NUTS_EMAIL_NO_REPLY;

	return $row;
}



?>