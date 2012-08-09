<?php
/**
 *  Add plugin notification
 */


// alert 404 & error tags *********************************************************
$sql = "SELECT COUNT(*) FROM NutsLog WHERE Application = '_fo-error' AND Deleted = 'NO'";
$nuts->doQuery($sql);
$c = (int)$nuts->dbGetOne();

$plugin->addSystemNotification($c, '_control-center');




?>