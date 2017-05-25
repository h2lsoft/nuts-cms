<?php
/**
 * Delete all files
 */

// controller **********************************************************************************************************
$folder_param = @urldecode($_POST["folder"]);

$files = (array)@$_POST["files"];
for($i=0; $i < count($files); $i++)
    $files[$i] = urldecode($files[$i]);

// no files
if(!count($files))
    systemError(translate("No files to delete !"));

// folder restriction upload_path
if(!preg_match("#^$upload_pathX#", $folder_param))
{
    $msg = "Parameters folder `$folder_param` not correct !";
    edmLog('DELETE', 'ERROR', $folder_param, $msg);
    systemError(translate($msg));
}


// same folder parent
$folder_dest = "";
foreach($files as $file)
{
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;

    // get parent folder
    if(empty($folder_dest))
    {
        if($is_dir)
            $folder_dest = $file;
        else
            $folder_dest = str_replace(basename($file), "", $file);
    }

    // folder-file restriction upload_path
    if(!preg_match("#^$upload_pathX#", $file))
    {
        $msg = "Parameters files path `$file` not correct !";
        edmLog('DELETE', 'ERROR', $folder_dest, $msg);
        systemError(translate($msg));
    }

    // folder same root
    if($is_dir)
    {
        if($file == $folder_param)
        {
            $msg = "Parameters folder `$file` folder not correct !";
            edmLog('DELETE', 'ERROR', $folder_dest, $msg);
            systemError(translate($msg));
        }
    }
    else
    {
        $cur_folder = str_replace(basename($file), "", $file);
        if($cur_folder != $folder_param)
        {
            $msg = "Parameters files `$file` folder not correct !";
            edmLog('DELETE', 'ERROR', $folder_dest, $msg);
            systemError(translate($msg));
        }
    }
}

// right access verification
if(!edmUserHasRight('DELETE', $folder_dest))
{
    $msg = "Action not allowed !";
    edmLog('DELETE', 'ERROR', $folder_dest, $msg);
    systemError(translate($msg));
}

// folder-file exists ?
foreach($files as $file)
{
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;

    if(!$is_dir && !file_exists(WEBSITE_PATH.$file))
    {
        $msg = "File doesn't exist";
        edmLog('DELETE', 'ERROR', $folder_dest, $msg);
        systemError(translate($msg)." `$file`");
    }

    if($is_dir && !is_dir(WEBSITE_PATH.$file))
    {
        $msg = "Folder doesn't exist";
        edmLog('DELETE', 'ERROR', $folder_dest, $msg);
        systemError(translate($msg)." `$file`");
    }

    // folder countains a file locked ?
    if(!$is_dir)
    {
        $cfolder = str_replace(basename($file), '', $file);
        edmCheckLock($cfolder, basename($file), 'json');
    }
    else
    {
        $cfolder = $file;
        edmCheckLock($cfolder, "", 'json');
    }
}

// trigger
nutsTrigger('edm::delete_before', true, "edm user action delete");


// delete files
foreach($files as $file)
{
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;

    if(!$is_dir)
    {

        if(!@unlink(WEBSITE_PATH.$file))
        {
            $msg = "Deleting file failed !";
            edmLog('DELETE', 'ERROR', $folder_dest, $msg);
            systemError(translate($msg)." `$file`");
        }
        else
        {
            edmLog('DELETE', 'FILE', $file);
        }
    }
    else
    {
        $folder = $file;
        if(!@recursiveDelete(WEBSITE_PATH.$file))
        {
            $msg = "Deleting folder failed !";
            edmLog('DELETE', 'ERROR', $folder_dest, $msg);
            systemError(translate($msg)." `$file`");
        }
        else
        {
            // delete folder and subfolders rights
            $folderX = addslashes($folder);
            $nuts->dbDelete('NutsEDMFolderRights', "Folder = '$folderX' OR Folder LIKE '$folderX%'");
            edmLog('DELETE', 'FOLDER', $folder);
        }
    }
}



nutsTrigger('edm::delete_success', true, "edm user action delete");
$resp['result'] = 'ok';

