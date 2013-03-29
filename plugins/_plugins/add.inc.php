<?php

include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{	
	$CUR_ID = $plugin->formInsert();

    // add rights for superadmin
    $plugin_name = $_POST['Name'];
    $plugin_info = NUTS_PLUGINS_PATH."/$plugin_name/info.yml";
    if(file_exists($plugin_info))
    {
        $tmp = Spyc::YAMLLoad($plugin_info);
        $plugins_rights = explode(',', $tmp['actions']);
        $plugins_rights = array_map('trim', $plugins_rights);

        foreach($plugins_rights as $plugins_right)
        {
            $f = array();
            $f['NutsMenuID'] = $CUR_ID;
            $f['NutsGroupID'] = 1;
            $f['Name'] = $plugins_right;
            $nuts->dbInsert('NutsMenuRight', $f);
        }
    }

}


?>