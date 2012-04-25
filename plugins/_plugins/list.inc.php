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
$plugin->listAddCol('Icon', ' ', 'center; width:10px;', false);
$plugin->listAddCol('Name', '', '', true);
$plugin->listAddCol('Version', '', 'center; width:10px;  white-space:nowrap;', false);
$plugin->listAddCol('Language', '', 'center; width:10px; white-space:nowrap;', false);
$plugin->listAddCol('Author', $lang_msg[2], 'center; width:10px; white-space:nowrap;', false);
$plugin->listAddCol('Web', '', 'center; width:10px;', false);
$plugin->listAddCol('Email', '', 'center; width:10px;', false);


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

    $yml_path = WEBSITE_PATH.'/plugins/'.$row['Name'].'/info.yml';
    $parser = Spyc::YAMLLoad($yml_path);

    $row['Version'] = $parser['version'];
    $row['Author'] = $parser['author'];
    $row['Description'] = $parser['info'];
    $row['Email'] = $parser['email'];

    // change description
    $langs = explode(',', $parser['langs']);
    $langs = array_map('trim', $langs);
    $default_lang = $langs[0];

    if($default_lang != $_SESSION['Language'] && isset($parser['info_'.$_SESSION['Language']]))
        $row['Description'] = $parser['info_'.$_SESSION['Language']];


    // name
    $plugin_name = $row['Name'];
	$icon_path = NUTS_PLUGINS_PATH.'/'.$row['Name'].'/icon.png';
	if(file_exists($icon_path))
	{
        $plugin_folder_name = $row['Name'];

        if(in_array($_SESSION['Language'], $langs))
            $default_lang = $_SESSION['Language'];

        include(WEBSITE_PATH.'/plugins/'.$row['Name'].'/lang/'.$default_lang.'.inc.php');
        $plugin_name_label = $lang_msg[0];


        $row['Description'] = ucfirst($row['Description']);

		$icon_url = str_replace(NUTS_PLUGINS_PATH, NUTS_PLUGINS_URL, $icon_path);
		$row['Icon'] = '<img src="'.$icon_url.'" style="width:32px;" align="absbottom" /> ';

        $row['Name'] = "<b>$plugin_name_label</b> ($plugin_folder_name)";
        $row['Name'] .= "<br /><span class='mini'>{$row['Description']}</span>";
	}

	if($row['BreakBefore'] == 1)$row['Name'] = "<hr noshade style='height:1px; border:0;' />".$row['Name'];
	if($row['BreakAfter'] == 1)$row['Name'] .= "<hr noshade style='height:1px; border:0;' />";

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
		$config_url = '/nuts/index.php?mod=_configuration&do=edit&popup=1&f='.base64_encode($cfg_file);
		$configure = <<<EOF

							<a class="tt" href="javascript:popupModal('{$config_url}', 'Configuration', 1024, 768, '');" title="Configuration"><img src="img/configuration_24x24.png" alt="Configuration" align="absbottom" /></a>

EOF;
		$row['Configure'] = $configure;
	}


    // Language
    $cur_lang = '';
    $langs = explode(',', str_replace(' ','',$parser['langs']));
    foreach($langs as $lang)
    {
        $cur_lang .= '<img src="'.NUTS_IMAGES_URL.'/flag/'.$lang.'.gif" align="absbottom" /> ';
    }

    $row['Language'] = $cur_lang;



    // website
    $row['Web'] = '';
    if(!empty($parser['website']))
    {
        $row['Web'] = <<<EOF

            <a class="tt" title="{$parser['website']}" href="{$parser['website']}" target="_blank"><img src="/nuts/img/website_48.png" style="width:16px;" /></a>
EOF;

    }

    // email
    $row['Email'] = '';
    if(!empty($parser['email']))
    {
        $row['Email'] = <<<EOF

            <a class="tt" title="{$parser['email']}" href="mailto:{$parser['email']}"><img src="/nuts/img/email.png" style="width:16px;" /></a>
EOF;
    }




	return $row;
}



?>