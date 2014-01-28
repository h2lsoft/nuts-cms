<?php
/**
 * Plugin slider - action List
 *
 * @version 1.0
 * @date 01/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsSlider', "
                                          (SELECT COUNT(*) FROM NutsSliderImage WHERE NutsSliderID = NutsSlider.ID AND Deleted = 'NO') AS Slides
");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Name', $lang_msg[1]);

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', $lang_msg[1], '', true);
$plugin->listAddCol('Images', '', 'center; width:30px', false);
$plugin->listAddColImg('GenerateJs', "Javascript", '', false);
$plugin->listAddCol('Code', "", '', false);



// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;


	$row['Name'] = "<b>{$row['Name']}</b><br><span class='mini'>{$row['Description']}</span>";
	$row['Code'] = "<pre class='copy'>{@NUTS    TYPE='PLUGIN'    NAME='_slider'    PARAMETERS='{$row['ID']}'}</pre>";

    $row['Images'] = <<<EOF


<a class="counter" href="javascript:popupModal('/nuts/?mod=_slider-images&do=list&popup=1&NutsSliderID={$row['ID']}');"> <img src="/plugins/_slider/icon.png" align="absbottom" style="width:16px;" /> {$row['Slides']}</a>


EOF;


	return $row;
}

