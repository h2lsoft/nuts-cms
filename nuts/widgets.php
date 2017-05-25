<?php
/**
 * List nuts widgets:
 *
 *	- plugin
 *	- forms
 *	- survey
 *  - blocks
 *	- pattern
 *	- region
 *  - zone
 */

// includes
include_once("config.inc.php");
include_once("headers.inc.php");

// initialization
$nuts = new NutsCore(false);
$nuts->DbConnect();
include_once("_inc/session.inc.php");
include(WEBSITE_PATH."/nuts/lang/{$_SESSION['Language']}.inc.php");
$timer = time();

// execution
$nuts->open(NUTS_PATH.'/_templates/widgets.html');


$no_record = '<div class="w_item_no_record">'.$nuts_lang_msg[6].'</div>';


// plugins ********************************************************************
$dir_plugin = WEBSITE_PATH.'/plugins';
$plugins = glob($dir_plugin.'/*', GLOB_ONLYDIR);
$new_plugins = array();
foreach($plugins as $plugin)
{
	$plugin = str_replace($dir_plugin."/", '', $plugin);
	if(!in_array($plugin, array('_page-manager','_news')) && is_dir($dir_plugin."/".$plugin."/www"))
		$new_plugins[] = $plugin;
}

// parse items
if(!count($new_plugins))
{
	$nuts->parseBloc('plugins', $no_record);
}
else
{
	foreach($new_plugins as $item)
	{
		$img = '/plugins/_dropbox/icon.png';
		if(file_exists(WEBSITE_PATH."/plugins/$item/icon.png"))
			$img = "/plugins/$item/icon.png";


		$item_c = "{@NUTS    TYPE='PLUGIN'    NAME='$item'    PARAMETERS=''}";

		$nuts->parse('plugins.item', $item_c);
		$nuts->parse('plugins.img', $img);
		$nuts->parse('plugins.name', $item);
		$nuts->loop('plugins');
	}
}

// title
$img = NUTS_PLUGINS_URL.'/_dropbox/icon.png';
$nuts->parse('menu.img', $img);
$nuts->parse('menu.name', 'Plugins');
$nuts->parse('menu.count', count($new_plugins));
$nuts->loop('menu');

// forms ********************************************************************
$sql = "SELECT ID, Name, Description FROM NutsForm WHERE Deleted = 'NO'";
$nuts->doQuery($sql);
$res = $nuts->dbNumRows();

if(!$res)
{
	$nuts->parseBloc('forms', $no_record);
}
else
{
	$img = NUTS_PLUGINS_URL.'/_form-builder/icon.png';
	while($row = $nuts->dbFetch())
	{
		$item_c = "{@NUTS    TYPE='FORM'    NAME='{$row['Name']}'}";

		$nuts->parse('forms.item', $item_c);
		$nuts->parse('forms.img', $img);
		$nuts->parse('forms.name', ucfirst($row['Name']));
		$nuts->parse('forms.desc', ucfirst($row['Description']));
		$nuts->loop('forms');
	}
}


// title
$img = NUTS_PLUGINS_URL.'/_form-builder/icon.png';
$nuts->parse('menu.img', $img);
$nuts->parse('menu.name', $nuts_lang_msg[78]);
$nuts->parse('menu.count', $res);
$nuts->loop('menu');



// survey *****************************************************************************************
$sql = "SELECT ID, Title FROM NutsSurvey WHERE Deleted = 'NO'";
$nuts->doQuery($sql);
$res = $nuts->dbNumRows();
if(!$res)
{
	$nuts->parseBloc('survey', $no_record);
}
else
{
	$img = NUTS_PLUGINS_URL.'/_form-builder/icon.png';
	while($row = $nuts->dbFetch())
	{
		$item_c = sprintf("{@NUTS    TYPE='SURVEY'    ID='%s'    TITLE='%s'}", $row['ID'], $row['Title']);

		$nuts->parse('survey.item', $item_c);
		$nuts->parse('survey.img', $img);
		$nuts->parse('survey.name', ucfirst($row['Title']));
		$nuts->loop('survey');
	}
}

// title
$img = NUTS_PLUGINS_URL.'/_survey/icon.png';
$nuts->parse('menu.img', $img);
$nuts->parse('menu.name', $nuts_lang_msg[79]);
$nuts->parse('menu.count', $nuts->dbNumRows());
$nuts->loop('menu');




// blocks *****************************************************************************************************************************
$sql = "SELECT ID, GroupName, SubGroupName, Name, Preview FROM NutsBlock WHERE Deleted = 'NO' AND Visible = 'YES' ORDER BY GroupName, SubGroupName, Name";
$nuts->doQuery($sql);
$res = $nuts->dbNumRows();
if(!$res)
{
	$nuts->parseBloc('blocks', $no_record);
}
else
{

	while($row = $nuts->dbFetch())
	{
		$item_c = sprintf("{@NUTS    TYPE='BLOCK'    NAME='%s'}", $row['Name']);

		$img = NUTS_THEMES_URL.'/default/_preview/no-preview.png';
		if(!empty($row['Preview']))
			$img = $row['Preview'];

		if(empty($row['GroupName']))$row['GroupName'] = "-";
		if(empty($row['SubGroupName']))$row['SubGroupName'] = "-";

		$nuts->parse('blocks.item', $item_c);
		$nuts->parse('blocks.img', $img);
		$nuts->parse('blocks.name', ucfirst($row['Name']));
		$nuts->parse('blocks.group', ucfirst($row['GroupName']));
		$nuts->parse('blocks.sub_group', ucfirst($row['SubGroupName']));
		$nuts->loop('blocks');
	}

}


$img = NUTS_PLUGINS_URL.'/_block_builder/icon.png';
$nuts->parse('menu.img', $img);
$nuts->parse('menu.name', 'Blocks');
$nuts->parse('menu.count', $nuts->dbNumRows());
$nuts->loop('menu');

// pattern ************************************************************************************************
$sql = "SELECT ID, Name, Description, Pattern, Type FROM NutsPattern WHERE Deleted = 'NO' ORDER BY Name";
$nuts->doQuery($sql);
$res = $nuts->dbNumRows();
if(!$res)
{
	$nuts->parseBloc('patterns', $no_record);
}
else
{
	$img = NUTS_PLUGINS_URL.'/_pattern/icon.png';
	while($row = $nuts->dbFetch())
	{
		$item_c = $row['Pattern'];

		$nuts->parse('patterns.item', $item_c);
		$nuts->parse('patterns.img', $img);
		$nuts->parse('patterns.name', ucfirst($row['Name']));
		$nuts->parse('patterns.type', $row['Type']);
		$nuts->parse('patterns.desc', ucfirst($row['Description']));
		$nuts->loop('patterns');
	}
}


// title
$img = NUTS_PLUGINS_URL.'/_pattern/icon.png';
$nuts->parse('menu.img', $img);
$nuts->parse('menu.name', 'Patterns');
$nuts->parse('menu.count', $nuts->dbNumRows());
$nuts->loop('menu');

// region *****************************************************************************************************
$sql = "SELECT ID, Name, Description FROM NutsRegion WHERE Deleted = 'NO' ORDER BY Name";
$nuts->doQuery($sql);
$res = $nuts->dbNumRows();

if(!$res)
{
	$nuts->parseBloc('regions', $no_record);
}
else
{
	$img = NUTS_PLUGINS_URL.'/_region-manager/icon.png';
	while($row = $nuts->dbFetch())
	{
		$item_c = sprintf("{@NUTS    TYPE='REGION'    NAME='%s'}", $row['Name']);

		$nuts->parse('regions.item', $item_c);
		$nuts->parse('regions.img', $img);
		$nuts->parse('regions.name', ucfirst($row['Name']));
		$nuts->parse('regions.desc', ucfirst($row['Description']));
		$nuts->loop('regions');
	}
}

// title
$img = NUTS_PLUGINS_URL.'/_region-manager/icon.png';
$nuts->parse('menu.img', $img);
$nuts->parse('menu.name', 'Regions');
$nuts->parse('menu.count', $nuts->dbNumRows());
$nuts->loop('menu');

// zones *****************************************************************************
$sql = "SELECT ID, Name, Description FROM NutsZone WHERE Deleted = 'NO' AND Visible = 'YES' ORDER BY Name";
$nuts->doQuery($sql);
$res = $nuts->dbNumRows();

if(!$res)
{
	$nuts->parseBloc('zones', $no_record);
}
else
{
	$img = NUTS_PLUGINS_URL.'/_zone-manager/icon.png';
	while($row = $nuts->dbFetch())
	{
		$item_c = sprintf("{@NUTS    TYPE='ZONE'    NAME='%s'}", $row['Name']);

		$nuts->parse('zones.item', $item_c);
		$nuts->parse('zones.img', $img);
		$nuts->parse('zones.name', ucfirst($row['Name']));
		$nuts->parse('zones.desc', ucfirst($row['Description']));
		$nuts->loop('zones');
	}
}

// title
$img = NUTS_PLUGINS_URL.'/_zone-manager/icon.png';
$nuts->parse('menu.img', $img);
$nuts->parse('menu.name', 'Zones');
$nuts->parse('menu.count', $nuts->dbNumRows());
$nuts->loop('menu');

$nuts->write();

$nuts->dbClose();


