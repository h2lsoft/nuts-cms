<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsRteTemplate', "");

// create search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Name');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', '', '', true); // with order by
$plugin->listAddCol('Description', '', '', false);


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	
	return $row;
}



?>