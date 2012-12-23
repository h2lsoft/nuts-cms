<?php
/**
 * Upload files from url
 */


// controller **********************************************************************************************************
if($allowedActions['upload'] === FALSE)
    systemError(translate("Action not allowed !"));


// exec ******************************************************
if(!isset($_POST['file_url']))die("Error: POST[file_url] not correct");

// simulate files array
$temp_file = tempnam(sys_get_temp_dir(), 'ufu');
if(!@file_put_contents($temp_file, file_get_contents($_POST['file_url'])))
{
    $_lang = @strtolower($_POST['lang']);
    $msg = ($_lang != 'fr') ? "Error: downloading file error" : "Erreur: téléchargement du fichier erreur";
    die($msg);
}


$_FILES = array();
$_FILES['file'] = array();
$_FILES['file']['name'] = basename($_POST['file_url']);
$_FILES['file']['error'] = 0;
$_FILES['file']['size'] = filesize($temp_file);
$_FILES['file']['tmp_name'] = $temp_file;
$_FILES['file']['type'] = mime_content_type($temp_file);


// trigger
nutsTrigger('file-explorer::upload_from_url_before', true, "file-explorer user upload file from url");
$_POST['path'] = urldecode($_POST['path']);
$_POST['name'] = basename($_POST['file_url']);

define('UPLOAD_FROM_URL', 1);



include('upload.inc.php');






?>