<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// ajaxer **************************************************************************************************************
if(ajaxerRequested())
{
	if(ajaxerAction('get_rights'))
	{
		// die("ID = {$_GET['ID']}");
		ajaxerParameterRequired('ID', 'int');
		$rights = Query::factory()->select('Rights')->from('NutsZone')->whereID($_GET['ID'])->executeAndGetOne();
		die(json_encode(unserialize($rights)));
	}

	die();

}



// execution ***********************************************************************************************************

// assign table to db
$plugin->listSetDbTable('NutsZone');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true); // with order by
$plugin->listAddCol('Name', $lang_msg[2], '', true);
$plugin->listAddCol('CssName', $lang_msg[3]);
$plugin->listAddCol('Description', $lang_msg[4]);
$plugin->listAddColImg('Navbar', $lang_msg[7], '', true);

$plugin->listSetFirstOrderBySort('ASC');

// render list
$plugin->listRender(20);



?>