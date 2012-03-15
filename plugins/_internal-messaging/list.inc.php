<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// list only message for User or All Group
if(isset($_GET['action']) && $_GET['action'] == 'nb_read')
{
	$nuts->doQuery("SELECT COUNT(*) FROM NutsIM WHERE NutsUserID = {$_SESSION['ID']} AND Viewed = 'NO' AND Deleted = 'NO'");
	die($nuts->dbGetOne());
}
// get nuts user by ajax
if(isset($_GET['action']) && $_GET['action'] == 'get_user')
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



$plugin->listAddButtonLabel = $lang_msg[7];

// assign table to db
$plugin->listSetDbTable('NutsIM', "
										(SELECT Email FROM NutsUser WHERE ID = NutsIM.NutsUserIDFrom) AS uEmail,
										(SELECT Login FROM NutsUser WHERE ID = NutsIM.NutsUserIDFrom) AS uFrom,
										(SELECT CONCAT(Login,' (#', ID, ')') FROM NutsUser WHERE ID = NutsIM.NutsUserIDFrom) AS uFromX",
						"NutsUserID = {$_SESSION['ID']}");

// search engine
$custom_sql = "SELECT Subject AS val FROM NutsIM WHERE Deleted = 'NO' AND Subject LIKE '%[q]%' AND (NutsUserID = {$_SESSION['ID']} OR NutsUserIDFrom = {$_SESSION['ID']}) LIMIT 20";
$plugin->listSearchAddFieldTextAjaxAutoComplete('Subject', $lang_msg[1], '', '', '', '', '', '', $custom_sql);
$plugin->listSearchAddFieldBooleanX('Viewed', $lang_msg[6]);


// create fields
$plugin->listAddCol('uFrom', $lang_msg[3], 'center; width:10px; white-space:nowrap;', true);
$plugin->listAddCol('Date', $lang_msg[5], 'center;  width:10px; white-space:nowrap;', true);
$plugin->listAddCol('Subject', $lang_msg[2], '', false);
$plugin->listAddCol('Reply', ' ', 'center; width:10px; white-space:nowrap;', false);

// render list
$plugin->listRender(20, 'hookData');


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
	$msg = ' <a href="javascript:;" onclick="$(\'#message_'.$row['ID'].'\').toggle(\'fast\');">[ + ]</a>';
	$msg .= '<p id="message_'.$row['ID'].'" style="display:none;margin-top:0;" class="mini">'.nl2br($row['Message']).'</p>';

	$row['Subject'] .= $msg;

	if($row['NutsUserIDFrom'] == $_SESSION['ID'])
	{
		// $row['Subject'] = "{$link}";
		$row['Reply'] = '';
		$row['uFrom'] = $lang_msg[19];
	}

	// add gravatar logo
	$default = WEBSITE_URL.'/plugins/_comments/www/anonymous.gif';
	$grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($row['uEmail'])))."?d=".urlencode($default)."&s=40";
	$row['uFrom'] = '<img style="border:1px solid #ccc; width:40px; height:40px;" valign="top" src="'.$grav_url.'" /><br />'.ucfirst($row['uFrom']);


	return $row;
}



?>