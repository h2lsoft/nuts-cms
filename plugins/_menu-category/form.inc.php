<?php
/**
 * Plugin menu-category - Form layout
 * 
 * @version 1.0
 * @date 19/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsMenuCategory'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldText('NameFr', $lang_msg[1]." FR", true, 'ucfirst');
$plugin->formAddFieldColorPicker('Color', $lang_msg[2], true, "", "", "");

if($_GET['ID'])
    $plugin->formAddFieldText('Position', "", true, 'number', '', '', '');


if($_POST)
{
    if(!$_GET['ID'])
    {
        $_POST['Position'] = $plugin->formGetMaxPosition('Position')+1;
    }
    else
    {
        // check position
        $_POST['Position'] = (int)$_POST['Position'];
        $max_position = $plugin->formGetMaxPosition('Position');
        if($_POST['Position'] < 1 ||  $_POST['Position'] > $max_position)
        {
            $msg = "Position must be between 1 and $max_position";
	        if($_SESSION['Language'] == 'fr')$msg = "Votre position doit être entre 1 et $max_position";

	        $nuts->addError('Position', $msg);
        }
    }
}





?>