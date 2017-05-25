<?php

// assign table to db
$plugin->listSetDbTable('NutsRichEditor');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Content', $lang_msg[1], '', false);

// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	$row['Content'] = nl2br($row['Content']);

	return $row;
}


