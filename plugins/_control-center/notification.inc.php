<?php
/**
 *  Add plugin notification
 */


// alert 404 & error tags *********************************************************
$sql = "SELECT COUNT(*) FROM NutsLog WHERE Application = '_fo-error' AND Deleted = 'NO'";
$nuts->doQuery($sql);
$c = (int)$nuts->dbGetOne();

if($c > 0)
{

    $msg = "error(s) detected in front-office";
    if($_SESSION['Language'] == 'fr')
        $msg = "erreur(s) détectées en front-office";

    Plugin::dashboardAddNotification('error', "$c $msg");
}






?>