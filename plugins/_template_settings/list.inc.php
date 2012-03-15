<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->setListDbTable('NutsTemplateConfiguration');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true); // with order by
$plugin->listAddCol('Preview', " ", '; width:30px', false);
$plugin->listAddCol('Theme', $lang_msg[4], 'width:30px', true);
$plugin->listAddColImg('LanguageDefault', $lang_msg[6], '', true, NUTS_IMAGES_URL.'/flag/{LanguageDefault}.gif');
$plugin->listAddCol('Languages', $lang_msg[7], 'center');

// render list
$plugin->listRender(0, 'hookData');

function hookData($row)
{
	// preview
	$theme_preview = NUTS_THEMES_PATH."/{$row['Theme']}/_preview/theme.jpg";
	if(!file_exists($theme_preview))
	{
		$img_url = NUTS_PLUGINS_URL.'/_template_settings/no-preview.png';
		$row['Preview'] = '<img src="'.$img_url.'" style="padding:3px; border:px solid #ccc;" />';
	}
	else
	{
		$img_url = str_replace(NUTS_THEMES_PATH, NUTS_THEMES_URL, $theme_preview);
		$row['Preview'] = '<a title="Preview" class="tt"><img class="image_preview" src="'.$img_url.'" style="width:150px;" /></a>';
	}


	// information
	$cur_theme = $row['Theme'];

	$theme_info = NUTS_THEMES_PATH."/$cur_theme/info.yml";
	if(file_exists($theme_info))
	{
		$info = SPYC::YAMLLoad($theme_info);

		$row['Theme'] = "<b>".ucfirst($row['Theme'])." - {$info['info']}</b>";
		$row['Theme'] .= "<br />Version: {$info['version']}";
		$row['Theme'] .= "<br />Author: {$info['author']}";
		$row['Theme'] .= "<br />Website: {$info['website']}";
		$row['Theme'] .= "<br />Email: {$info['email']}";
		$row['Theme'] .= "<br />Language(s): {$info['langs']}";

	}
	else
	{
		$row['Theme'] = "<b>".ucfirst($row['Theme'])."</b>";
	}




	$res = explode(',', $row['Languages']);
	$res = array_unique($res);
	$str = "";

	foreach($res as $lng)
	{
		$lng = trim($lng);


		$img = NUTS_IMAGES_PATH."/flag/$lng.gif";
		if(file_exists($img))
		{
			$img = str_replace(NUTS_IMAGES_PATH, NUTS_IMAGES_URL, $img);
			$str .= ' <img src="'.$img.'" align="absmiddle" /> ';
		}
		else
		{
			$str .= ' '.$lng.' ';
		}

		$row['Languages'] = trim($str);

	}

	return $row;
}


?>