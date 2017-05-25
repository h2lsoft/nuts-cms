<?php

include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$_POST['NutsUserID'] = $_SESSION['NutsUserID'];
	$plugin->formInsert();
}

