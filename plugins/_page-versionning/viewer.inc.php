<?php

$sql = "SELECT Content FROM NutsPageVersion WHERE ID = {$_GET['ID']}";
$nuts->doQuery($sql);
$txt = $nuts->dbGetOne();
echo <<<EOF
		
		<html>
			<heade>
				<title>{$lang_msg[3]}</title>
				<link rel="stylesheet" type="text/css" href="../themes/default/style.css" />
			</head>
			<body>
$txt
			<body>

		</html>

EOF;
exit();





?>