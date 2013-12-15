<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsI18n');

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldText('Pattern');


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Pattern', '', '', true); // with order by


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	$row['Pattern'] = str_replace('<', '<', $row['Pattern']);
	$row['Pattern'] = str_replace('>', '>', $row['Pattern']);
	$row['Pattern'] = '<pre>'.$row['Pattern'].'</pre>';

	return $row;
}


