<?php
/**
 * copy paste
 */

// controller **********************************************************************************************************
$files = (array)@$_POST["files"];
$folder = urldecode(@$_POST["folder"]);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';


// check path exists and no forbidden
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    $msg = "The folder path was tampered with !";
    edmLog('COPY_PASTE', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}

if(!count($files))
{
    systemError(translate("Parameters not correct !"));
}

// right access verification
if(!edmUserHasRight('WRITE', $folder))
{
    $msg = "Action not allowed !";
    edmLog('COPY_PASTE', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}

// verify extension allowed
foreach($files as $file)
{
    $file = urldecode($file);
    $ext = strtolower(end(explode('.', basename($file))));
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;
    if((!$is_dir && !is_file(WEBSITE_PATH.$file)) || ($is_dir && !is_dir(WEBSITE_PATH.$file)) || !preg_match("#^$upload_pathX#", $file) || (!$is_dir && !in_array($ext, $filetypes_exts)))
    {
        $type = ($is_dir) ? 'FOLDER' : 'FILE';
        $msg = "Parameters files `$file` not correct !";
        edmLog('COPY_PASTE', 'ERROR', $type, $msg);
        systemError(translate($msg));
    }

    // user has right for file or folder to cut it ?
    if($is_dir)
    {
        if(!edmUserHasRight('READ', $file))
        {
            $msg = "Action not allowed for folder `$file`";
            edmLog('COPY', 'ERROR', $file, $msg);
            systemError(translate($msg));
        }

        // check folder lock
        edmCheckLock($file, "", 'json');
    }
    else
    {
        $cur_folder = str_replace(basename($file), '', $file);
        if(!edmUserHasRight('READ', $cur_folder))
        {
            $msg = "Action not allowed for folder `$cur_folder`";
            edmLog('COPY', 'ERROR', $cur_folder, $msg);
            systemError(translate($msg));
        }

        // check file lock
        edmCheckLock($cur_folder, basename($file), 'json');
    }
}

// launch rename
foreach($files as $file)
{
    $file = urldecode($file);
    $file_path = WEBSITE_PATH.$file;
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;
    $type = ($is_dir) ? 'folder' : 'file';

    // file
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

        if(!@copy($file_path, WEBSITE_PATH.$folder.'/'.$current_file_name))
        {
            $msg = "Error while copying file";
            edmLog('COPY_PASTE', 'FILE', $file, "$folder.'/'.$current_file_name");
            systemError(translate($msg)." `$current_file_name`");
        }
        else
        {
            edmLog('COPY_PASTE', 'FILE', $folder.'/'.$current_file_name);
        }
    }
    // folder
    else
    {
        $current_folder_name = basename($file);
        $k = 2;
        while(is_dir(WEBSITE_PATH.$folder.$current_folder_name))
        {
            $current_folder_name = basename($file).'_'.$k;
            $k++;
        }

        $source = WEBSITE_PATH.$file."/";
        $dest = WEBSITE_PATH.$folder.$current_folder_name."/";

        if(!@smartCopy($source, $dest))
        {
            $msg = "Error while copying folder";
            edmLog('COPY_PASTE', 'FOLDER', $folder.$current_folder_name."/", "`$file` => `$folder.$current_folder_name/`");
            systemError(translate($msg)." `$current_folder_name`<br />$source <> $dest");
        }
        else
        {
            // copy target rights
            $original_source = str_replace(WEBSITE_PATH, '', $source);
            $original_dest = str_replace(WEBSITE_PATH, '', $dest);

            $parent_rights = Query::factory()->select("*")
                                              ->from('NutsEDMFolderRights')
                                              ->where("Folder = '".addslashes($folder)."'")
                                              ->executeAndGetAll();


            $sub_dirs = glob_recursive("$dest*", GLOB_ONLYDIR);
            $sub_dirs[] =  $dest;

            foreach($sub_dirs as $sub_dir)
            {
                $cur_folder = str_replace(WEBSITE_PATH, '', $sub_dir);
                if($cur_folder[strlen($cur_folder)-1] != '/') $cur_folder .= '/';

                if(count($parent_rights) == 0)
                {
                    $f = array();
                    $f['Folder'] = $cur_folder;
                    $f['Type'] = 'GROUP';
                    $f['NutsEDMGroupID'] = 0;
                    $f['NutsUserID'] = 0;
                    $f['`LIST`'] = 'NO';
                    $f['`READ`'] = 'NO';
                    $f['`MODIFY`'] = 'NO';
                    $f['`DELETE`'] = 'NO';
                    $f['`WRITE`'] = 'NO';
                    $f['`UPLOAD`'] = 'NO';

                    $nuts->dbInsert('NutsEDMFolderRights', $f);
                }
                else
                {
                    foreach($parent_rights as $parent_right)
                    {
                        $f = array();
                        $f['Folder'] = $cur_folder;
                        $f['Type'] = $parent_right['Type'];
                        $f['NutsEDMGroupID'] = $parent_right['NutsEDMGroupID'];
                        $f['NutsUserID'] = $parent_right['NutsUserID'];
                        $f['`LIST`'] = $parent_right['LIST'];
                        $f['`READ`'] = $parent_right['READ'];
                        $f['`MODIFY`'] = $parent_right['MODIFY'];
                        $f['`DELETE`'] = $parent_right['DELETE'];
                        $f['`WRITE`'] = $parent_right['WRITE'];
                        $f['`UPLOAD`'] = $parent_right['UPLOAD'];

                        $nuts->dbInsert('NutsEDMFolderRights', $f);
                    }
                }
            }
        }
    }
}




?>