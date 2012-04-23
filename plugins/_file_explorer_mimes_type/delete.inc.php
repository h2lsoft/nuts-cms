<?php
/**
 * Plugin file_explorer_mimes_type - action Delete
 * 
 * @version 1.0
 * @date 22/03/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


$plugin->deleteDbTable(array('NutsFileExplorerMimesType'));

// hacks delete action



$plugin->deleteRender();



?>