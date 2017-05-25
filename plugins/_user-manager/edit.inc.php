<?php

/* @var $plugin Plugin */

include(PLUGIN_PATH.'/form.inc.php');

$plugin->formInit();
$r = $plugin->formInitGetRow();

$r['Password'] = nutsUserGetPassword($r['ID']);

$plugin->formInitSetRow($r);

if($plugin->formValid())
{
	$CUR_ID = $plugin->formUpdate();
	
	nutsUserSetPassword($CUR_ID, $_POST['Password']);
	
	
	include_once(PLUGIN_PATH.'/trt_emailer.inc.php');
}


