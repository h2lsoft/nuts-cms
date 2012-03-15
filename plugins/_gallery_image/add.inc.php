<?php

include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$plugin->formInsert();
}


?>