<?php
/**
 * Plugin Visual query builder - action Exec
 * 
 * @version 1.0
 * @date 02/12/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// ajax ****************************************************************************************************************
if(ajaxerRequested())
{
	if(ajaxerAction(array('query_preview', 'format_sql', 'get_fields')))
	{
		include(PLUGIN_PATH."/ajax/{$_GET['_action']}.inc.php");
		die();
	}
}


// execution ***********************************************************************************************************
$nuts->open(PLUGIN_PATH.'/exec.html');

// list table
$sql = "SHOW FULL TABLES";
$nuts->doQuery($sql);
while($row = $nuts->dbFetch())
{
    $keys = array_keys($row);

    $table_type = strtolower($row[$keys[1]]);
    $table_type = str_replace('base ', '', $table_type);

    $nuts->parse('tables.table_name', $row[$keys[0]]);
    $nuts->parse('tables.table_type', $table_type);
    $nuts->loop('tables');
}




$plugin->render = $nuts->output();



?>