<?php

/* @var $nuts NutsCore */
/* @var $plugin Plugin */

// assign table to db
$plugin->listSetDbTable('NutsPressKit');

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldDate('Date');
$plugin->listSearchAddFieldSelectSql('Source', $lang_msg[3]);
$plugin->listSearchAddFieldText('Title', $lang_msg[2]);


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true); // with order by
$plugin->listAddCol('Source', $lang_msg[3], '; width:50px; white-space:nowrap;', true);
$plugin->listAddCol('Title', $lang_msg[2], '', true);
$plugin->listAddCol('File', $lang_msg[4], 'center; width:50px; white-space:nowrap;');

// render list
$plugin->listRender(20, 'hookdata');

function hookData($row)
{
	global $nuts;

	// file
	$image = getImageExtension($row['File']);
	$dl = '<a title="[FILE]" class="tt"  href="[FILE]" target="_blank">'.$image.'</a>';
	$row['File'] = str_replace('[FILE]', $row['File'], $dl);
		
	return $row;
}


?>