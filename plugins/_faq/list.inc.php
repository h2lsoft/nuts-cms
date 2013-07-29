<?php
/**
 * Plugin faq - action List
 *
 * @version 1.0
 * @date 02/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/config.inc.php');

// assign table to db
$plugin->listSetDbTable('NutsFAQ', "", "", "", "");

// search engine
$plugin->listSearchAddFieldSelectSql('Language', $lang_msg[1]);
$plugin->listSearchAddFieldSelectSql('Category', $lang_msg[2]);
$plugin->listSearchAddFieldText('Question', $lang_msg[3]);

// create fields
$plugin->listAddCol('Language', '&nbsp;', 'center; width:30px', false);
$plugin->listAddCol('Category', $lang_msg[2], '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Question', $lang_msg[3], '', false);
$plugin->listAddColImg('Visible', $lang_msg[4]);
$plugin->listAddCol('Position', $lang_msg[6], 'center; width:30px', false);


// render list
$plugin->listSetFirstOrderBySort('ASC');
$plugin->listRender(100, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;


	$row['Language'] = '<img src="/library/media/images/flag/'.$row['Language'].'.gif" />';


	return $row;
}



?>