<?php

$users_online = array();

// get user online last 2 minutes
$sql = "SELECT
				NutsUserID,
				(SELECT Application FROM NutsUser WHERE ID = NutsUserID ORDER BY Date DESC LIMIT 1) AS Application,
				(SELECT Avatar FROM NutsUser WHERE ID = NutsUserID) AS Avatar,
				(SELECT Login FROM NutsUser WHERE ID = NutsUserID) AS Name
		FROM
				NutsLog
		WHERE
				NutsUserID != 0 AND
				TIMESTAMPDIFF(MINUTE, Date, NOW()) <= 5 AND
				Application != 'front-office'
		GROUP BY
				NutsUserID
		ORDER BY
				Date DESC";
$nuts->doQuery($sql);
while($row = $nuts->dbFetch())
{
	$row['Application'] = str_replace(array('-','_'), ' ', $row['Application']);
	$row['Application'] = trim($row['Application']);

	// $gravatar_url = 'https://www.gravatar.com/avatar/'.md5($row['Email']).'?s=60&d=http%3A%2F%2Fwww.nuts-cms.com%2Fnuts%2Fimg%2Fgravatar.jpg';
    $gravatar_url = $row['Avatar'];
    if(empty($gravatar_url))$gravatar_url = WEBSITE_URL.'/nuts/img/gravatar.jpg';

    if($row['NutsUserID'] != $_SESSION['NutsUserID'])
	    $users_online[] = array('avatar_url' => $gravatar_url, 'Name' => $row['Name'], 'ID' => $row['NutsUserID'], 'Application' => $row['Application']);
}

$tmp = nutsUserGetData('', 'Avatar');
$gravatar_url = (empty($tmp['Avatar'])) ? WEBSITE_URL.'/nuts/img/gravatar.jpg' : $tmp['Avatar'];
$me = ($_SESSION['Language'] == 'fr') ? 'Moi' : 'Me';
$users_online[] = array('avatar_url' => $gravatar_url, 'Name' => $me." ({$_SESSION['Login']})", 'ID' => $row['NutsUserID'], 'Application' => '');

die(json_encode($users_online));
