<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

/* @var $plugin Plugin */
include_once(PLUGIN_PATH.'/config.inc.php');

// sql table PLUGIN_PATH
$plugin->formDBTable(array('NutsI18n'));

// fields
$lng_default = nutsGetDefaultLanguage();
$plugin->formAddFieldTextArea('Pattern', '<img src="'.NUTS_IMAGES_URL.'/flag/'.nutsGetDefaultLanguage().'.gif" />'." Pattern", true, '', 'height:150px');

// dynamically add language
foreach($i18n_langs as $i18n_lang)
{
	if($i18n_lang != $lng_default)
	{
		$translator = '<a href="javascript:translator(\'Pattern\', \'LANG_'.$i18n_lang.'\', \''.$lng_default.'\', \''.$i18n_lang.'\')">Translator</a>';
		$plugin->formAddField("LANG_{$i18n_lang}", '<img src="'.NUTS_IMAGES_URL.'/flag/'.$i18n_lang.'.gif" /> '.$translator, 'textarea', false, array('style' => 'height:150px', 'lang' => $i18n_lang, 'class' => 'lang2', 'help' => $lang_msg[1]));
	}
}

$plugin->formAddException('LANG_*');

if($_POST)
{
	// recreate Replace
	$rep = array();
	foreach($i18n_langs as $i18n_lang)
	{
		$rep[$i18n_lang] = @$_POST["LANG_{$i18n_lang}"];
	}
	
	$_POST['Replacement'] = serialize($rep);

}


