<?php
/**
 * Create folder
 */

$folder_name = @urldecode($_POST['folder_name']);
$folder_dest = @urldecode($_POST['folder']);

// right access verification
if($allowedActions['create_folder'] === FALSE)
    systemError(translate("Action not allowed !"));

// parameters error
if(empty($folder_name) || empty($folder_dest))
    systemError(translate("Parameters not correct !"));

// check folder dest
if(!is_dir(WEBSITE_PATH.$folder_dest))
    systemError(translate("Folder doesn't exist")." `$folder_name`");

if(!preg_match("#^$upload_pathX#", $folder_dest))
    systemError(translate("Folder access forbidden")." `$folder_dest`");

// folder exits ?
if(is_dir(WEBSITE_PATH.$folder_dest.$folder_name))
    systemError(translate("Directory already exists !"));

// create folder
if(!@mkdir(WEBSITE_PATH.$folder_dest.$folder_name))
    systemError(translate("Creating new folder failed !"));


$resp['result'] = 'ok';





?>