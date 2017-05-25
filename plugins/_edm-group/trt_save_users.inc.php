<?php
/**
 * Save users
 */


// reset record
$sql = "DELETE FROM NutsEDMGroupUser WHERE NutsEDMGroupID = $CUR_ID";
$nuts->doQuery($sql);

// save record
$done = array();
foreach($_POST['users'] as $user)
{
    $user = (int)$user;
    if($user && !in_array($user, $done))
    {
        $f = array();
        $f['NutsEDMGroupID'] = $CUR_ID;
        $f['NutsUserID'] = (int)$user;

        $nuts->dbInsert('NutsEDMGroupUser', $f);
        $done[] = $user;
    }
}

