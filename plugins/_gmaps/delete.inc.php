<?php
/**
 * Plugin gmaps - action Delete
 * 
 * @version 1.0
 * @date 08/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


$plugin->deleteDbTable(array('NutsGMaps'));

// hacks delete action



$plugin->deleteRender();



?>