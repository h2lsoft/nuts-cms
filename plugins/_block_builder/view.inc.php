<?php

if(@$_GET['iframe'])
{
	$sql = "SELECT Text FROM NutsBlock WHERE ID = {$_GET['ID']}";
	$nuts->doQuery($sql);
	$txt = $nuts->getOne();
	echo <<<EOF

		<!doctype html>
		<html>
			<head>
				<title>Preview</title>
				<link rel="stylesheet" type="text/css" href="../themes/default/style.css" />
				<script type="text/javascript" src="/library/js/jquery.js"></script>
			</head>
			<body>
			$txt

			<script>
			$(document).keydown(function(e){
				code = e.keyCode ? e.keyCode : e.which;
                if(code == 27)window.close();
            });
			</script>

			</body>



		</html>

EOF;
exit();
}


$plugin->viewDbTable(array('NutsBlock'));

$t = time();
$plugin->viewAddSQLField("CONCAT(
									'<iframe src=\"?mod=_block_builder&do=view&t=$t&iframe=1&ID=',ID,'\" id=\"preview\" style=\"width:850px; height:350px;\">',
									'</iframe>'
							   ) AS Text");

$plugin->viewAddVar('Name', '');
$plugin->viewAddVar('Text', '&nbsp;');
$plugin->viewRender();




?>
