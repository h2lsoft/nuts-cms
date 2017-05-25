<?php
/**
 * Create folder
 */

$folder_name = @urldecode($_POST['folder_name']);
$folder_dest = @urldecode($_POST['folder']);

$folder_name = protectFolderName($folder_name);

// right access verification
if(!edmUserHasRight('WRITE', $folder_dest))
{
    $msg = "Action not allowed !";
    edmLog('WRITE', 'ERROR', $folder_dest, $msg);
    systemError(translate($msg));
}

// parameters error
if(empty($folder_name) || empty($folder_dest))
{
    $msg = "Parameters not correct !";
    edmLog('WRITE', 'ERROR', $msg);
    systemError(translate($msg));
}

// check folder dest
if(!is_dir(WEBSITE_PATH.$folder_dest))
{
    edmLog('WRITE', 'ERROR', $folder_name, "Folder doesn't exist");
    systemError(translate("Folder doesn't exist")." `$folder_name`");
}

if(!preg_match("#^$upload_pathX#", $folder_dest))
{
    edmLog('WRITE', 'ERROR', $folder_dest, "Folder access forbidden");
    systemError(translate("Folder access forbidden")." `$folder_dest`");
}

// folder exits ?
if(is_dir(WEBSITE_PATH.$folder_dest.$folder_name))
{
    $msg = "Directory already exists !";
    edmLog('WRITE', 'ERROR', $folder_dest, $msg." `$folder_dest.$folder_name`");
    systemError(translate($msg));
}

## trigger ##
nutsTrigger('edm::create-folder_before', true, "edm user action create folder");

// create folder
if(!@mkdir(WEBSITE_PATH.$folder_dest.$folder_name))
{
    $msg = "Creating new folder failed !";
    edmLog('WRITE', 'ERROR', $folder_dest, $msg." `$folder_dest.$folder_name`");
    systemError(translate($msg));
}

// get parent rights
$parent_rights = Query::factory()->select("
                                                Type,
                                                NutsEDMGroupID,
                                                NutsUserID,
                                                Folder,
                                                `LIST`,
                                                `READ`,
                                                `MODIFY`,
                                                `DELETE`,
                                                `WRITE`,
                                                `UPLOAD`
                                           ")
                                ->from('NutsEDMFolderRights')
                                ->where("Folder = '".addslashes($folder_dest)."'")
                                ->executeAndGetAll();

if(count($parent_rights) == 0)
{
    $f = array();
    $f['Folder'] = $folder_dest.$folder_name.'/';
    $f['Type'] = 'GROUP';
    $f['NutsEDMGroupID'] = 0;
    $f['NutsUserID'] = 0;
    $f['`LIST`'] = 'NO';
    $f['`READ`'] = 'NO';
    $f['`MODIFY`'] = 'NO';
    $f['`LIST`'] = 'NO';
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
        $f['Folder'] = $folder_dest.$folder_name.'/';
        $f['Type'] = $parent_right['Type'];
        $f['NutsEDMGroupID'] = $parent_right['NutsEDMGroupID'];
        $f['NutsUserID'] = $parent_right['NutsUserID'];
        $f['`LIST`'] = $parent_right['LIST'];
        $f['`READ`'] = $parent_right['READ'];
        $f['`MODIFY`'] = $parent_right['MODIFY'];
        $f['`LIST`'] = $parent_right['LIST'];
        $f['`DELETE`'] = $parent_right['DELETE'];
        $f['`WRITE`'] = $parent_right['WRITE'];
        $f['`UPLOAD`'] = $parent_right['UPLOAD'];

        $nuts->dbInsert('NutsEDMFolderRights', $f);
    }
}




edmLog('WRITE', 'FOLDER', $folder_dest.$folder_name);

$resp['result'] = 'ok';

## trigger ##
nutsTrigger('edm::create-folder_success', true, "edm user action create folder");



