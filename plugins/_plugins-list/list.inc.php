<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->listSetDbTable('NutsMenu');


// create fields
$plugin->listAddCol('Name', '', '; width:10px; white-space:nowrap;', true); // with order by
$plugin->listAddCol('Version', '', 'center; width:10px;  white-space:nowrap;', false);
$plugin->listAddCol('Language', '', 'center; width:10px; white-space:nowrap;', false);
$plugin->listAddCol('Author', '', 'center; width:10px; white-space:nowrap;', false);
$plugin->listAddCol('Description', '', '', false);


// render list
$plugin->listSetFirstOrderBySort('asc');
$plugin->listRender(0, 'hookData');


function hookData($row)
{
	$yml_path = WEBSITE_PATH.'/plugins/'.$row['Name'].'/info.yml';
	$icon = '<img src="'.WEBSITE_URL.'/plugins/'.$row['Name'].'/icon.png'.'" align="absbottom" style="width:24px;" />';

	$parser = $yaml = Spyc::YAMLLoad($yml_path);
	
	$row['Version'] = $parser['version'];
	$row['Author'] = $parser['author'];
	$row['Description'] = $parser['info'];	
	$langs = explode(',', $parser['langs']);
	$langs = array_map('trim', $langs);
	$default_lang = $langs[0];

	// change description
	if($default_lang != $_SESSION['Language'] && isset($parser['info_'.$_SESSION['Language']]))
	{
		$row['Description'] = $parser['info_'.$_SESSION['Language']];
	}

	if(in_array($_SESSION['Language'], $langs))
		$default_lang = $_SESSION['Language'];

	include(WEBSITE_PATH.'/plugins/'.$row['Name'].'/lang/'.$default_lang.'.inc.php');
	$row['Name'] = $icon.' '.$lang_msg[0];

	$cur_lang = '';
	$langs = explode(',', str_replace(' ','',$parser['langs']));
	foreach($langs as $lang)
	{
		$cur_lang .= '<img src="'.NUTS_IMAGES_URL.'/flag/'.$lang.'.gif" align="absbottom" /> ';
	}

	$row['Language'] = $cur_lang;
	

	return $row;
}



?>