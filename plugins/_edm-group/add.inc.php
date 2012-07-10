<?php

include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$CUR_ID = $plugin->formInsert();
    include_once(PLUGIN_PATH.'/trt_save_users.inc.php');
}


?>