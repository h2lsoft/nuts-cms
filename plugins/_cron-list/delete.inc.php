<?php
/**
 * Plugin _cron-list - action Delete
 * 
 * @version 1.0
 * @date 16/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


$plugin->deleteDbTable(array('NutsCron'));

// hacks delete action



$plugin->deleteRender();



?>