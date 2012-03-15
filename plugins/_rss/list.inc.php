<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->setListDbTable('NutsRss');


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);

$plugin->listAddCol('RssImage', ' ', 'center; width:30px;', false);
$plugin->listAddCol('RssTitle', $lang_msg[1], '', false);
$plugin->listAddCol('RssLimit', $lang_msg[3], 'center; width:30px;', false);
$plugin->listAddCol('View', ' ', 'center; width:30px;', false);



// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	$row['RssImage'] = '<img src="'.$row['RssImage'].'" style="width:150px;" />';
	$row['View'] = '<a href="/plugins/_rss/viewer.php?ID='.$row['ID'].'" target="_blank"><img src="/nuts/img/icon-preview.gif" /></a>';

	$row['RssTitle'] = '<b>'.$row['RssTitle'].'</b><br />'.$row['RssDescription'];

	return $row;
}



?>