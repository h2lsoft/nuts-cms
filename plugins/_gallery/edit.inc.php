<?php

include(PLUGIN_PATH.'/form.inc.php');

$plugin->formInit();
if($plugin->formValid())
{
	$CID = $plugin->formUpdate();
	include(PLUGIN_PATH.'/trt_mupload.inc.php');

}


?>