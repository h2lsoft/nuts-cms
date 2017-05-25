<?php
/**
 * Plugin edm-logs - action List
 *
 * @version 1.0
 * @date 08/07/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsEDMLogs', "(SELECT Login FROM NutsUser WHERE ID = NutsUserID) AS Login");

// search engine
$plugin->listSearchAddFieldDatetime('Date');
$plugin->listSearchAddFieldDatetime('Date2', 'Date', 'Date');
$plugin->listSearchAddFieldTextAjaxAutoComplete('NutsUserID', 'Login', 'begins', 'Login', 'ID', 'NutsUser');
$plugin->listSearchAddFieldSelectSql('Action');
$plugin->listSearchAddFieldSelectSql('Object');
$plugin->listSearchAddFieldTextAjaxAutoComplete('ObjectName', 'Object Info');


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px;', true);
$plugin->listAddCol('Date', '', 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Login', '', 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Action', '', 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Object', '', 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('ObjectName', 'Object Info', '; width:200px;', false);
$plugin->listAddCol('Resume', '', '', false);

// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;
	
	
	
	return $row;
}
