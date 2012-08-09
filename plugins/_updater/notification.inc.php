<?php
/**
 *  Add plugin notification
 */


// update available ************************************************************************
$last_version = @file_get_contents("http://www.nuts-cms.com/_last-version.php");
if($last_version)
{
    if(NUTS_VERSION < $last_version)
    {
        $plugin->addSystemNotification(1, '_updater');
    }

}



?>