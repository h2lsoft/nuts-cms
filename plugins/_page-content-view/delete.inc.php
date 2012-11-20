<?php
/**
 * Plugin page-content-view - action Delete
 * 
 * @version 1.0
 * @date 20/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


$plugin->deleteDbTable(array('NutsPageContentView'));

// hacks delete action



$plugin->deleteRender();



?>