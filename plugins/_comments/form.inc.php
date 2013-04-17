<?php
/**
 * Plugin comments - Form layout
 * 
 * @version 1.0
 * @date 31/12/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsPageComment'));

// fields
$plugin->formAddFieldText('NutsPageID', "Page ID", true, 'number');
$plugin->formAddFieldText('Url', "Page url", false, 'url');

// info
$plugin->formAddFieldsetStart('Information');
$plugin->formAddFieldDateTime('Date', '', true);
$plugin->formAddFieldText('Name', 'Login', true);
$plugin->formAddFieldText('Email', '', true);
$plugin->formAddFieldText('Website', '', false);
$plugin->formAddFieldTextArea('Message', '', true);
$plugin->formAddFieldsetEnd();

$plugin->formAddFieldBoolean('Visible', '', true);

if($_POST)
{
    if($_POST['NutsPageID'] == 0 && empty($_POST['Url']))
    {
        $nuts->notEmpty('Url');
    }
}




?>