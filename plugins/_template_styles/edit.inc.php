<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// configuration *************************************************************************
$rte_start = '/*** rte start ***/';
$rte_end = '/*** rte end ***/';
$cur_theme = nutsGetTheme();
$global_css_file = NUTS_THEMES_PATH."/$cur_theme/style.css";
if(!isset($_GET['f']))$_GET['f'] = 'RTE';
$plugin->trace($_GET['f']);



// <editor-fold defaultstate="collapsed" desc="Post ***************************************">
if($_POST)
{
	$css_contents = file_get_contents($global_css_file);
	if($_POST['f'] == 'RTE')
	{
		$rte_contents = $nuts->extractStr($css_contents, $rte_start, $rte_end, true);
		$rte_contents_replace = $rte_start.CR.trim($_POST['Configuration']).CR.$rte_end.CR.CR;
		$css_contents = str_replace($rte_contents, $rte_contents_replace, $css_contents);
	}
	else
	{
		$rte_contents = $nuts->extractStr($css_contents, $rte_start, $rte_end, true);
		$css_contents = trim($rte_contents).CR.CR.trim($_POST['Configuration']);
	}

	$op_res = @file_put_contents($global_css_file, $css_contents);
	if(!$op_res)
	{
		$plugin->trace("`{$_POST['f']}` => post error");
		die('error');
	}
	else
	{
		$plugin->trace("`{$_POST['f']}` modified");
		die('ok');
	}
}
// </editor-fold>


// get css files
$select_config_files = '<option value="RTE">'.ucfirst($cur_theme).'> Richeditor styles</option>"'."\n";
$select_config_files .= '<option value="ALL">'.ucfirst($cur_theme).'> Global styles</option>"'."\n";
$GLOBALS['Syntax'] = 'css';

$cfg = file_get_contents($global_css_file);

if($_GET['f'] == 'RTE')
{
	$cfg = $nuts->extractStr($cfg, $rte_start, $rte_end, false);
}
else
{
	$x = $nuts->extractStr($cfg, $rte_start, $rte_end, true);
	$cfg = str_replace($x, "", $cfg);
}
$cfg = trim($cfg);



$nuts->open(PLUGIN_PATH.'/form.html');
$nuts->parse('cfg', $cfg);
$plugin->render = $nuts->output();



?>