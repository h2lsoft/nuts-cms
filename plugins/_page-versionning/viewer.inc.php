<?php

$sql = "SELECT Content, NutsPageContentViewID FROM NutsPageVersion WHERE ID = {$_GET['ID']}";
$nuts->doQuery($sql);
$rec = $nuts->dbFetch();

$viewID = (int)$rec['NutsPageContentViewID'];
if($viewID > 0)
{
	$txt = ($_SESSION['Language'] == 'fr') ? 'Vue de contenu #'.$viewID : 'Content view #'.$viewID;
}
else
{
	$txt = $rec['Content'];
}

echo <<<EOF

		<!doctype html>
		<html>
			<head>
				<title>{$lang_msg[3]}</title>
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

