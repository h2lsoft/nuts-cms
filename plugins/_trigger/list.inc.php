<?php
/**
 * Plugin trigger - action List
 *
 * @version 1.0
 * @date 19/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsTrigger');

// search engine
// $plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Name', $lang_msg[1]);

// create fields
// $plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', $lang_msg[1], '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Description', '', '', false);


// render list
$plugin->listSetFirstOrderBySort('ASC');
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;
	
	
	
	return $row;
}

