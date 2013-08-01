<?php

include(NUTS_PLUGINS_PATH."/_comments/config.inc.php");

$sql = "SELECT * FROM NutsPageComment WHERE NutsPageID = {$_GET['ID']} AND Deleted = 'NO' ORDER BY Date";
$nuts->doQuery($sql);
$datas = array();
while($row = $nuts->dbFetch())
{
	$email = $row["Email"];
	$default = (empty($comments_avatar_default_image_url)) ? WEBSITE_URL.'/plugins/_comments/www/anonymous.gif': $comments_avatar_default_image_url;
	$size = $comments_avatar_size;
	$grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?d=".urlencode($default)."&s=".$size;
	$row['Avatar'] = $grav_url;

	$row['IP'] = long2ip($row['IP']);
	$row['VisibilityImage'] = ($row['Visible'] == 'YES') ? 'YES.gif' : 'icon-error.gif';

	$datas[] = $row;
}

$datas_json = $nuts->array2json($datas);

echo $datas_json;

exit(1);







?>