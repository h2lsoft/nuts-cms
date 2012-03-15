<?php

/* @var $plugin Plugin */
include(PLUGIN_PATH.'/form.inc.php');

$plugin->formInit();

$row = $plugin->formInitGetRow();
$r = unserialize($row['Replacement']);

foreach($i18n_langs as $i18n_lang)
	$r["LANG_{$i18n_lang}"] = @$r[$i18n_lang];

$row = array_merge($row, $r);
$plugin->formInitSetRow($row);


if($plugin->formValid())
{
	$plugin->formUpdate();
}


?>