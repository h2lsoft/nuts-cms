<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// uploader *************************************************************************
if(@$_GET['_action'] == 'm_upload')
{
	include(PLUGIN_PATH.'/config.inc.php');
	include(NUTS_LIBRARY_PATH."/js/m_uploader/uploader.inc.php");

	// to pass data through iframe you will need to encode all html tags
	$upload_dir = PLUGIN_PATH.'/_tmp';

	$maxFileSize = strtolower($gallery_images_allowed_max_size);
	$maxFileSize = str_replace(' ', '', $maxFileSize);
	$maxFileSize = str_replace('ko', ' * 1024', $maxFileSize);
	$maxFileSize = str_replace('mo', ' * 1024 * 1024', $maxFileSize);

	eval('$maxFileSize = '.$maxFileSize.';');

	$allowed_ext = explode(',', str_replace(' ', '', $gallery_images_allowed_exts));

	$result = handleUpload($upload_dir, $maxFileSize, $allowed_ext);
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	exit(0);

}


// execution *************************************************************************
include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$CID = $plugin->formInsert();
	include(PLUGIN_PATH.'/trt_mupload.inc.php');

}


?>