<?php

// get all meta keywords
$q = urldecode($_GET['q']);
$q = str_replace('%', '', $q);
$q = str_replace('_', '\\_', $q);
$q = str_replace("'", "\'", $q);

$sql = "SELECT MetaKeywords FROM NutsPage WHERE MetaKeywords LIKE '%$q%' LIMIT 20";
$nuts->doQuery($sql);

$tags = "";
$tags_done = array();
while($row = $nuts->dbFetch())
{
	$tmp_arr = explode(',', $row['MetaKeywords']);
	$tmp_arr = array_map('trim', $tmp_arr);

	for($i=0; $i < count($tmp_arr); $i++)
	{
		if(!in_array($tmp_arr[$i], $tags_done) && stristr($tmp_arr[$i], urldecode($_GET['q'])))
		{
			$tags = "{$tmp_arr[$i]}\n";
			$tags_done[] = $tmp_arr[$i];
		}
	}
}

$tags = trim($tags);
die($tags);


