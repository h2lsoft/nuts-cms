<?php
/**
 * cut paste
 */


// controller **********************************************************************************************************
if($allowedActions['copy_paste'] === FALSE)
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

// launch rename
foreach($files as $file)
{
    $file = urldecode($file);
    $file_path = WEBSITE_PATH.$file;
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;

    if(!$is_dir)
    {
        $current_file_name = basename($file);
        $k = 2;
        while(file_exists(WEBSITE_PATH.$folder.'/'.$current_file_name))
        {
            $ext = end(explode('.', $current_file_name));
            $current_file_name = str_replace('.'.$ext, '', basename($file)).'_'.$k.'.'.$ext;
            $k++;
        }

        // trigger
        nutsTrigger('file-explorer::copy-paste_file_before', true, "file-explorer user action copy and paste file");

        if(!@copy($file_path, WEBSITE_PATH.$folder.'/'.$current_file_name))
            systemError(translate("Error while copying file")." `$current_file_name`");

        // trigger
        nutsTrigger('file-explorer::copy-paste_file_success', true, "file-explorer user action copy and paste file");
    }
    else
    {
        $current_folder_name = basename($file);

        $k = 2;
        while(is_dir(WEBSITE_PATH.$folder.$current_folder_name))
        {
            $current_folder_name = basename($file).'_'.$k;
            $k++;
        }

        $source = WEBSITE_PATH.$file;
        $dest = WEBSITE_PATH.$folder.$current_folder_name."/";

        // trigger
        nutsTrigger('file-explorer::copy-paste_folder_before', true, "file-explorer user action copy and paste file");

        if(!@smartCopy($source, $dest))
            systemError(translate("Error while copying folder")." `$current_folder_name`<br />$source <> $dest");

        // trigger
        nutsTrigger('file-explorer::copy-paste_folder_success', true, "file-explorer user action copy and paste file");

    }

}



