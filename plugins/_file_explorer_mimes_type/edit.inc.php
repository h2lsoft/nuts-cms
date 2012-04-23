<?php
/**
 * Plugin file_explorer_mimes_type - action Edit
 * 
 * @version 1.0
 * @date 22/03/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/form.inc.php');

$rec = $plugin->formInit();
if($plugin->formValid())
{
	$CUR_ID = $plugin->formUpdate();
}


?>