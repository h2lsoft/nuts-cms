<?php
/**
 * cut paste
 */

// controller **********************************************************************************************************
$files = (array)@$_POST["files"];
$folder = urldecode(@$_POST["folder"]);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';


// check path exists and no forbidden
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    $msg = "The folder path was tampered with !";
    edmLog('CUT_PASTE', 'ERROR', $folder, $msg);
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
    edmLog('CUT_PASTE', 'ERROR', $folder, $msg);
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
        edmLog('CUT_PASTE', 'ERROR', $type, $msg);
        systemError(translate($msg));
    }

    // user has right for file or folder to cut it ?
    if($is_dir)
    {
        if(!edmUserHasRight('DELETE', $file))
        {
            $msg = "Action not allowed for folder `$file`";
            edmLog('CUT', 'ERROR', $file, $msg);
            systemError(translate($msg));
        }

        // check folder lock
        edmCheckLock($file, "", 'json');

    }
    else
    {
        $cur_folder = str_replace(basename($file), '', $file);
        if(!edmUserHasRight('DELETE', $cur_folder))
        {
            $msg = "Action not allowed for folder `$cur_folder`";
            edmLog('CUT', 'ERROR', $cur_folder, $msg);
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

    // folder
    if($is_dir)
    {
        if(!@rename($file_path, WEBSITE_PATH.$folder.basename($file)))
        {
            $msg = "Error while moving $type";
            edmLog('CUT_PASTE', 'ERROR', $file, $msg.": $file => $folder".basename($file));
            systemError(translate($msg)." `$file`");
        }
        else
        {
            // update folder and subfolders rights
            $dest = $folder.basename($file);
            if(!empty($dest) && $dest[strlen($dest)-1] != '/')$dest .= '/';

            $fileX = addslashes($file);
            $sql = "DELETE FROM NutsEDMFolderRights WHERE Folder = '$fileX' OR Folder LIKE '$fileX%'";
            $nuts->doQuery($sql);

            $original_dest = $dest;

            $parent_rights = Query::factory()->select("*")
                                            ->from('NutsEDMFolderRights')
                                            ->where("Folder = '".addslashes($folder)."'")
                                            ->executeAndGetAll();

            $sub_dirs = glob_recursive(WEBSITE_PATH.$folder.basename($file)."/*", GLOB_ONLYDIR);
            $sub_dirs[] = WEBSITE_PATH.$folder.basename($file);

            foreach($sub_dirs as $sub_dir)
            {
                $cur_folder = str_replace(WEBSITE_PATH, '', $sub_dir).'/';
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

            edmLog('CUT_PASTE', 'FOLDER', $file, "$file => $original_dest");
        }
    }
    // file
    else
    {
        if(!@rename($file_path, WEBSITE_PATH.$folder.basename($file)))
        {
            $msg = "Error while moving $type";
            edmLog('CUT_PASTE', 'ERROR', $file, $msg.": $file => $folder".basename($file));
            systemError(translate($msg)." `$file`");
        }
    }


}




?>