<?php
/**
 * Plugin user-shortcuts - Form layout
 * 
 * @version 1.0
 * @date 18/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsUserShortcut'));

// list all available plugin and translate name
$sql = "SELECT
                Name
        FROM
                NutsMenu
        WHERE
                Deleted = 'NO' AND
                ID IN(SELECT DISTINCT NutsMenuID FROM NutsMenuRight WHERE NutsGroupID = '{$_SESSION['NutsGroupID']}')";
$nuts->doQuery($sql);

$pref_language = ($_SESSION['Language'] == 'fr') ? 'fr' : 'en';

$opts = array();
while($r = $nuts->dbFetch())
{
    // get plugin name translated
    $plugin_folder_name = $r['Name'];

    if(file_exists(NUTS_PLUGINS_PATH.'/'.$plugin_folder_name.'/lang/'.$pref_language.'.inc.php'))
        include(NUTS_PLUGINS_PATH.'/'.$plugin_folder_name.'/lang/'.$pref_language.'.inc.php');
    else
        include(NUTS_PLUGINS_PATH.'/'.$plugin_folder_name.'/lang/en.inc.php');

    $plugin_name = $lang_msg[0];
    $opts[] = array('label' => $plugin_name." ($plugin_folder_name)", 'value' => $plugin_folder_name);
}
sort($opts);

$plugin->formAddFieldSelect('Plugin', "", true, $opts);

if($_POST)
{
    $_POST['NutsUserID'] = $_SESSION['NutsUserID'];

    // max position in adding mod
    if($plugin->formModeIsAdding())
    {
        $_POST['Position'] = $plugin->formGetMaxPosition('Position', 'NutsUserID', $_SESSION['NutsUserID']);
        $_POST['Position'] += 1;
    }

}




?>