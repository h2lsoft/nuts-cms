<?php
/**
 * Nuts Rss generator
 *
 * @version 1.0
 * @date 2010/07/06
 * @author H2lsoft
 *
 */

// controller *************************************************************************
$_GET['ID'] = (int)@$_GET['ID'];

// includes *************************************************************************
include("../../nuts/config.inc.php");
include(WEBSITE_PATH."/nuts/headers.inc.php");

// execution *************************************************************************
$plugin = new NutsCore();
$plugin->dbConnect();

// verify rss exists
$sql = "SELECT * FROM NutsRss WHERE ID = {$_GET['ID']} AND Deleted = 'NO'";
$plugin->doQuery($sql);
if($plugin->dbNumRows() == 0)
{
	echo "Error: Rss #{$_GET['ID']} not found";
}
else
{
	$rec = $plugin->dbFetch();
	$rec['PhpCode'] = trim($rec['PhpCode']);
	$rec['HookFunction'] = trim($rec['HookFunction']);

	// rss flux
	header('Content-Type: application/rss+xml');

	$rss_desc = array();
	$rss_desc['title'] = $rec['RssTitle'];
	$rss_desc['link'] = $rec['RssLink'];
	$rss_desc['description'] = $rec['RssDescription'];
	$rss_desc['copyright'] = $rec['RssCopyright'];
	$rss_desc['image_url'] = $rec['RssImage'];

	// fetch items
	$sql = $rec['Query'];

	// php code
	$sql_added = '';
	if(!empty($rec['PhpCode']))
		eval($rec['PhpCode']);
	$sql = str_replace('[sql_added]', $sql_added, $sql);
	$sql .= "\nLIMIT {$rec['RssLimit']}";

	$plugin->doQuery($sql);

	$rss_items = array();
	while($row = $plugin->dbFetch())
	{
		if(!empty($rec['HookFunction']))
			eval($rec['HookFunction']);

		$rss_items[] = array(
								'title' => $row['title'],
								'link' => $row['link'],
								'pubDate' => $row['pubDate'],
								'description' => $row['description']
							);
	}

	$plugin->rssWrite($rss_desc, $rss_items, 'UTF-8');


}


$plugin->dbClose();


