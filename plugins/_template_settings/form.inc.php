<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table
$plugin->formDBTable(array('NutsTemplateConfiguration'));

// languages
/*$lngs = array();
foreach($nuts_lang_options as $lng)
	$lngs[] = array('value' => $lng[0], 'label' => $lng[1]);*/
$plugin->formAddFieldSelect('LanguageDefault', $lang_msg[6], true, $nuts_lang_options);

// fields
$themes = array();
$a = glob(WEBSITE_PATH.'/library/themes/*', GLOB_ONLYDIR);
foreach($a as $theme)
{
	$theme_str = explode('/', $theme);
	$theme_str = $theme_str[count($theme_str)-1];
	$themes[] = array('value' => $theme_str, 'label' => ucfirst($theme_str));
}
$plugin->formAddFieldText('Languages', $lang_msg[7], false, '', '', '', '', $lang_msg[8]);
$plugin->formAddFieldSelect('Theme', $lang_msg[4], true, $themes);
//$plugin->formAddField('Description', $lang_msg[5], 'textarea');

$plugin->formAddFieldsetEnd();


if($_POST && !$nuts->formGetTotalError())
{
	// save in configuration file
	$replacement = "\$nuts_theme_selected = '{$_POST['Theme']}'; // theme selected";
	fileChangeLineContents(NUTS_PATH.'/config.inc.php', '$nuts_theme_selected =', $replacement);
}


?>