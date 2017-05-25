<?php

/*@var $plugin Plugin */
if(@$_GET['iframe'])
{
	$sql = "SELECT Body FROM NutsNewsletter WHERE ID = {$_GET['ID']}";
	$nuts->doQuery($sql);
	$txt = $nuts->dbGetOne();
	echo $txt;
	exit();
}

$plugin->viewDbTable(array('NutsNewsletter'));

$t = time();
$plugin->viewAddSQLField("CONCAT(
									'<iframe src=\"?mod=_newsletter&do=view&t=$t&iframe=1&ID=',ID,'\" id=\"preview\" style=\"width:850px; height:750px;\">',
									'</iframe>'
							   ) AS Text");

$plugin->viewAddVar('Text', '&nbsp;');
$plugin->viewRender();



