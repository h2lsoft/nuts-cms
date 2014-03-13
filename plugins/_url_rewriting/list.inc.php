<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


if(@$_GET['ajaxer'])
{
    include(PLUGIN_PATH.'/trt_url_rewriting.inc.php');
    die('ok');
}



// assign table to db
$plugin->listSetDbTable('NutsUrlRewriting', '', "", "ORDER BY Position");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Type');
$plugin->listSearchAddFieldText('Pattern');
$plugin->listSearchAddFieldText('Replacement', $lang_msg[1]);
$plugin->listSearchAddFieldTextAjaxAutoComplete('Tag');


// create fields
$plugin->listAddButton('Generate', $lang_msg[3], 'urlRewritingGenerate();');


// $plugin->listAddColPosition('Position');
$plugin->listAddCol('ID', '', 'center; width:30px', false); // with order by
$plugin->listAddCol('Type', '', 'center; width:30px', false); // with order by
$plugin->listAddCol('Pattern', '', '', false); // with order by
$plugin->listAddCol('Replacement', $lang_msg[1], '', false);
$plugin->listAddCol('Position', '', 'center; width:30px', true);
$plugin->listAddCol('Tag', '', 'center; width:30px; white-space:nowrap;', false);


// render list
$plugin->listRender(50, 'hookData');

function hookData($row)
{
	global $plugin;

	// $row['Position'] = $plugin->listGetPositionContents($row['ID']);
    if(!empty($row['Tag']))
    {
	    $tag_add = '';
	    if($row['Tag'] == 'auto')$tag_add = 'tag_green';
	    $row['Tag'] = "<span class='tag $tag_add'>{$row['Tag']}</span>";
    }

	return $row;
}



