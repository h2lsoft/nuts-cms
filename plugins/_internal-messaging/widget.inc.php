<?php
/**
 * Plugin internal-messaging - widget
 *
 * @version 1.0
 * @date 19/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// include language file
include(Plugin::getIncludeUserLanguagePath('_internal-messaging'));


$title = $lang_msg[21];
$subject = $lang_msg[1];
$from = $lang_msg[3];
$me = $lang_msg[19];

$sql = "SELECT *, (SELECT Login FROM NutsUser WHERE ID = NutsUserIDFrom) AS Login FROM NutsIM WHERE Deleted = 'NO' AND Viewed = 'NO' AND NutsUserID = {$_SESSION['NutsUserID']} ORDER BY Date DESC LIMIT 20";
$nuts->doQuery($sql);

if(!$nuts->dbNumRows())
{
    $msg = $lang_msg[22];
    $content = '<div class="no-record"><i class="icon-mail"></i> '.$msg.'</div>';
}
else
{
    $title .= " (".$nuts->dbNumRows().")";

    $content = <<<EOF
<table>
<tr>
    <th>Date</th>
    <th>$from</th>
    <th>$subject</th>
</tr>
EOF;


    while($r = $nuts->dbFetch())
    {
        $date = ($_SESSION['Language'] == 'fr') ? $nuts->db2date($r['Date']) : $r['Date'];
        $subject = $r['Subject'];
        $login = $r['Login'];

        $content .= "<tr class=\"with_selector\" onclick=\"popupModal('index.php?mod=_internal-messaging&do=list&popup=1&parent_refresh=1');\">
                          <td style=\"white-space: nowrap; width: 30px;\">$date</td>
                          <td style=\"white-space: nowrap; width: 30px;\">$login</td>
                          <td style=\"text-align:left\"><b><i class='icon-mail' style='color: #ffcc00'></i> $subject</b></td>
                    </tr>";
    }

    $content .= "</table>";
    Plugin::dashboardAddWidget($title, 'medium', 'internal-messaging', 'full', 'max-height:120px; overflow:scroll;', $content);
}

// last connected users
    $content = <<<EOF
<table>
<tr>
    <th>Login</th>
    <th>{$nuts_lang_msg[110]}</th>
    <th>{$nuts_lang_msg[80]}</th>
    <th>Date</th>
</tr>
EOF;

$sql = "SELECT
				*,
				(SELECT Name FROM NutsGroup WHERE ID = NutsGroupID) AS NutsGroup
		FROM
				NutsUser
		WHERE
				Deleted = 'NO' AND
				LastConnection NOT IN('0000-00-00 00:00:00', '')
		ORDER BY
				LastConnection DESC
		LIMIT 10";
$nuts->doQuery($sql);
while($r = $nuts->dbFetch())
{
	$date = ($_SESSION['Language'] == 'fr') ? $nuts->db2date($r['LastConnection']) : $r['LastConnection'];
	
	$full_name = $r['FirstName'].' '.$r['LastName'];
	$full_name = ucfirst($full_name);
	$login = $r['Login'];
	$group = ucfirst($r['NutsGroup']);
	
	// avatar
	$avatar_src = WEBSITE_URL.'/nuts/img/gravatar.jpg';
	if(!empty($r['Avatar']))
		$avatar_src = $r['Avatar'];
	
	
	$content .= "<tr>
                          <td style=\"white-space: nowrap; width: 30px;\">
                            <img src='{$avatar_src}' class='img-avatar'> {$login}
                          </td>
                          <td style=\"text-align:left; white-space: nowrap; width: 30px;\">{$full_name}</td>
                          <td style=\"text-align:left; white-space: nowrap; width: 30px;\">{$group}</td>
                          <td style=\"\">{$date}</td>
                    </tr>";
	
}
$content .= "</table>";

Plugin::dashboardAddWidget($nuts_lang_msg[109], 'low', 'last-connected', 'full', 'max-height:350px; overflow:scroll;', $content);


