<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');

// sql table
$plugin->formDBTable(array('NutsBlock'));

// get all themes
$select_blocks = array();
foreach($group_blocks as $gb)
	$select_blocks[] = array('value' => $gb[0], 'label' => $gb[1]);

// distinct types
$types = array(
				array('label' => 'Html', 'value' => 'HTML'),
				array('label' => $lang_msg[7], 'value' => 'TEXT')
			);

// fields
$plugin->formAddFieldSelect('GroupName', $lang_msg[1], true, $select_blocks, '', '', '', $group_blocks_help);
$plugin->formAddFieldTextAjaxAutoComplete('SubGroupName', $lang_msg[10], false, 'countains', '', '', '', '', '', '', '', '', $lang_msg[11]);
$plugin->formAddFieldText('Name', $lang_msg[2], true);
$plugin->formAddFieldSelect('Type', $lang_msg[6], true, $types);
$plugin->formAddFieldHtmlArea('Text', $lang_msg[3], false, 'height:500px;');
$plugin->formAddFieldImageBrowser('Preview', $lang_msg[8], false, 'nuts_block_preview', $lang_msg[9]);
$plugin->formAddFieldBoolean('Visible', $lang_msg[4], true);

