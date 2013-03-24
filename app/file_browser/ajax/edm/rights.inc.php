<?php
/**
 * rights folder
 */

set_time_limit(0);
ignore_user_abort(true);

$folder = @urldecode($_POST['folder']);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';

$rights = @$_POST['rights'];
$recursive = (int)@$_POST['recursive'];

// right access verification
if(EDM_ADMINISTRATOR == false)
{
    edmLog('RIGHTS', 'ERROR', $folder);
    systemError(translate("Action not allowed !"));
}

// check path
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    edmLog('RIGHTS', 'ERROR', $folder, "Folder not exists");
    systemError(translate("The folder path was tampered with !"));
}

// treatment
$right_arr = explode(CR, $rights);

$tmp = array();
foreach($right_arr as $r)
{
    $cur = explode(';', $r);
    if(count($cur) == 4)
    {
        $recID = $cur[0];
        $action = strtoupper($cur[1]);
        $state = $cur[2];
        $tmp[$recID]["`$action`"] = ($state) ? 'YES' : 'NO';
    }
}

// apply on current folder *********************************************************************************************
foreach($tmp as $recordID => $vals)
{
    // everybody creation on fly
    if(!$recordID)
    {
        $everybodyID = Query::factory()->select('ID')
                                        ->from('NutsEDMFolderRights')
                                        ->where("Type = 'GROUP'")
                                        ->where("NutsEDMGroupID = 0")
                                        ->where("NutsUserID = 0")
                                        ->where("Folder = '".addslashes($folder)."'")
                                        ->executeAndGetOne();
        if(!$everybodyID)
        {
            $f = array();
            $f['Type'] = 'GROUP';
            $f['NutsEDMGroupID'] = 0;
            $f['NutsUserID'] = 0;
            $f['Folder'] = $folder;
            $everybodyID = $nuts->dbInsert('NutsEDMFolderRights', $f, array(), true);
        }

        $recordID = $everybodyID;
    }

    $nuts->dbUpdate('NutsEDMFolderRights', $vals, "ID = $recordID");
    edmLog('RIGHTS', 'FOLDER', $folder, $_POST['rights']);
}

// recursive ? *********************************************************************************************************
if($recursive == 1)
{
    $_cache_parent = array();

    $dirs = glob_recursiveX(WEBSITE_PATH.$folder.'*');

    foreach($dirs as $dir)
    {
        $cur_folder = str_replace(WEBSITE_PATH, '', $dir).'/';
        foreach($tmp as $recordID => $vals)
        {
            // everybody creation on fly
            if(!$recordID)
            {
                $everybodyID = Query::factory()->select('ID')
                                                ->from('NutsEDMFolderRights')
                                                ->where("Type = 'GROUP'")
                                                ->where("NutsEDMGroupID = 0")
                                                ->where("NutsUserID = 0")
                                                ->where("Folder = '".addslashes($cur_folder)."'")
                                                ->executeAndGetOne();
                if(!$everybodyID)
                {
                    $f = array();
                    $f['Type'] = 'GROUP';
                    $f['NutsEDMGroupID'] = 0;
                    $f['NutsUserID'] = 0;
                    $f['Folder'] = $cur_folder;
                    $everybodyID = $nuts->dbInsert('NutsEDMFolderRights', $f, array(), true);
                }

                $recordID = $everybodyID;
            }
            else
            {
                // get parent info ?
                if(!isset($_cache_parent[$recordID]))
                {
                    $_cache_parent[$recordID] = Query::factory()->from('NutsEDMFolderRights')
                                                                ->where("ID = $recordID")
                                                                ->executeAndFetch();
                }

                // parent exists ?
                Query::factory()->select("ID")
                                ->from('NutsEDMFolderRights')
                                ->where("Folder = '".addslashes($cur_folder)."'")
                                ->where("Type = '{$_cache_parent[$recordID]['Type']}'")
                                ->where("NutsEDMGroupID = {$_cache_parent[$recordID]['NutsEDMGroupID']}")
                                ->where("NutsUserID = {$_cache_parent[$recordID]['NutsUserID']}")
                                ->limit(1)
                                ->execute();
                if(!$nuts->dbNumRows())
                {
                    $f = array();
                    $f['Type'] = $_cache_parent[$recordID]['Type'];
                    $f['NutsEDMGroupID'] = $_cache_parent[$recordID]['NutsEDMGroupID'];
                    $f['NutsUserID'] = $_cache_parent[$recordID]['NutsUserID'];
                    $f['Folder'] = $cur_folder;
                    $recordID = $nuts->dbInsert('NutsEDMFolderRights', $f, array(), true);
                }
                else
                {
                    $recordID = $nuts->dbGetOne();
                }
            }

            $nuts->dbUpdate('NutsEDMFolderRights', $vals, "ID = $recordID");
            edmLog('RIGHTS', 'FOLDER', $cur_folder, $_POST['rights']);
        }
    }
}

$resp['result'] = 'ok';
$resp['message'] = translate("Folder rights has been saved");





?>