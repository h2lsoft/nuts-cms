<?php
/* @var $plugin Plugin */

include(PLUGIN_PATH.'/config.inc.php');

// assign table to db
$plugin->listSetDbTable('NutsBlock');

// create search engine
$select_blocks = array();
foreach($group_blocks as $gb)
	$select_blocks[] = array('value' => $gb[0], 'label' => $gb[1]);

// search engine
$plugin->listSearchAddFieldSelect('GroupName', $lang_msg[1], $select_blocks);
$plugin->listSearchAddFieldSelectSql('SubGroupName', $lang_msg[10]);
$plugin->listSearchAddFieldTextAjaxAutoComplete('Name', $lang_msg[2]);
$plugin->listSearchAddFieldText('Text', $lang_msg[3], 'text');
$plugin->listSearchAddFieldBoolean('Visible', $lang_msg[4]);


// create fields
$plugin->listAddCol('Preview', ' ', 'center; width:10px', false); // with order by
$plugin->listAddCol('GroupName', $lang_msg[1], 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('SubGroupName', $lang_msg[10], 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Name', $lang_msg[2], '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Code', $lang_msg[5], '', false);
$plugin->listAddColImg('Visible', $lang_msg[4], '', true);

$plugin->listFirstOrderBy = 'GroupName';


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	$row['Code'] = "<pre>{@NUTS	TYPE='BLOCK'	NAME='{$row['Name']}'}</pre>";

	if(empty($row['Preview']))$row['Preview'] = '/nuts/img/no-preview.png';
	$row['Preview'] = '<img src="'.$row['Preview'].'" class="image_preview" style="height:65px; max-width:160px;" />';
	

	return $row;
}



?>