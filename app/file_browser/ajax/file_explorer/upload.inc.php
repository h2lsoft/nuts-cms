<?php
/**
 * Upload files
 */

// init ******************************************************
function upload_error($num, $filename=''){

    $_lang = @strtolower($_POST['lang']);

    if($num == 1)$msg = ($_lang != 'fr') ? "Error: POST[name] not correct" : "Erreur: POST[name] non correct";
    elseif($num == 2)$msg = ($_lang != 'fr') ? "Error: POST[path] not correct": "Erreur: POST[path] non correct";
    elseif($num == 3)$msg = ($_lang != 'fr') ? "Error: path not correct": "Erreur: chemin incorrect";
    elseif($num == 4)$msg = ($_lang != 'fr') ? "Error: directory `%s` not exists": "Erreur: dossier `%s` inexistant";
    elseif($num == 5)$msg = ($_lang != 'fr') ? "Error: FILES not correct": "Erreur: non correct";

    elseif($num == 6)$msg = ($_lang != 'fr') ? "Error: FILES[file] not correct": "Erreur: FILES[file] incorrect";
    elseif($num == 7)$msg = ($_lang != 'fr') ? "Error: FILES[file] is not a uploaded file": "Erreur: FILES[file] fichier non uploadé";
    elseif($num == 8)$msg = ($_lang != 'fr') ? "Error: FILES[file] error detected": "Erreur: FILES[file] erreur détectée";
    elseif($num == 9)$msg = ($_lang != 'fr') ? "Error: file size is bigger than %s" : "Erreur: taille de fichier est plus grande que %s";

    elseif($num == 10)$msg = ($_lang != 'fr') ? "Error: file name not allowed": "Erreur: nom de fichier non autorisé";
    elseif($num == 11)$msg = ($_lang != 'fr') ? "Error: file type `%s` not allowed": "Erreur: type de fichier `%s` non autorisé";
    elseif($num == 12)$msg = ($_lang != 'fr') ? "Error: file extension `%s` not allowed": "Erreur: extension de fichier `%s` non autorisé";
    elseif($num == 13)$msg = ($_lang != 'fr') ? "Error: file `%s` not uploaded": "Erreur: fichier `%s` non uploadé";

    $msg = sprintf($msg, $filename);
	if($num == 13)
	{
		$tmp = error_get_last();
		$php_last_error = @$tmp['message'];
		$msg .= " ($php_last_error)";
	}



    die($msg);
}

// controller **********************************************************************************************************
if($allowedActions['upload'] === FALSE)
    systemError(translate("Action not allowed !"));


// exec ******************************************************
if(!isset($_POST['name']))upload_error(1);
if(!isset($_POST['path']))upload_error(2);
if(!preg_match("#^/library/media#", $_POST['path']))upload_error(3);
if(!preg_match("#/$#", $_POST['path']))upload_error(3);

// directory exists ?
if(!file_exists(WEBSITE_PATH.$_POST['path']))upload_error(4, $_POST['path']);

// files
if(!$_FILES)upload_error(5);
if(!isset($_FILES['file']))upload_error(6);

// non check for upload_from_url
if(!defined('UPLOAD_FROM_URL') && !is_uploaded_file($_FILES['file']['tmp_name']))upload_error(7);
if($_FILES['file']['error'])upload_error(8);
if($_FILES['file']['size'] > $max_file_size_in_bytes)upload_error(9, $max_file_size);

// @name
if(!preg_match("/^[$valid_chars_regex]+$/i", basename($_FILES['file']['name'])))
    upload_error(10);

// @filetype
if(!in_array($_FILES['file']['type'], $filetypes_mimes) && !in_array('*', $filetypes_mimes))
    upload_error(11, $_FILES['file']['type']);

// @extension
$file_parts = pathinfo($_FILES['file']['name']);
$file_parts['extension'] = strtolower(@$file_parts['extension']);
$extension_joker = $file_parts['extension'];
$extension_joker[strlen($extension_joker)-1] = "*";

if(!in_array(@$file_parts['extension'], $filetypes_exts)  && !in_array(@substr($extension_joker, 0, 3), $filetypes_exts) && !in_array($extension_joker, $filetypes_exts))
    upload_error(12, @$_FILES['file']['extension']);


// is uploaded file
// $file_name = utf8_decode($_FILES['file']['name']);
$file_name = $_FILES['file']['name'];
$file_name = trim($file_name);
$file_name = str_replace(' ', '-', $file_name);

// file exists ?
if(file_exists(WEBSITE_PATH.$_POST['path'].$file_name))
{
    $file_name_original_ext = strtolower(end(explode('.',$file_name)));
    $file_name_original_noext = str_replace(".$file_name_original_ext", "", $file_name);
    $k = 2;
    while(file_exists(WEBSITE_PATH.$_POST['path'].$file_name))
    {
        $file_name = $file_name_original_noext.'_'.$k.".$file_name_original_ext";
        $k++;
    }
}



// trigger
nutsTrigger('file-explorer::upload_before', true, "file-explorer user upload file");

// normal upload
if(!defined('UPLOAD_FROM_URL'))
{
    if(!@move_uploaded_file($_FILES['file']['tmp_name'], WEBSITE_PATH.$_POST['path'].$file_name))
    {
        upload_error(13, $_POST['path'].$_FILES['file']['name']);
    }
}
else
{
    if(!@rename($_FILES['file']['tmp_name'], WEBSITE_PATH.$_POST['path'].$file_name))
    {
        upload_error(13, $_POST['path'].$_FILES['file']['name']);
        @unlink($_FILES['file']['tmp_name']);
    }
}


// trigger
nutsTrigger('file-explorer::upload_success', true, "file-explorer user upload file");


die('ok');

