<?php
/**
 * Upload files connector 
 */

// includes ******************************************************
include "../config.php";

// init ******************************************************

// exec ******************************************************
if(!isset($_POST['name']))die("Error: POST[name] not correct");
if(!isset($_POST['path']))die("Error: POST[path] not correct");
if(!preg_match("#^/library/media#", $_POST['path']))die("Error: path not correct");
if(!preg_match("#/$#", $_POST['path']))die("Error: path not correct");

// directory exists ?
if(!file_exists(WEBSITE_PATH.$_POST['path']))die("Error: directory `{$_POST['path']}` not exists");

// files
if(!$_FILES)die("Error: FILES not correct");
if(!isset($_FILES['file']))die("Error: FILES[file] not correct");
if(!is_uploaded_file($_FILES['file']['tmp_name']))die("Error: FILES[file] is not a uploaded file");
if($_FILES['file']['error'])die("Error: FILES[file] error detect");
if($_FILES['file']['size'] > $max_file_size_in_bytes)die("Error: file size is bigger than $max_file_size");

// @name
if(!preg_match("/^[$valid_chars_regex]+$/i", basename($_FILES['file']['name'])))die("Error: file name not allowed");

// @filetype
if(!in_array($_FILES['file']['type'], $filetypes_mimes))die("Error: file type `{$_FILES['file']['type']}` not allowed");

// @extension
$file_parts = pathinfo($_FILES['file']['name']);
if(!in_array(@$file_parts['extension'], $filetypes_exts))die("Error: file extension `{$_FILES['file']['type']}` not allowed");

// is uploaded file
if(!move_uploaded_file($_FILES['file']['tmp_name'], WEBSITE_PATH.$_POST['path'].strtolower(trim($_FILES['file']['name']))))
{
	die("Error: file `".$_POST['path'].$_FILES['file']['name']."` not uploaded");
}

die('ok');


?>