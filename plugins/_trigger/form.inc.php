<?php
/**
 * Plugin trigger - Form layout
 *
 * @version 1.0
 * @date 19/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsTrigger'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], 'notEmpty|unique');
$plugin->formAddFieldTextArea('Description', "", false, "processed", "height:60px;");
$plugin->formAddFieldTextArea('PhpCode', "Php code", false, "php", "height:450px;");

