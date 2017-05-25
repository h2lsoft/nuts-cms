<?php
/**
 * Groups attachment
 */

$folder = @urldecode($_POST['folder']);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';
$groups = @$_POST['groups'];

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
$groups_arr = explode(";", $groups);

// list all groups to exclude
Query::factory()->select('NutsEDMGroupID')
                ->from('NutsEDMFolderRights')
                ->where("Folder = '".addslashes($folder)."'")
                ->where('NutsEDMGroupID != 0')
                ->where("Type = 'GROUP'")
                ->execute();

$excluded = array();
while($r = $nuts->dbFetch())
{
    $excluded[] = $r['NutsEDMGroupID'];
}

foreach($groups_arr as $groupID)
{
    $groupID = (int)$groupID;
    if($groupID)
    {
        $f = array();
        $f['Type'] = 'GROUP';
        $f['NutsEDMGroupID'] = $groupID;
        $f['Folder'] = $folder;
        $f['`LIST`'] = 'YES';
        $nuts->dbInsert('NutsEDMFolderRights', $f);


    }
}


edmLog('RIGHTS', 'FOLDER', $folder, $groups);
$resp['result'] = 'ok';

