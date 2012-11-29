<?php
/**
 * Lock / Unlock file - direct error message
 */

// controller **********************************************************************************************************
$file = @urldecode($_GET["file"]);
$folder = str_replace(basename($file), '', $file);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';
$file_only = basename($file);

// parameter error
if(!@in_array($_GET['type'], array('lock', 'unlock')))
{
    $msg = "Error: parameter type not found";
    die($msg);
}

// check path exists and no forbidden
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    $msg = "The folder path was tampered with !";
    edmLog('LOCK', 'ERROR', $folder, $msg);
    die(translate($msg));
}

// right access verification
if(!edmUserHasRight('WRITE', $folder))
{
    $msg = "Action not allowed !";
    edmLog('LOCK', 'ERROR', $folder, $msg);
    die(translate($msg));
}

// file exists
if(!file_exists(WEBSITE_PATH.$file))
{
    $msg = "The file path was tampered with !";
    edmLog('LOCK', 'ERROR', $file, $msg);
    die(translate($msg));
}

// lock ****************************************************************************************************************
if($_GET['type'] == 'lock')
{
    // already lock by another person ?
    Query::factory()->select("(SELECT CONCAT(LastName,' ', FirstName) FROM NutsUser WHERE ID = NutsEDMLock.NutsUserID) AS UserName")
                    ->from('NutsEDMLock')
                    ->where("Folder = '".addslashes($folder)."'")
                    ->where("File = '$file_only'")
                    ->where("NutsUserID != {$_SESSION['NutsUserID']}")
                    ->execute();

    if($nuts->dbNumRows())
    {
        $user = $nuts->dbFetch();
        $msg = "File is already lock by ".$user['UserName'];
        edmLog('LOCK', 'ERROR', $file, $msg);
        die(translate($msg));
    }

    // lock file
    $f = array();
    $f['Date'] = 'NOW()';
    $f['NutsUserID'] = $_SESSION['NutsUserID'];
    $f['Folder'] = $folder;
    $f['File'] = $file_only;
    $nuts->dbInsert('NutsEDMLock', $f);
    edmLog('LOCK', 'FILE', $file);
}


// unlock ****************************************************************************************************************
if($_GET['type'] == 'unlock')
{
    // lock found by user ?
    Query::factory()->select("ID")
                    ->from('NutsEDMLock')
                    ->where("Folder = '".addslashes($folder)."'")
                    ->where("File = '$file_only'")
                    ->where("NutsUserID = {$_SESSION['NutsUserID']}")
                    ->execute();
    if(!$nuts->dbNumRows())
    {
        $msg = "No lock found";
        edmLog('LOCK', 'ERROR', $file, $msg);
        die(translate($msg));
    }

    // unlock file
    $f = array();
    $folderX = addslashes($folder);
    $file_onlyX = addslashes($file_only);

    $nuts->dbDelete('NutsEDMLock', "Folder = '$folderX' AND File = '$file_onlyX' AND NutsUserID = {$_SESSION['NutsUserID']}");
    edmLog('UNLOCK', 'FILE', $file);
}



die('ok');


?>