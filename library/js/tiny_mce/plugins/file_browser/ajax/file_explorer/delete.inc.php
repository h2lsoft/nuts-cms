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

    /*
    if(!$is_dir && !@unlink(WEBSITE_PATH.$file))
        systemError(translate("Deleting file failed !")." `$file => {$file[strlen($file)-1]}`");

    if($is_dir && !@recursiveDelete(WEBSITE_PATH.$file))
        systemError(translate("Deleting folder failed !")." `$file`");
    */

    // rename file to file_explorer hidden format
    if(!$is_dir)
    {
        $tmp = explode('/', $file);
        $current_path = join('/', array_slice($tmp, 0, -1));
        $new_file_name = basename($file);

        $extensions = explode('.', $new_file_name);
        $ext = end($extensions);
        $new_file_name_we = join('.', array_slice($extensions, 0, -1));

        for($i=1; $i >= -1; $i++)
        {
            $new_file_name = $new_file_name_we.".$ext.{$_SESSION['NutsUserID']}.v$i".".del";
            if(!file_exists(WEBSITE_PATH.$current_path.'/'.$new_file_name))
                break;
        }

        // preg_match .del file ?
        if(!preg_match("#.del$#", basename($file)))
        {
            // rename
            if(!@rename(WEBSITE_PATH.$file, WEBSITE_PATH.$current_path.'/'.$new_file_name))
            {
                systemError(translate("Deleting file failed !")." `$file`");
            }
            else  // reset info
            {
                @touch(WEBSITE_PATH.$current_path.'/'.$new_file_name);
            }
        }
        else
        {
            if(@$_SESSION['NutsGroupID'] != 1)
            {
                systemError("Forbidden to delete .del file");
            }
            else
            {
                if(!@unlink(WEBSITE_PATH.$file))
                    systemError(translate("Deleting file failed !")." `$file`");
            }
        }
    }
    else
    {
        $file[strlen($file)-1] = '';
        $file = trim($file);

        $tmp = explode('/', $file);
        $current_path = join('/', array_slice($tmp, 0, -1));
        $current_folder = end($tmp);

        // remove folder
        if(!preg_match("#.del$#", $current_folder))
        {
            // rename folder and touch
            for($i=1; $i >= -1; $i++)
            {
                $new_folder_name = $current_folder.".{$_SESSION['NutsUserID']}.v{$i}.del";
                if(!file_exists(WEBSITE_PATH.$current_path.'/'.$new_folder_name))
                    break;
            }

            // rename
            if(!@rename(WEBSITE_PATH.$file, WEBSITE_PATH.$current_path.'/'.$new_folder_name))
            {
                systemError(translate("Deleting folder failed !")." `$file` => ".$current_path.'/'.$new_folder_name);
                systemError(translate("Deleting folder failed !")." `$file`");
            }
            else
            {
                @touch(WEBSITE_PATH.$current_path.'/'.$new_folder_name); // reset info
            }
        }
        else
        {
            if(@$_SESSION['NutsGroupID'] != 1)
            {
                systemError("Forbidden to delete .del folder");
            }
            else
            {
                // real remove folder
                if(!@recursiveDelete(WEBSITE_PATH.$file))
                {
                    systemError(translate("Deleting folder failed !")." `$file`");
                }
            }
        }
    }
}

// trigger
nutsTrigger('file-explorer::delete_success', true, "file-explorer user action delete");

$resp['result'] = 'ok';



?>