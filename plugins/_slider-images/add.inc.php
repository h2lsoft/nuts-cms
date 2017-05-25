<?php
/**
 * Plugin slider  images- action Add
 *
 * @version 1.0
 * @date 01/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$CUR_ID = $plugin->formInsert();
}

