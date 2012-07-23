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

/**
 * Check Lock for file and send error
 *
 * @param $folder
 * @param string $file optionnal
 * @param string $error_output_format json, direct (default=json)
 */
function edmCheckLock($folder, $file="", $error_output_format='json')
{
    global $nuts, $upload_pathX;


    $sql_added = "";
    if(!empty($file))
        $sql_added = " AND File = '$file'";

    $sql = "SELECT
                    ID,
                    Folder,
                    File,
                    (SELECT Login FROM NutsUser WHERE ID = NutsUserID) AS Login
            FROM
                    NutsEDMLock
            WHERE
                    Deleted = 'NO' AND
                    (Folder LIKE '$folder%' OR Folder = '$folder')
                    $sql_added
            LIMIT 1";
    $nuts->doQuery($sql);

    if($nuts->dbNumRows() == 1)
    {
        $rec = $nuts->dbFetch();
        $cfile = $rec['Folder'].$rec['File'];
        $cfile = str_replace($upload_pathX, '', $cfile);

        $msg = translate("File lock exists")." `/$cfile`";

        if($error_output_format == 'json')
            systemError($msg);
        else
            die($msg);
    }
}

/**
 * Check if file is locked
 *
 * @param string $file_path
 * @return bool
 */
function edmFileIsLocked($file_path)
{
    global $nuts;

    $folder = str_replace(basename($file_path), '', $file_path);
    $file = basename($file_path);

    $sql = "SELECT
                    ID
            FROM
                    NutsEDMLock
            WHERE
                    Deleted = 'NO' AND
                    Folder = '$folder' AND
                    File = '$file'
            LIMIT 1";
    $nuts->doQuery($sql);

    if($nuts->dbNumRows() == 1)
        return true;

    return false;
}

/**
 * Return lock info
 *
 * @param $file_path
 * @return string
 */
function edmGetLockInfo($file_path)
{
    $info = "";

    global $nuts;

    $folder = str_replace(basename($file_path), '', $file_path);
    $file = basename($file_path);

    $sql = "SELECT
                    Date,
                    (SELECT Login FROM NutsUser WHERE ID = NutsUserID) AS Login
            FROM
                    NutsEDMLock
            WHERE
                    Deleted = 'NO' AND
                    Folder = '$folder' AND
                    File = '$file'
            ORDER BY
                    Date DESC
            LIMIT 1";
    $nuts->doQuery($sql);

    if($nuts->dbNumRows() == 1)
    {
        $r = $nuts->dbFetch();

        $date = $nuts->db2Date($r['Date']);
        $info = $r['Login']." (".$date.")";
    }


    return $info;
}


/**
 * Return files array locked
 *
 * @param $folder
 * @return array
 */
function edmGetFilesLocked($folder)
{
    global $nuts;

    $sql = "SELECT
                    File
            FROM
                    NutsEDMLock
            WHERE
                    Deleted = 'NO' AND
                    Folder = '$folder'";
    $nuts->doQuery($sql);

    $files = array();
    while($r = $nuts->dbFetch())
        $files[] = $folder.$r['File'];

    return $files;
}


if(!function_exists('glob_recursive'))
{

    // Does not support flag GLOB_BRACE
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }
}


?>