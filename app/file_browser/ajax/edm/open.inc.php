<?php
/**
 * Open file
 */

// controller **********************************************************************************************************
$file = @urldecode($_GET["file"]);
$folder = str_replace(basename($file), '', $file);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';
$viewer = (@$_GET['viewer'] == 'true') ? true : false;


// check path exists and no forbidden
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    $msg = "The folder path was tampered with !";
    edmLog('OPEN', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}

// right access verification
if(!edmUserHasRight('READ', $folder))
{
    $msg = "Action not allowed !";
    edmLog('OPEN', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}

// file exists
if(!file_exists(WEBSITE_PATH.$file))
{
    $msg = "The file path was tampered with !";
    edmLog('OPEN', 'ERROR', $file, $msg);
    systemError(translate($msg));
}

if($viewer)
{
    /*$f = WEBSITE_URL.'/library/js/tiny_mce/plugins/file_browser/index.php?editor=edm&';
    $f .= 'do=exec&ajax=1&action=open&file='.urlencode($file);
    $f .= '&XPHPSESSID='.session_id();
    $f .= '&download=false';

    // Google Docs viewer
    $uri = 'http://docs.google.com/viewer?';
    $uri .= 'embedded=true';
    $uri .= '&url='.urlencode($f);

    $nuts->redirect($uri);*/
}


// download for service ?
$size = filesize(WEBSITE_PATH.$file);
$file_name = basename($file);
$file_mime = mime_content_type(WEBSITE_PATH.$file);
$file_extension = strtolower(end(explode('.', $file_name)));


if(@$_GET['download'] == 'false')
{
    // upgrade for viewer
    if($file_extension == 'doc')$file_mime = 'application/msword';
    elseif($file_extension == 'docx')$file_mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    elseif($file_extension == 'xls')$file_mime = 'application/vnd.ms-excel';
    elseif($file_extension == 'xlsx')$file_mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    elseif($file_extension == 'ppt')$file_mime = 'application/vnd.ms-powerpoint';
    elseif($file_extension == 'pptx')$file_mime = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';

    header("Content-length: ".$size);
    header("Content-type: ".$file_mime);
}
else
{
    header("Content-Type: application/force-download; name=\"".$file_name."\"");
	if($file_extension == 'zip')header('Content-Type: application/zip'); // ZIP file
    header("Content-Transfer-Encoding: binary");
    header("Content-Disposition: attachment; filename=\"".$file_name."\"");
    header("Expires: 0");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");

	header("Content-length: ".$size);
}

ob_end_flush();
readfile(WEBSITE_PATH.$file);
exit();