<?php

// controller ******************************************************************
if(!isset($_GET['f']))$_GET['f'] = '';


// verify extension
$exts = explode('.', $_GET['f']);
$ext = $exts[count($exts)-1];
$ext = strtolower($ext);

// pdf & other file format ?
if(in_array($ext, array('pdf', 'doc', 'xls', 'ppt')))
{
	header("Location: ".$_GET['f']);
	exit();
}

// streaming format
if(!in_array($ext, array('flv', 'mp3', 'mp4', 'aac')))
{
	$_GET['f'] = '';
}

if(empty($_GET['f']))
	die("Error: file not supported by mediaplayer");

// execution *******************************************************************





?>
<html>
	<head>
		<title>Nuts media player</title>
	</head>
	<body style ="margin:0; text-align:center; background-color:#ccc;">

	<p id='preview'>The player will show in this paragraph</p>

	<script type='text/javascript' src='swfobject.js'></script>
	<script type='text/javascript'>
	var s1 = new SWFObject('player.swf','player','630','550','9');
	s1.addParam('allowfullscreen','true');
	s1.addParam('allowscriptaccess','always');
	s1.addParam('flashvars','file=<?php echo $_GET['f'] ?>&stretching=fill&autostart=true');
	s1.write('preview');
	</script>


	</body>


</html>