<?php
/**
 * Rename file or folder
 */

// controller **********************************************************************************************************
if(!isset($_POST['new_filename']) || !isset($_POST['old_filename']) || !isset($_POST['folder']) || !isset($_POST['type']))
    systemError(translate("Parameters not correct !"));

if(!@in_array($_POST['type'], array('folder', 'file')))
    systemError(translate("Parameters not correct !"));

$type = $_POST["type"];
$folder = urldecode($_POST['folder']);
$new_filename = urldecode($_POST['new_filename']);
$old_filename = urldecode($_POST['old_filename']);

// right access verification
if(!edmUserHasRight('MODIFY', $folder))
{
    $msg = "Action not allowed !";
    edmLog('RENAME', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}

// check path
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    $msg = "The folder path was tampered with !";
    edmLog('RENAME', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}

if($type == 'file')
{
    // check old filename
    if(!file_exists(WEBSITE_PATH.$folder.$old_filename))
    {
        $msg = "File not exists";
        edmLog('RENAME', 'ERROR', $folder.$old_filename, $msg);
        systemError(translate($msg)." `$folder$old_filename`");
    }

    // check new filename exists !
    if(file_exists(WEBSITE_PATH.$folder.$new_filename))
    {
        $msg = "File already exists !";
        edmLog('RENAME', 'ERROR', $folder.$new_filename, $msg);
        systemError(translate($msg)." `$folder$new_filename`");
    }

    // file locked ?
    edmCheckLock($folder, $old_filename, 'json');

    $source = WEBSITE_PATH.$folder.$old_filename;
    $dest = WEBSITE_PATH.$folder.$new_filename;

    // trigger
    nutsTrigger('edm::rename-file_before', true, "edm user action rename file");

    // rename failed
    if(!@rename($source, $dest))
    {
        $msg = "Rename failed !";
        edmLog('RENAME', 'ERROR', $folder.$new_filename, $msg);
        systemError(translate($msg)." `$folder$new_filename`");
    }

    // trigger
    nutsTrigger('edm::rename-file_success', true, "edm user action rename file");

    edmLog('RENAME', 'FILE', $folder.$old_filename, $folder.$new_filename);
    $resp['result'] = 'ok';
}
elseif($type == 'folder')
{
    $new_filename = protectFolderName($new_filename);


    // check old dirname
    if(!is_dir(WEBSITE_PATH.$folder.$old_filename))
    {
        $msg = "Folder doesn't exist";
        edmLog('RENAME', 'FOLDER', $folder.$old_filename, $msg);
        systemError(translate($msg)." `$folder$old_filename`");
    }

    // right on target folder ?
    if(!edmUserHasRight('MODIFY', $folder.$old_filename))
    {
        $msg = "Action not allowed";
        edmLog('RENAME', 'ERROR', $folder.$old_filename, $msg);
        systemError(translate($msg)." `$folder$old_filename`");
    }

    // folder locked ?
    edmCheckLock($folder.$old_filename, "", 'json');


    $source = WEBSITE_PATH.$folder.$old_filename;
    $dest = WEBSITE_PATH.$folder.$new_filename;

    // trigger
    nutsTrigger('edm::rename-folder_before', true, "edm user action rename folder");


    // rename failed
    if(!@rename($source, $dest))
    {
        $msg = "Rename failed !";
        edmLog('RENAME', 'ERROR', $folder.$old_filename, $msg);
        systemError(translate("Rename failed !"));
    }

    // update folder right name
    $tmp_old = addslashes("$folder$old_filename");
    $tmp_new = addslashes("$folder$new_filename");


    $sql = "UPDATE
                    NutsEDMFolderRights
            SET
                    Folder = REPLACE(Folder, '$tmp_old', '$tmp_new')
            WHERE
                    Folder = '$tmp_old' OR Folder LIKE '$tmp_old%'";

    $nuts->doQuery($sql);

    edmLog('RENAME', 'FOLDER', $folder.$old_filename, $folder.$new_filename);
    $resp['result'] = 'ok';

    // trigger
    nutsTrigger('edm::rename-folder_success', true, "edm user action rename folder");
}






?>