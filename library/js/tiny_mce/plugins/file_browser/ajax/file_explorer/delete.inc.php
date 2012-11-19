<?php
/**
 * Delete all files
 */

// controller **********************************************************************************************************
if($allowedActions['delete'] === FALSE)
    systemError(translate("Action not allowed !"));


// controller **********************************************************************************************************
$files = (array)@$_POST["files"];
for($i=0; $i < count($files); $i++)
    $files[$i] = urldecode($files[$i]);

// no files
if(!count($files))
    systemError(translate("No files to delete !"));

// folder-file restriction upload_path
foreach($files as $file)
{
    if(!preg_match("#^$upload_pathX#", $file))
        systemError(translate("Parameters files `$file` not correct !"));

    if(in_array(WEBSITE_PATH.$file, $tree_hidden_folders))
        systemError(translate("Parameters files `$file` not correct (forbidden folder) !"));
}

// folder-file exists ?
foreach($files as $file)
{
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;

    if(!$is_dir && !file_exists(WEBSITE_PATH.$file))
        systemError(translate("File doesn't exist")." `$file`");

    if($is_dir && !file_exists(WEBSITE_PATH.$file))
        systemError(translate("Folder doesn't exist")." `$file`");
}


// trigger
nutsTrigger('file-explorer::delete_before', true, "file-explorer user action delete");


// delete files
foreach($files as $file)
{
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;

    if(!$is_dir && !@unlink(WEBSITE_PATH.$file))
        systemError(translate("Deleting file failed !")." `$file => {$file[strlen($file)-1]}`");

    if($is_dir && !@recursiveDelete(WEBSITE_PATH.$file))
        systemError(translate("Deleting folder failed !")." `$file`");
}


// trigger
nutsTrigger('file-explorer::delete_success', true, "file-explorer user action delete");

$resp['result'] = 'ok';



?>