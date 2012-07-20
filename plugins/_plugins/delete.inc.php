<?php

$plugin->deleteDbTable(array('NutsMenu'));

if($plugin->deleteUserHasConfirmed())
{
    // remove menu really
    $sql = "DELETE FROM NutsMenu WHERE ID = {$_GET['ID']}";
    $nuts->doQuery($sql);

    // remove all rights from menu
    $sql = "DELETE FROM NutsMenuRight WHERE NutsMenuID = {$_GET['ID']}";
    $nuts->doQuery($sql);

}



$plugin->deleteRender();




?>