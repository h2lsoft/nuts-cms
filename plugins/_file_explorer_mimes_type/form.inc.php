<?php
/**
 * Plugin file_explorer_mimes_type - Form layout
 *
 * @version 1.0
 * @date 22/03/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsFileExplorerMimesType'));


// fields
$plugin->formAddFieldText('Extension', "", true, 'upper', 'width:50px; text-align:center;', "", "", $lang_msg[1]);
$plugin->formAddFieldTextArea('Mimes', "", true, '', 'height:250px', "", $lang_msg[2]);






?>