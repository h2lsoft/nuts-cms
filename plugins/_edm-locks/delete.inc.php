<?php

$plugin->deleteDbTable(array('NutsEDMLock'));

if($plugin->deleteUserHasConfirmed())
{
    $sql = "DELETE FROM NutsEDMLock WHERE ID = {$_GET['ID']}";
    $nuts->doQuery($sql);
}
$plugin->deleteRender();


?>