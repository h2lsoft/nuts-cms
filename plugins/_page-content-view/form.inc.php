<?php
/**
 * Plugin page-content-view - Form layout
 * 
 * @version 1.0
 * @date 20/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsPageContentView'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], true, "ucfirst");
$plugin->formAddFieldTextarea('Description', "", false, "ucfirst", "height:65px;");
$plugin->formAddFieldTextarea('Html', "", true, 'html', "height:200px;", "", $lang_msg[3]);
$plugin->formAddFieldTextarea('HookData', "", false, 'php', "height:200px;", "", $lang_msg[4]);


?>