<?php
/**
 * Plugin _cron-list - Form layout
 * 
 * @version 1.0
 * @date 16/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsCron'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldTextArea('Description', "", true, 'ucfirst');
$plugin->formAddFieldTextAjaxAutoComplete('Type', "", false);

$val = "wget --delete-after \"".WEBSITE_URL."/cron/your_path"."\"";
$plugin->formAddFieldTextarea('Command', $lang_msg[2], true, "", "", "", "", $val);

if($_POST)
{
    $_POST['Type'] = ucfirst($_POST['Type']);

}


?>