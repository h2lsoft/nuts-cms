<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/form.inc.php');

$plugin->formInit();
if($plugin->formValid())
{
	$CUR_ID = $plugin->formUpdate();
	include_once(PLUGIN_PATH.'/trt_url_rewriting.inc.php');
}


?>