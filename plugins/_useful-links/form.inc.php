<?php
/**
 * Plugin useful_links - Form layout
 *
 * @version 1.0
 * @date 29/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/config.inc.php');

// sql table
$plugin->formDBTable(array('NutsUsefulLinks'));

// fields
$plugin->formAddFieldImage('Logo', 'Image', true, '', '', '', '', '', '', '', '', '', '', '', '', true, $useful_links_thumbs_width, $useful_links_thumbs_height, true, $useful_links_thumbs_background_color);
$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldtextArea('Description', $lang_msg[2], false, 'ucfirst', 'height:60px;');
$plugin->formAddFieldText('Url', $lang_msg[3], true, '', 'width:620px;', '<input type="button" class="button" id="openthumb" value="Thumbalizr..." onclick="popup2(\'http://www.thumbalizr.com\', \'Thubalizr\', 1024, 768)" />');
$plugin->formAddFieldBoolean('Visible', '', true);

if($_POST)
{
    // form assignation
    if($plugin->formModeIsAdding())
    {
        // get max position
        $_POST['Position'] = $plugin->formGetMaxPosition('Position');
        $_POST['Position'] += 1;
    }

}



