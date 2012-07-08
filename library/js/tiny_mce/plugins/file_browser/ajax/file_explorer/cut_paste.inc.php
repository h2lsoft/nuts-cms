<?php
/**
 * cut paste
 */


// controller **********************************************************************************************************
if($allowedActions['cut_paste'] === FALSE)
{
    systemError(translate("Action not allowed !"));
}


// controller **********************************************************************************************************
$files = (array)@$_POST["files"];
$folder = urldecode(@$_POST["folder"]);

// check path exists and no forbidden
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
    systemError(translate("The folder path was tampered with !"));

if(in_array($folder, $paste_forbidden_folders))
    systemError(translate("The folder path was tampered with !"));

if(!count($files))
    systemError(translate("Parameters not correct !"));

// verify extension allowed
foreach($files as $file)
{
    $file = urldecode($file);
    $ext = strtolower(end(explode('.', basename($file))));

    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;

    if((!$is_dir && !is_file(WEBSITE_PATH.$file)) || ($is_dir && !is_dir(WEBSITE_PATH.$file)) || !preg_match("#^$upload_pathX#", $file) || (!$is_dir && !in_array($ext, $filetypes_exts)))
        systemError(translate("Parameters files `$file` not correct !"));
}

// lanch rename
foreach($files as $file)
{
    $file = urldecode($file);
    $file_path = WEBSITE_PATH.$file;

    if(!@rename($file_path, WEBSITE_PATH.$folder.'/'.basename($file)))
        systemError(translate("Error while moving file")." `$file`");
}




?>