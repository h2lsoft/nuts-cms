<?php

if(@$_GET['iframe'])
{
	$sql = "SELECT Text FROM NutsBlock WHERE ID = {$_GET['ID']}";
	$nuts->doQuery($sql);
	$txt = $nuts->getOne();
	echo <<<EOF
		
		<html>
			<heade>
				<title>Preview</title>
				<link rel="stylesheet" type="text/css" href="../themes/default/style.css" />
			</head>
			<body>
				$txt
			<body>

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