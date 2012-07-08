<?php

/**
 * Return User right on folder
 *
 * @param $right
 * @param $folder
 * @return bool
 */
function edmUserHasRight($right, $folder)
{
    // control on folder
    if($folder[strlen($folder)-1] != '/')
        $folder .= '/';

    // administrator has all rights
    if(EDM_ADMINISTRATOR == true)
        return true;

    global $nuts;

    // get group allowed by user
    $sql = "SELECT
                    NutsEDMGroupID
            FROM
                    NutsEDMGroup,
                    NutsEDMGroupUser
            WHERE
                    NutsEDMGroup.ID = NutsEDMGroupUser.NutsEDMGroupID AND
                    NutsEDMGroupUser.NutsUserID = {$_SESSION['NutsUserID']} AND
                    NutsEDMGroup.Deleted = 'NO' AND
                    NutsEDMGroupUser.Deleted = 'NO'";
    $nuts->doQuery($sql);

    $group_user = '';
    while($r = $nuts->dbFetch())
    {
        if(!empty($group_user))$group_user .= ', ';
        $group_user .= $r['NutsEDMGroupID'];
    }

    $sql_added = '';
    if(!empty($group_user))
        $sql_added = " OR (Type = 'GROUP' AND NutsEDMGroupID IN($group_user)) ";

    // right for everybody, user or group
    Query::factory()->select('Folder')
                    ->from('NutsEDMFolderRights')
                    ->where("`$right` = 'YES'")
                    ->where("Folder = '$folder'")
                    ->where("(
                                (Type = 'GROUP' AND NutsEDMGroupID = 0 AND NutsUserID = 0) OR
                                (Type = 'USER' AND NutsUserID = {$_SESSION['NutsUserID']})
                                $sql_added
                              )")
                    ->order_by('Folder')
                    ->execute();

    if($nuts->dbNumRows() >= 1)
        return true;

    return false;
}



/**
 * Log user action in EDM Logs
 *
 * @param $action
 * @param $object
 * @param $object_name
 * @param string $resume
 */
function edmLog($action, $object, $object_name, $resume="")
{
    global $nuts;

    $f = array();
    $f['Date'] = 'NOW()';
    $f['NutsUserID'] = $_SESSION['NutsUserID'];
    $f['Action'] = $action;
    $f['Object'] = $object;
    $f['ObjectName'] = $object_name;
    $f['Resume'] = $resume;

    $nuts->dbInsert('NutsEDMLogs', $f);
}



?>