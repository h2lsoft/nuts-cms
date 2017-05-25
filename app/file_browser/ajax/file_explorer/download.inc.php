<?php
/**
 * Download file
 */

// controller **********************************************************************************************************
$folder = urldecode($_GET['folder']);
$file = urldecode($_GET['file']);

// check path
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
	systemError(translate("The folder path was tampered with !"));

// check filename
if(!file_exists(WEBSITE_PATH.$folder.$file))
	systemError(translate("File not exists")." `$folder$file`");


nutsTrigger('file-explorer::download-file_before', true, "file-explorer user action download file");

$path = WEBSITE_PATH.$folder.$file;

header("Content-Type: application/force-download; name=\"".$file."\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($path));
header("Content-Disposition: attachment; filename=\"".$file."\"");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
readfile($path);
exit();



