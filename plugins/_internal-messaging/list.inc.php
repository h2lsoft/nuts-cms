<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// ajax ****************************************************************************************************************
if(ajaxerRequested())
{

	// read && unread
	if(ajaxerAction('read') || ajaxerAction('unread'))
	{
		$IDS = @explode(';', $_GET['IDS']);
		foreach($IDS as $cur_id)
		{
			$cur_id = (int)$cur_id;
			if($cur_id != 0)
			{
				$viewed = (ajaxerAction('read')) ? 'YES' : 'NO';
				$nuts->dbUpdate('NutsIM', array('Viewed' => $viewed), "NutsUserID = {$_SESSION['ID']} AND ID=$cur_id");
			}
		}

		die('ok');
	}

	// get nuts users for ac
	if(ajaxerAction('get_user'))
	{
		$str = "";

		$_GET['q'] = strip_tags($_GET['q']);
		$q = addslashes($_GET['q']);
		$q = str_replace(array('%', '_'), array('', '\\_'), $q);

		$q = explode(',', $q);
		$q = end($q);
		$q = trim($q);

		$nuts->doQuery("SELECT
							ID,
							FirstName,
							LastName
					FROM
							NutsUser
					WHERE
							Deleted = 'NO' AND
							ID != {$_SESSION['NutsUserID']} AND
							(Lastname LIKE '$q%' OR Firstname LIKE '$q%' OR Login LIKE '$q%')
					ORDER BY
							LastName, FirstName");
		while($row = $nuts->dbFetch())
		{
			$str .= "{$row['FirstName']} {$row['LastName']} (#{$row['ID']})\n";
		}

		$str = trim($str);
		die($str);
	}


	// get nb unread message
	if(ajaxerAction('nb_read'))
	{
		$nuts->doQuery("SELECT COUNT(*) FROM NutsIM WHERE NutsUserID = {$_SESSION['ID']} AND Viewed = 'NO' AND Deleted = 'NO'");
		die($nuts->dbGetOne());
	}

	exit(1);

}


$plugin->listAddButtonLabel = $lang_msg[7];

// assign table to db
$plugin->listSetDbTable('NutsIM', "
										(SELECT Email FROM NutsUser WHERE ID = NutsIM.NutsUserIDFrom) AS uEmail,
										(SELECT Avatar FROM NutsUser WHERE ID = NutsIM.NutsUserIDFrom) AS uAvatar,
										(SELECT Login FROM NutsUser WHERE ID = NutsIM.NutsUserIDFrom) AS uFrom,
										(SELECT CONCAT(Login,' (#', ID, ')') FROM NutsUser WHERE ID = NutsIM.NutsUserIDFrom) AS uFromX",
						"NutsUserID = {$_SESSION['ID']}");

// search engine
$custom_sql = "SELECT Subject AS val FROM NutsIM WHERE Deleted = 'NO' AND Subject LIKE '%[q]%' AND (NutsUserID = {$_SESSION['ID']} OR NutsUserIDFrom = {$_SESSION['ID']}) LIMIT 20";
$plugin->listSearchAddFieldTextAjaxAutoComplete('Subject', $lang_msg[1], '', '', '', '', '', '', $custom_sql);
$plugin->listSearchAddFieldBooleanX('Viewed', $lang_msg[6]);


// create fields
$plugin->listAddCol('uFrom', $lang_msg[3], '; width:10px; white-space:nowrap;', true);
$plugin->listAddCol('Date', $lang_msg[5], 'center;  width:10px; white-space:nowrap;', true);
$plugin->listAddCol('Subject', $lang_msg[2], '', false);
$plugin->listAddCol('Reply', ' ', 'center; width:10px; white-space:nowrap;', false);

// render list
$plugin->listSetFirstOrderBy('Date');
$plugin->listSetFirstOrderBySort('DESC');

// batch actions
$plugin->listAllowBatchActions = true;
$plugin->listAddBatchAction($lang_msg[23], ajaxerUrlConstruct('read'));
$plugin->listAddBatchAction($lang_msg[24], ajaxerUrlConstruct('unread'));
$plugin->listRender(50, 'hookData');



function hookData($row)
{
	global $lang_msg, $nuts;

	$replyX = urlencode($row['Subject']);
	$replyToX = urlencode($row['uFromX']);
	$row['Reply'] = '<img src="img/icon-reply.png" align="absbottom" /> '.'<a href="javascript:;" onclick="formIt(\''.$lang_msg[14].'\', \'?mod=_internal-messaging&do=add&replyID='.$row['ID'].'&reply='.$replyX.'&replyTo='.$replyToX.'\');">'.$lang_msg[13].'</a>';

	$window_title = $lang_msg[15];
	$formIt = "formIt('{$window_title}','?mod=_internal-messaging&do=view&ID={$row['ID']}');";
	// $link = '<a href="javascript:;" onclick="'.$formIt.'" title="'.$lang_msg[15].'">'.$row['Subject'].'</a>';
	$link = $row['Subject'];

	if($row['Viewed'] == 'NO')
	{
		$row['Subject'] = '<img src="img/email.png" align="absmiddle" /> '."<b>{$link}</b>";
	}
	else
	{
		$row['Subject'] = "{$link}";
	}

	// $row['Message'] = $nuts->clickable($row['Message']);
	$row['Message'] = trim($row['Message']);

	$row['Subject'] = "<a href=\"javascript:;\" onclick=\"$('#ls_tr_{$row['ID']} .list_btn_view').click();\">{$row['Subject']}</a>";
	$row['Subject'] .= '<br /><span class="mini">'.str_cut($row['Message']).'</span>';

	if($row['NutsUserIDFrom'] == $_SESSION['ID'])
	{
		// $row['Subject'] = "{$link}";
		$row['Reply'] = '';
		$row['uFrom'] = $lang_msg[19];
	}

	// add gravatar logo
	// $default = WEBSITE_URL.'/plugins/_comments/www/anonymous.gif';
    // $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($row['uEmail'])))."?d=".urlencode($default)."&s=40";
    $grav_url = $row['uAvatar'];
    if(empty($grav_url))$grav_url = WEBSITE_URL.'/nuts/img/gravatar.jpg';
	$row['uFrom'] = '<img style="border:1px solid #ccc; width:30px;height:30px;" valign="middle" src="'.$grav_url.'" /> '.ucfirst($row['uFrom']);

    // date format
    $row['Date'] = explode(':', $row['Date']);
    $row['Date'] = $row['Date'][0].':'.$row['Date'][1]; // remove seconds
    if($_SESSION['Language'] == 'fr')
        $row['Date'] = $nuts->db2Date($row['Date']);


	return $row;
}

