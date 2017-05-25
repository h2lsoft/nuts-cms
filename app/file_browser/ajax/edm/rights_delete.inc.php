<?php
/**
 * rights folder delete
 */


$folder = @urldecode($_POST['folder']);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';

$rightID = (int)@$_POST['rightID'];

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

// get Type, GroupID, UserID to recursively delete rights
$parentInfo = Query::factory()->select('*')
                              ->from('NutsEDMFolderRights')
                              ->where("ID = $rightID")
                              ->executeAndFetch();
// current right
$nuts->dbDelete('NutsEDMFolderRights', "ID=$rightID");

// recursive right
$nuts->dbDelete('NutsEDMFolderRights', "Folder LIKE '$folder%' AND
                                        Type = '{$parentInfo['Type']}' AND
                                        NutsEDMGroupID = '{$parentInfo['NutsEDMGroupID']}' AND
                                        NutsUserID = '{$parentInfo['NutsUserID']}'");


$resp['result'] = 'ok';


