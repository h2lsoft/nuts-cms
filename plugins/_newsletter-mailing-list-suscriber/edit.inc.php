<?php

/* @var $plugin Plugin */
include(PLUGIN_PATH.'/form.inc.php');

$plugin->formInit();
if($plugin->formValid())
{
	$CUR_ID = $plugin->formUpdate();
}

