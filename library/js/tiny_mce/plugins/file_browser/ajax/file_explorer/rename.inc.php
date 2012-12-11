<?php
/**
 * Rename file or folder
 */

// controller **********************************************************************************************************
if($allowedActions['rename'] === FALSE)
    systemError(translate("Action not allowed !"));

if(!isset($_POST['new_filename']) || !isset($_POST['old_filename']) || !isset($_POST['folder']) || !isset($_POST['type']))
    systemError(translate("Parameters not correct !"));

if(!in_array($_POST['type'], array('folder', 'file')))
    systemError(translate("Parameters not correct !"));

$type = $_POST["type"];
$folder = urldecode($_POST['folder']);
$new_filename = urldecode($_POST['new_filename']);
$old_filename = urldecode($_POST['old_filename']);

// check path
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
    systemError(translate("The folder path was tampered with !"));

if($type == 'file')
{
    // check old filename
    if(!file_exists(WEBSITE_PATH.$folder.$old_filename))
        systemError(translate("File not exists")." `$folder$old_filename`");

    // check new filename exits
    if(file_exists(WEBSITE_PATH.$folder.$new_filename))
        systemError(translate("File already exists !")." `$folder$new_filename`");

    $source = WEBSITE_PATH.$folder.$old_filename;
    $dest = WEBSITE_PATH.$folder.$new_filename;

    // trigger
    nutsTrigger('file-explorer::rename-file_before', true, "file-explorer user action rename file");

    if(!@rename($source, $dest))
    {
        systemError(translate("Rename failed!"));
    }

    // trigger
    nutsTrigger('file-explorer::rename-file_success', true, "file-explorer user action rename file");

    $resp['result'] = 'ok';
}
elseif($type == 'folder')
{
    // check old dirname
    if(!is_dir(WEBSITE_PATH.$folder.$old_filename))
        systemError(translate("Folder doesn't exist")." `$folder$old_filename`");


    $source = WEBSITE_PATH.$folder.$old_filename;
    $dest = WEBSITE_PATH.$folder.$new_filename;

    // trigger
    nutsTrigger('file-explorer::rename-folder_before', true, "file-explorer user action rename folder");

    if(!@rename($source, $dest))
        systemError(translate("Rename failed!"));

    // trigger
    nutsTrigger('file-explorer::rename-folder_success', true, "file-explorer user action rename folder");


    $resp['result'] = 'ok';

}






?>