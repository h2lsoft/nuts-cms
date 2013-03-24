<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->listSetDbTable("NutsSliderImage", "", "NutsSliderID={$_GET['NutsSliderID']}", "ORDER BY Position");

// create fields

// add list position
$plugin->listAddColPosition('Position', 'NutsSliderID', $_GET['NutsSliderID']);
$plugin->listAddCol('Image', '', 'center;width:120px', false);
$plugin->listAddCol('Title', $lang_msg[1], false);
$plugin->listAddColImg('Visible', '', false);

// render list
$plugin->listAddButtonUrlAdd("NutsSliderID={$_GET['NutsSliderID']}");
$plugin->listCopyButton = false;
$plugin->listRender(0, 'hookData');


function hookData($row)
{
    global $plugin;

    $row['Position'] = $plugin->listGetPositionContents($row['ID']);
    $row['Image'] = '<img src="/uploads/_slider-images/'.$row['SliderImage'].'?t='.time().'" style="height:60px;" class="image_preview" />';

    return $row;
}



?>