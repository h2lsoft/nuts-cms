<?php

include(PLUGIN_PATH.'/form.inc.php');

$plugin->formInit();
if($plugin->formValid())
{
    $CUR_ID =  $plugin->formUpdate();
    include_once(PLUGIN_PATH.'/trt_save_users.inc.php');
}

