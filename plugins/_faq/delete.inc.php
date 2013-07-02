<?php
/**
 * Plugin faq - action Delete
 * 
 * @version 1.0
 * @date 02/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/config.inc.php');

$plugin->deleteDbTable(array('NutsFAQ'));

// hacks delete action



$plugin->deleteRender();



?>