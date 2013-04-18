<?php
/**
 * Plugin user-shortcuts - action Delete
 * 
 * @version 1.0
 * @date 18/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


$plugin->deleteDbTable(array('NutsUserShortcut'));

// hacks delete action



$plugin->deleteRender();



?>