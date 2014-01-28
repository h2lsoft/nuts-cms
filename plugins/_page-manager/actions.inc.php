<?php

/* var @nuts NutsCore */
$_GET['ID'] = (int)@$_GET['ID'];
$_GET['zoneID'] = (int)@$_GET['zoneID'];

include_once(NUTS_PLUGINS_PATH.'/_page-manager/func.inc.php');

// front office exception
if(@$_GET['from'] == 'iframe' && @in_array($_GET['from_action'], array('add_page', 'add_sub_page')))
	$_GET['_action'] = 'add_page';

$ajax_action_file = NUTS_PLUGINS_PATH.'/_page-manager/ajax/'.@$_GET['_action'].'.inc.php';
if(!@empty($_GET['_action']))
{
	if(!file_exists($ajax_action_file))die("Error: action `{$_GET['_action']}` not found");
	include($ajax_action_file);
	exit(1);
}


