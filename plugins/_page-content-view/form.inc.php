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
$plugin->formAddFieldText('Name', "", true, "ucfirst");
$plugin->formAddFieldTextarea('Description', "", false, "ucfirst", "height:65px;");

$help = "Enter your html code here, variable are `{FieldName}`";
$plugin->formAddFieldTextarea('Html', "", true, 'html', "height:200px;", "", $help);

$help = "Enter your php code for treatment before parsing in variable `\$row`";
$plugin->formAddFieldTextarea('HookData', "", false, 'php', "height:200px;");


?>