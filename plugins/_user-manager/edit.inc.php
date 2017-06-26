<?php

/* @var $plugin Plugin */

include(PLUGIN_PATH.'/form.inc.php');

$plugin->formInit();
$r = $plugin->formInitGetRow();
$r['Password'] = '';

$plugin->formInitSetRow($r);

if($plugin->formValid())
{
	$CUR_ID = $plugin->formUpdate();
	
	if(!empty($_POST['Password']))
		nutsUserSetPassword($CUR_ID, $_POST['Password']);
	
	
	include_once(PLUGIN_PATH.'/trt_emailer.inc.php');
}


