<?php

include(PLUGIN_PATH.'/config.inc.php');
$hidden_fields_arr = explode(',', str_replace(' ', '', $hidden_fields));

$plugin->viewDbTable(array('NutsNews'));
$plugin->viewAddSQLField("(SELECT CONCAT(Lastname,' ',Firstname) FROM NutsUser WHERE ID = NutsUserID) AS NutsUser");


$plugin->viewAddVar('NewsImage', 'Image');

$plugin->viewAddVar('NutsUser', $lang_msg[16]);
$plugin->viewAddVar('Language', $lang_msg[1]);
$plugin->viewAddVar('Date', $lang_msg[2]);
// if(!in_array('DateGMTExpiration', $hidden_fields_arr))$plugin->viewAddVar('DateGMTExpiration', $lang_msg[3]);

$plugin->viewAddVar('Title', $lang_msg[4]);
if(!in_array('Resume', $hidden_fields_arr))$plugin->viewAddVar('Resume', $lang_msg[5]);
if(!in_array('Text', $hidden_fields_arr))$plugin->viewAddVar('Text', $lang_msg[6]);

if(!in_array('Comment', $hidden_fields_arr))$plugin->viewAddVar('Comment', $lang_msg[17]);
if(!in_array('Event', $hidden_fields_arr))$plugin->viewAddVar('Event', $lang_msg[8]);




$plugin->viewAddVar('Active', $lang_msg[9]);

function hookData($row){

	if(!empty($row['NewsImage']))
		$row['NewsImage'] = '<img src="'.NUTS_NEWS_IMAGES_URL.'/thumb_'.$row['NewsImage'].'" />';

	if(!empty($row['Resume']))
		$row['Resume'] = '<fieldset>'.$row['Resume'].'</fieldset>';

	if(!empty($row['Text']))
		$row['Text'] = '<fieldset>'.$row['Text'].'</fieldset>';


	return $row;
}


$plugin->viewRender('hookData');

