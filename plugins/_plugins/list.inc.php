<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsMenu', '', "", "ORDER BY Position");

// create search engine
$opts = array();
$k = 1;

foreach($mods_group as $mod)
{
	$opts[] = array('label' => $mod['name'], 'value' => $k);
	$k++;
}

$plugin->listSearchAddFieldSelect('Category', '', $opts);
$plugin->listSearchAddFieldBoolean('Visible');



// create fields
// $plugin->listAddCol('ID', '', 'center; width:30px', true);

// add list position
if(isset($_GET['Category']))
	$plugin->listAddColPosition('Position', 'Category', $_GET['Category']);
//$plugin->listAddCol('Position', '', 'center; width:10px', true);

$plugin->listAddCol('Category', '', 'center; width:30px', true, array());
$plugin->listAddCol('Name', '', '', true);
$plugin->listAddColImg('Visible', '');

// configuration
if(nutsUserHasRight($_SESSION['NutsGroupID'], '_configuration', 'edit'))
{
	$plugin->listAddCol('Configure', ' ', 'center; width:30px', false);
}

//$plugin->listSetFirstOrderBy('Position');
//$plugin->listSetFirstOrderBySort('asc');
$plugin->listCopyButton = false;
$plugin->listSearchOpenOnload = true;
$plugin->listWaitingForUserSearching = true;
$plugin->listWaitingForUserSearchingMessage = "Please select a category";

if(nutsUserHasRight($_SESSION['NutsGroupID'], '_right-manager', 'edit'))
	$plugin->listAddButton("RightManager", "Right manager", "popupModal('/nuts/?mod=_right-manager&do=edit&NutsGroupID={$_SESSION['NutsGroupID']}&display=all&popup=1');");


// render list
$plugin->listRender(0, 'hookData');


function hookData($row)
{
	global $mods_group, $plugin;

	$plugin_name = $row['Name'];
	$icon_path = NUTS_PLUGINS_PATH.'/'.$row['Name'].'/icon.png';
	if(file_exists($icon_path))
	{
		$icon_url = str_replace(NUTS_PLUGINS_PATH, NUTS_PLUGINS_URL, $icon_path);
		$row['Name'] = '<img src="'.$icon_url.'" style="width:18px;" align="absbottom" /> '.$row['Name'];
	}

	if($row['BreakBefore'] == 1)$row['Name'] = "<hr />".$row['Name'];
	if($row['BreakAfter'] == 1)$row['Name'] .= "<hr />";

	if($row['Category'] != '')
		$row['Category'] = $mods_group[$row['Category']-1]['name'];

	$row['Position'] = $plugin->listGetPositionContents($row['ID']);

	// configure
	$cfg_file = NUTS_PLUGINS_PATH."/$plugin_name/config.inc.php";
	if(!file_exists($cfg_file))
	{
		$row['Configure'] = "";
	}
	else
	{
		$config_url = '/nuts/index.php?mod=_configuration&do=edit&f='.base64_encode($cfg_file);
		$configure = <<<EOF

							<a class="tt" href="javascript:popupModal('{$config_url}', 'Configuration', 1024, 768, '');" title="Configuration"><img src="img/configuration_24x24.png" alt="Configuration" align="absbottom" /></a>

EOF;
		$row['Configure'] = $configure;
	}








	return $row;
}



?>