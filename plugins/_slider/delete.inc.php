<?php
/**
 * Plugin slider - action Delete
 *
 * @version 1.0
 * @date 01/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


$plugin->deleteDbTable(array('NutsSlider'));

// hacks delete action



$plugin->deleteRender();




