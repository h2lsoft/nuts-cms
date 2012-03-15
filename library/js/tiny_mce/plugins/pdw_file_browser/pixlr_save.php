<?php
/**
 * Image reception
 */

@session_start();
$root = $_SERVER['DOCUMENT_ROOT'];
include($root.'/nuts/config.inc.php');
include(WEBSITE_PATH . '/nuts/headers.inc.php');

$error = false;
$error_msg = "";


if(!nutsUserIsLogon() || @$_SESSION['AllowUpload'] != 'YES')
{
	$error = true;
	$error_msg = "Access error";
}
elseif(!isset($_GET['image']) || empty($_GET['image']))
{
	$error = true;
	$error_msg = "Parameter image";
}
elseif(!isset($_GET['type']) || empty($_GET['type']) || !in_array($_GET['type'], array('jpg', 'gif', 'png')))
{
	$error = true;
	$error_msg = "Parameter type";
}
elseif(!isset($_GET['state']) || empty($_GET['state']) || $_GET['state'] != 'replace')
{
	$error = true;
	$error_msg = "Parameter state";
}
elseif(!isset($_GET['title']))
{
	$error = true;
	$error_msg = "Parameter title";
}

// verify error image must be host by pixlr.com
if(!$error)
{
	$urls = parse_url($_GET['image']);
	if(!isset($urls['host']))
	{
		$error = true;
		$error_msg = "Parameter image host not found";
	}
	else
	{
		if(!preg_match('#pixlr\.com$#', $urls['host']))
		{
			$error = true;
			$error_msg = "Parameter image host not correct";
		}
	}
}

// verify path
if(!$error)
{
	if(!file_exists(WEBSITE_PATH.$_GET['title']))
	{
		$error = true;
		$error_msg = "File `{$_GET['title']}` doesn't exist";
	}
}


// verify dl file
if(!$error)
{
	$img = file_get_contents($_GET['image']);
	if(!$img)
	{
		$error = true;
		$error_msg = "File `{$_GET['title']}` not downloaded";
	}
	else
	{

		if(!file_put_contents(WEBSITE_PATH.$_GET['title'], $img))
		{
			$error = true;
			$error_msg = "File `{$_GET['title']}` not replaced";
		}
	}

}



if($error)
{
	echo "<script>alert(\"Error: $error_msg\");</script>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />
	<title>Save Pixlr</title>
	<script type="text/javascript">

		if(parent){
			parent.pixlr.overlay.hide();
		}
	</script>
</head>
<body bgcolor="#000000">
</body>
</html>