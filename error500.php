<?php

include('nuts/config.inc.php');
include('nuts/headers.inc.php');

?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html>
<head>
        <meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
		<title>Nuts: 500 Internal Server Error</title>
		<link rel="stylesheet" type="text/css" href="/nuts/css/style.css" media="all" />
        <link rel="stylesheet" type="text/css" href="/nuts/css/custom.css" media="all" />
		<link rel="icon" href="/favicon.ico" />
</head>
<body>
	 <div id="header">
        <?php if(BACKOFFICE_LOGO_URL != ''){ ?>
	        <a href="/"><img src="<? echo BACKOFFICE_LOGO_URL; ?>" align="middle" alt="nuts logo" /></a>
        <?php } else { ?>
            <a href="/"><img src="/nuts/img/logo.png" align="middle" alt="nuts logo" /></a>
        <?php } ?>
    </div>

	<div id="menu" style="height:20px;"></div>
	
	<div id="content">
		<h1 style="padding-top: 10px"><img src="/nuts/img/icon-error.gif" alt=" "  align="absbottom" /> Internal Server Error</h1>
		<p>The requested URL `<?php echo @$_SERVER['HTTP_REFERER']; ?>` encountered an unexpected error.</p>
		<br />

		<div style="border:1px solid #ccc; background-color:#e5e5e5; padding:5px;">
			<img src="/nuts/img/icon-previous.png" alt=" " align="absbottom" /> <a href="javascript:history.back();">Return to previous page</a><br />
			<img src="/nuts/img/icon-previous.png" alt=" " align="absbottom"  /> <a href="/">Go to homepage</a><br />
		</div>
	</div>
	
</body>
</html>