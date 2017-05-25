<?php
/**
 * Plugin comments - action Delete
 * 
 * @version 1.0
 * @date 31/12/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


$plugin->deleteDbTable(array('NutsPageComment'));

// hacks delete action



$plugin->deleteRender();

