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


/**
 * Return dir and subdir
 * @param $dir
 */
function glob_recursiveX($dir)
{
    $dirs = array();

    if(systemIsWindows())
    {
        $dir = str_replace('/','\\', $dir);
        $cmd = "DIR $dir /A:D /B /S /O:N /N";
        $output = shell_exec($cmd);
        $output = trim($output);
        $dirs = explode("\n", $output);
    }
    else
    {

    }


    return $dirs;
}



/**
 * Get directory tree system
 *
 * @param $upload_path
 * @return array
 */
function getDirTreeX($upload_path)
{
    $dirs = glob_recursiveX($upload_path);

    $formatted_dirs = array();
    foreach($dirs as $dir)
    {
        if(systemIsWindows())
        {
            $dir = str_replace('\\', '/', $dir);
        }

        $dir = str_replace($upload_path, '/', $dir);

        if(!empty($dir) && $dir != '/' )
        {
            $dir[0] = ' ';
            $dir = trim($dir);
            // $dir = mb_convert_encoding($dir, 'utf-8');
            $tmp = explode('/', $dir);
            $formatted_dirs[] = $tmp;
        }
    }

    // new dBug($formatted_dirs);

    return $formatted_dirs;
}


/**
 * is in Windows ?
 *
 * @return bool
 */
function systemIsWindows()
{
    return (PHP_OS == 'WINNT');
}





?>