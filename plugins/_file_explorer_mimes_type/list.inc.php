<?php
/**
 * Plugin file_explorer_mimes_type - action List
 *
 * @version 1.0
 * @date 22/03/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsFileExplorerMimesType');

// search engine
$plugin->listSearchAddFieldSelectSql('Extension');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Extension', '', 'center; width:30px', true);
$plugin->listAddCol('Mimes', '', '', false);

// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

	$row['Extension'] = '<span class="tag">'.$row['Extension'].'</span>';
	$row['Mimes'] = '<span class="mini">'.nl2br(trim($row['Mimes'])).'</span>';

	return $row;
}



?>