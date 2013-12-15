<?php
/**
 * Plugin gmaps - action List
 *
 * @version 1.0
 * @date 08/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// ajax ****************************************************************************************************************
if(@$_GET['ajaxer'] == 1)
{
	if(@$_GET['_action'] == 'geocoder')
	{
		$uri = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($_GET['address'])."&sensor=false";
		$contents = file_get_contents($uri);
		die($contents);
	}
}



// assign table to db
$plugin->listSetDbTable('NutsGMaps', "(SELECT COUNT(*) FROM NutsGMapsPOI WHERE Deleted = 'NO' AND NutsGMapsID = NutsGMaps.ID) AS POI");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldText('Name', $lang_msg[2]);
$plugin->listSearchAddFieldSelect('Type', '', $lang_msg[1]);

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', $lang_msg[2], '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Type', '', 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('POI', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('Code', '', 'white-space:nowrap;', false);

// popup
if(@$_GET['popup'] == 1)
{
	$plugin->listAddCol('AddCode', '&nbsp;', 'center; width:35px');
}


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin, $lang_msg;

	$original_name = $row['Name'];

	$name = $row['Name'];
	$name = str_replace("'", "`", $name);

	$row['Name'] = "<b>{$row['Name']}</b><br />{$row['Description']}";


	if($row['Type'] == 'CLASSIC')$row['Type'] = $lang_msg[1][0]['label'];
	elseif($row['Type'] == 'STATIC')$row['Type'] = $lang_msg[1][1]['label'];
	elseif($row['Type'] == 'STREET VIEW')$row['Type'] = $lang_msg[1][2]['label'];


	$row['Code'] = "<pre class='copy'>{@NUTS    TYPE='PLUGIN'    NAME='_gmaps'    PARAMETERS='{$row['ID']}; $name'}</pre>";


	$uri = 'index.php?mod=_gmaps-poi&do=list&NutsGMapsID_operator=_equal_&NutsGMapsID='.$row['ID'];
	$tmp = '<a class="counter" href="javascript:;" onclick="popupModal(\''.$uri.'\', \'POI\');"><i class="icon-location"></i> '.$row['POI'].'</a>';
	$row['POI'] = $tmp;



	// add code
	if(@$_GET['popup'] == 1)
	{

		$original_name = str_replace("'", '`', $original_name);
		$map_name = $original_name;
		$original_name = 'gmaps - '.$original_name;
		$label = base64_encode($original_name);

		$media = 'plugin';
		$code = "<p><img class=\"nuts_tags\" src=\"/nuts/img/icon_tags/tag.php?tag={$media}&label=$label\" title=\"{@NUTS    TYPE='PLUGIN'    NAME='_gmaps'    PARAMETERS='{$row['ID']};$map_name'}\" border=\"0\"></p>";
		$code = str_replace('"', '``', $code);
		$code = str_replace("'", "\\'", $code);

		$row['AddCode'] = '<a href="javascript:;" onclick="window.opener.WYSIWYGAddText(\''.$_GET['parentID'].'\', \''.$code.'\'); window.close();" class="tt" title="'.$lang_msg[6].'"><i class="icon-arrow-down-3" style="font-size:18px; margin:0; padding:0;"></i></a>';
	}


	return $row;
}

