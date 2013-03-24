<?php
/**
 * Users attachment
 */

$folder = @urldecode($_POST['folder']);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';
$users = @$_POST['users'];

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
$users_arr = explode(";", $users);

// list all groups to exclude
Query::factory()->select('NutsUserID')
                ->from('NutsEDMFolderRights')
                ->where("Folder = '".addslashes($folder)."'")
                ->where('NutsUserID != 0')
                ->where("Type = 'USER'")
                ->execute();

$excluded = array();
while($r = $nuts->dbFetch())
{
    $excluded[] = $r['NutsUserID'];
}

foreach($users_arr as $userID)
{
    $userID = (int)$userID;
    if($userID)
    {
        $f = array();
        $f['Type'] = 'USER';
        $f['NutsUserID'] = $userID;
        $f['Folder'] = $folder;
        $f['`LIST`'] = 'YES';
        $nuts->dbInsert('NutsEDMFolderRights', $f);
    }
}


edmLog('RIGHTS', 'FOLDER', $folder, $users);
$resp['result'] = 'ok';




?>