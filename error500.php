<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html>
<head>
		<title>Nuts: 500 Internal Server Error</title>
		<link rel="stylesheet" type="text/css" href="/nuts/css/style.css" media="all" />
		<link rel="icon" href="/favicon.ico" />
</head>
<body>
	<img src="/nuts/img/logo.png" align="middle" alt="nuts logo" />
	<div id="menu" style="height:20px;"></div>
	
	<div id="content">
		<h1 style="padding-top: 10px"><img src="/nuts/img/list_delete.png" alt=" "  align="absbottom" /> Internal Server Error</h1>
		<p>The requested URL `<?php echo @$_SERVER['HTTP_REFERER']; ?>` encountered an unexpected error.</p>
		<br />

		<div style="border:1px solid #ccc; background-color:#e5e5e5; padding:5px;">
			<img src="/nuts/img/icon-previous.png" alt=" " align="absbottom" /> <a href="javascript:history.back();">Return to previous page</a><br />
			<img src="/nuts/img/icon-previous.png" alt=" " align="absbottom"  /> <a href="/">Go to homepage</a><br />
		</div>
	</div>
	
</body>
</html>