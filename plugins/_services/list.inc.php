<?php
/**
 * Plugin services - action List
 * 
 * @version 1.0
 * @date 19/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsService');

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Name');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', '', '; width:30px; white-space:nowrap', true);
$plugin->listAddCol('Description', '', '', false);
$plugin->listAddCol('Output', '', 'center; width:30px', false);
$plugin->listAddCol('Url', '', 'center; width:30px', false);


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

    $url = WEBSITE_URL.'/plugins/_services/?name='.strtouri($row['Name']).'&ID='.$row['ID'].'&output='.strtolower($row['Output']).'&token='.$row['Token'];
    $row['Url'] = '<a href="'.$url.'" target="_blank"><img src="/nuts/img/icon-web.gif" /></a>';


	
	return $row;
}



?>