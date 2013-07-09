<?php
/**
 * Plugin gmaps-poi - action List
 * 
 * @version 1.0
 * @date 09/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->listSetDbTable('NutsGMapsPOI', "NutsGMapsID = {$_GET['NutsGMapsID']}");

$plugin->listSearchAddFieldTextAjaxAutoComplete('Title', $lang_msg[1]);
$plugin->listSearchAddFieldText('Address', $lang_msg[2]);
$plugin->listSearchAddFieldTextAjaxAutoComplete('ZipCode', $lang_msg[3]);
$plugin->listSearchAddFieldTextAjaxAutoComplete('City', $lang_msg[4]);
$plugin->listSearchAddFieldTextAjaxAutoComplete('Country', $lang_msg[5]);

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Title', $lang_msg[1], '; white-space:nowrap;', true);
$plugin->listAddCol('Address', $lang_msg[2], '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('ZipCode', $lang_msg[3], 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('City', $lang_msg[4], 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Country', $lang_msg[5], 'center; width:30px; white-space:nowrap;', true);


// render list
$plugin->listAddButtonUrlAdd("NutsGMapsID={$_GET['NutsGMapsID']}");
$plugin->listRender(100, 'hookData');


function hookData($row)
{
	global $nuts, $plugin, $lang_msg;



	
	return $row;
}


?>