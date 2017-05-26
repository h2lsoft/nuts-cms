<?php

/*@var $plugin Plugin */
/*@var $nuts NutsCore */

include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$_POST['DateCreate'] = 'NOW()';
	$CUR_ID = $plugin->formInsert();
}

