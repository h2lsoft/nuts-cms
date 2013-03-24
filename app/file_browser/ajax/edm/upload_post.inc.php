<?php
/**
 * Upload file by post direct for applet
 */

// controller ******************************************************
$folder = @$_POST['folder'];
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';
$file = @$_POST['file'];

// check path
if(!preg_match("#^$upload_pathX#", $folder))
{
    $msg = "The folder path was tampered with !";
    edmLog('UPLOAD_LIVE', 'ERROR', $folder, $msg);
    die(translate($msg)." `$folder`");
}

// verify folder exists ?
if(!is_dir(WEBSITE_PATH.$folder))
{
    $msg = "Folder not exists";
    edmLog('UPLOAD_LIVE', 'ERROR', $folder, $msg);
    die(translate($msg));
}

// right access verification
if(!edmUserHasRight('WRITE', $folder))
{
    $msg = "Action not allowed !";
    edmLog('UPLOAD_LIVE', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}


// file exists in folder ?
$file_path = WEBSITE_PATH.$folder.$file;
if(!file_exists($file_path))
{
    $msg = "File not exists";
    edmLog('UPLOAD_LIVE', 'ERROR', $folder, $msg." `$file`");
    die(translate($msg));
}

// renbame iso by utf8 prevent system bug
if(!mb_check_encoding($file, 'utf-8'))
{
    @unlink($file_path);
    $file = utf8_encode($file);
    $file_path = WEBSITE_PATH.$folder.$file;
    usleep(200);
}


// contents
if(!isset($_POST['contents']))
{
    $msg = "Parameter error contents";
    edmLog('UPLOAD_LIVE', 'ERROR', $folder, $msg." `$file`");
    die(translate($msg));
}

// save contents
$contents = urldecode($_POST['contents']);

if(!@file_put_contents($file_path, $contents))
{
    $msg = "File writable error";
    edmLog('UPLOAD_LIVE', 'ERROR', $folder, $msg." `$file`");
    die(translate($msg));
}

edmLog('UPLOAD_LIVE', 'FILE', str_replace(WEBSITE_PATH, '', $file_path));
die('ok');




?>