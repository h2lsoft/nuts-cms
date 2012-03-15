<?php

include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$CUR_ID = $plugin->formInsert();	
	nutsUserSetPassword($CUR_ID, $_POST['Password']);
	
	
	include_once(PLUGIN_PATH.'/trt_emailer.inc.php');

}


?>