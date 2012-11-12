<?php
/**
 * Plugin edm-locks - action List
 * 
 * @version 1.0
 * @date 01/08/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsEDMLock', "(SELECT Login FROM NutsUser WHERE ID = NutsUserID) AS Login");

// search engine
$plugin->listSearchAddFieldDate('Date');
$plugin->listSearchAddFieldTextAjaxAutoComplete('NutsUserID', 'Login', 'begins', 'Login', 'ID', 'NutsUser');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Folder');
$plugin->listSearchAddFieldTextAjaxAutoComplete('File');



// create fields
$plugin->listAddCol('ID', '', 'center; width:30px;', true);
$plugin->listAddCol('Date', '', 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Login', '', 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Folder', '', '', false);
$plugin->listAddCol('File', '', '', false);

// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;
	
	
	
	return $row;
}



?>