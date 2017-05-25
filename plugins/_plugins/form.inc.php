<?php

/* @var $plugin Plugin */

// sql table
$plugin->formDBTable(array('NutsMenu'));

// fields
$opts = array();
$opts[] = array('label' => " ", 'value' => "");
$k = 1;
foreach($mods_group as $mod)
{
	$opts[] = array('label' => $mod['name'], 'value' => $k);
	$k++;
}
$plugin->formAddFieldSelect('Category', $lang_msg[3], true, $opts);

// plugin list
$plugins_list = (array)glob(NUTS_PLUGINS_PATH.'/*');
sort($plugins_list);
$plugin_list = '<option value=""></option>'."\n";
foreach($plugins_list as $plg)
{
	$plg = str_replace(NUTS_PLUGINS_PATH.'/', '', $plg);
	$plugin_list .= '<option value="'.$plg.'">'.$plg.'</option>'."\n";
}
$plugin->formAddFieldSelectHtml('Name', $lang_msg[4], true, $plugin_list);
$plugin->formAddFieldText('Position', '', true, '', 'width:3em; text-align:center', '', '', $lang_msg[1]);
$plugin->formAddFieldText('ExternalUrl', $lang_msg[6], false);

/*$options = array();
$options[] = array('value' => 0, 'label' => 'No');
$options[] = array('value' => 1, 'label' => 'Yes');
$plugin->formAddFieldSelect('BreakBefore', '', true, $options);
$plugin->formAddFieldSelect('BreakAfter', '', true, $options);
*/

$plugin->formAddFieldBoolean('Visible', '', true);


if($_POST)
{
	if($_POST['Position'] == -1)
	{
		$_POST['Position'] = $plugin->formGetMaxPosition('Position', 'Category', $_POST['Category']);
		$_POST['Position'] += 1;
	}
}


