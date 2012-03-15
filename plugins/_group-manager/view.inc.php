<?php
/* @var $plugin Plugin */

$plugin->viewDbTable(array('NutsGroup'));

$plugin->viewAddVar('Name', $lang_msg[1]);
$plugin->viewAddVar('Description', $lang_msg[2]);

$plugin->viewAddVar('FrontofficeAccess', $lang_msg[21]);
$plugin->viewAddVar('BackofficeAccess', $lang_msg[20]);

$plugin->viewAddVar('AllowUpload', $lang_msg[16]);
$plugin->viewAddVar('AllowEdit', $lang_msg[17]);
$plugin->viewAddVar('AllowDelete', $lang_msg[18]);
$plugin->viewAddVar('AllowFolders', $lang_msg[19]);



$plugin->viewRender();



?>