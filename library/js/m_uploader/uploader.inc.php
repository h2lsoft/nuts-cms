<?php

class UploadFileXhr {
	function save($path){
		$input = fopen("php://input", "r");
		$fp = fopen($path, "w");
		while ($data = fread($input, 1024)){
			fwrite($fp,$data);
		}
		fclose($fp);
		fclose($input);			
	}
	function getName(){
		return $_GET['qqfile'];
	}
	function getSize(){
		//$headers = apache_request_headers();
		return (int)$_SERVER['CONTENT_LENGTH'];
	}
}

class UploadFileForm {	
  function save($path){
		move_uploaded_file($_FILES['qqfile']['tmp_name'], $path);
	}
	function getName(){
		return $_FILES['qqfile']['name'];
	}
	function getSize(){
		return $_FILES['qqfile']['size'];
	}
}

/**
 * Multi Uploader
 * @param string $upload_dir path to save file
 * @param int $maxFileSize
 * @param array $allowed_ext
 * @return json
 */
function handleUpload($upload_dir, $maxFileSize, $allowed_ext){
	$uploaddir = $upload_dir;
		
	if(isset($_GET['qqfile'])){$file = new UploadFileXhr();}
	elseif(isset($_FILES['qqfile'])){$file = new UploadFileForm();}
	else {return array('success' => false);}

	$size = $file->getSize();
	if ($size == 0)
	{
		$msg = (@$_GET['lang'] == 'fr') ? 'Ficher vide.' : 'File is empty.';
		return array('success' => false, 'error' => $msg);
	}
	if ($size > $maxFileSize)
	{
		$msg = (@$_GET['lang'] == 'fr') ? "Ficher est trop lourd." : "File is too large.";
		return array('success' => false, 'error' => $msg);
	}
	
	$pathinfo = pathinfo($file->getName());
	$filename = $_SESSION['ID'].'_'.$pathinfo['filename'];
	$ext = strtolower($pathinfo['extension']);

	if(!in_array($ext, $allowed_ext))
	{
		$msg = (@$_GET['lang'] == 'fr') ? "Extension de fichier `$ext` non autorisée" : "File extension `$ext` is not allowed";
		return array('success' => false, 'error' => $msg);
	}


	// if you limit file extensions on the client side,
	// you should check file extension here too			
	while (file_exists($uploaddir.'/'. $filename . '.' . $ext)){
		$filename .= rand(10, 99);
	}
		
	$file->save($uploaddir.'/'.$filename.'.'.$ext);
	
	return array('success' => true, 'filename' => $uploaddir.'/'.$filename.'.'.$ext);
}


?>