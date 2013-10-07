<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

$plugin->mainTitleAddUrl("&NutsGalleryID={$_GET['NutsGalleryID']}");

// assign table to db
$plugin->listSetDbTable('NutsGalleryImage', "", "NutsGalleryID = {$_GET['NutsGalleryID']}", "ORDER BY Position");


// create fields

// add list position
if(@(int)$_GET['NutsGalleryID'] != 0)
	$plugin->listAddColPosition('Position', 'NutsGalleryID', $_GET['NutsGalleryID']);

$plugin->listAddCol('Thumbnail', '&nbsp;', 'center; width:30px', false);
$plugin->listAddCol('Legend', $lang_msg[2], '', false); // with order by
$plugin->listAddColImg('Active', $lang_msg[4], '', false);

// render list
$plugin->listAddButtonUrlAdd("NutsGalleryID={$_GET['NutsGalleryID']}");
$plugin->listCopyButton = false;
$plugin->listRender(50, 'hookData');


function hookData($row)
{
	global $plugin;

	$row['Position'] = $plugin->listGetPositionContents($row['ID']);
	$row['Legende'] = "<b>{$row['Legend']}</b><br />{$row['Description']}";


	$row['Thumbnail'] = '';
	if(!empty($row['MainImage']))
	{
		$ext = explode('.', $row['MainImage']);
		$ext = $ext[count($ext) - 1];
		$row['Thumbnail'] = '<img class="image_preview"  style="max-height:80px;" src="'.NUTS_IMAGES_URL.'/gallery_images/thumb_'.$row['ID'].'.'.$ext.'?t='.time().'" />';
	}

	return $row;
}



?>