<?php
/**
 * Plugin _cron-list - action List
 *
 * @version 1.0
 * @date 16/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsCron');

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Type');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Type', '', 'center; width:30px', true);
$plugin->listAddCol('Name', $lang_msg[1], '', true);


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

    $row['Name'] .= "<br><span class='mini'>{$row['Description']}</span>";
    $row['Name'] .= "<pre class='copy'>{$row['Command']}</pre>";

	return $row;
}


