<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->listSetDbTable('NutsUrlRewriting', '', "", "ORDER BY Position");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Type');


// create fields
$plugin->listAddColPosition('Position');
$plugin->listAddCol('ID', '', 'center; width:30px', false); // with order by
$plugin->listAddCol('Type', '', 'center; width:30px', false); // with order by
$plugin->listAddCol('Pattern', '', '', false); // with order by
$plugin->listAddCol('Replacement', '', '', false);


// render list
$plugin->listRender(50, 'hookData');

function hookData($row)
{
	global $plugin;

	$row['Position'] = $plugin->listGetPositionContents($row['ID']);
	return $row;
}



?>