<?php
/**
 * Plugin url_redirect - Form layout
 *
 * @version 1.0
 * @date 12/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include_once(PLUGIN_PATH.'/config.inc.php');

// sql table
$plugin->formDBTable(array('NutsUrlRedirect'));

// fields
$plugin->formAddFieldText('UrlOld', WEBSITE_URL, true, '', '', '', '', '', '/');
$plugin->formAddFieldSelect('Type', '', true, $types);
$plugin->formAddFieldText('UrlNew', $lang_msg[4], false);
$plugin->formAddFieldText('Position', '', true, '', 'width:50px; text-align:center;', '', '', "Fill -1 to place at end");

if($_POST)
{
    // force type
    if($_POST['Type'] != 'gone')
    {
        $nuts->notEmpty('UrlNew');
    }
    else
    {
        $_POST['UrlNew'] = '';
    }

    // force position
    if($_POST['Position'] == -1)
        $_POST['Position'] = $plugin->formGetMaxPosition('Position')+1;
}

