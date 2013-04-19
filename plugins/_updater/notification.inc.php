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
        $msg = "New version $last_version is vailable";
        if($_SESSION['Language'] == 'fr')
            $msg = "Nouvelle version $last_version disponible";

        Plugin::dashboardAddNotification('info', $msg);
    }

}



?>