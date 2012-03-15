<?php

include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$plugin->formInsert();
	include_once(PLUGIN_PATH.'/trt_url_rewriting.inc.php');
}


?>