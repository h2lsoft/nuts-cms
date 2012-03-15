<?php

@session_start();

if(isset($_COOKIE['NutsRemember']) && !empty($_COOKIE['NutsRemember']))
{
	$v = $_COOKIE['NutsRemember'];
	$v = strrev($v);
	$v = base64_decode($v);
	$vs = explode('|||', $v);
	$vs = $nuts->xssProtect($vs);

	if(count($vs) == 2)
	{
		$nuts->dbSelect("SELECT
								ID
						 FROM
								NutsUser
						WHERE
								Login = '%s' AND
								Password = ENCODE('%s', '".NUTS_CRYPT_KEY."') AND
								Active = 'YES' AND
								Deleted = 'NO'", $vs);
		if($nuts->dbNumRows() == 1)
		{
			$_SESSION['NutsUserID'] = (int)$nuts->dbGetOne();
		}
	}
}


// user verification
if(!isset($_SESSION['NutsUserID']) || $_SESSION['NutsUserID'] == '')
{
	nutsDestroyIt();
}
// verify record and IP
$_SESSION['NutsUserID'] = (int)$_SESSION['NutsUserID'];
$sql = "SELECT
				NutsUser.ID,
				NutsUser.Login,

				NutsUser.Gender,
				NutsUser.FirstName,
				NutsUser.LastName,
				NutsUser.NutsGroupID,
				NutsUser.Timezone,
				NutsUser.Country,

				NutsGroup.FrontofficeAccess,

				NutsGroup.TinyMceConfig,

				NutsGroup.AllowUpload,
				NutsGroup.AllowEdit,
				NutsGroup.AllowDelete,
				NutsGroup.AllowFolders,

				Language,
				Email
		FROM
				NutsUser,
				NutsGroup
		WHERE
				NutsUser.NutsGroupID = NutsGroup.ID AND
				NutsUser.ID = {$_SESSION['NutsUserID']} AND
				NutsUser.Active = 'YES' AND
				NutsGroup.Deleted = 'NO' AND
				NutsGroup.BackofficeAccess = 'YES' AND
				NutsUser.Deleted = 'NO'";
$nuts->doQuery($sql);
if($nuts->dbNumrows() != 1)
{
	nutsDestroyIt();
}
else
{
	$tmp_id = $_SESSION['NutsUserID'];
	$last_form_percent_rID = (int)@$_SESSION['FormPercentRecordID'];
	$serial = array();
	if(@is_array($_SESSION['FormPercentParams']))
	{
		$serial = $_SESSION['FormPercentParams'];
	}
	
	// preserve keys
	$tmp_keys = array();
	foreach($nuts_session_preserve_keys as $tmp_key)
	{
		if(isset($_SESSION[$tmp_key]))
			$tmp_keys[$tmp_key] = $_SESSION[$tmp_key];
	}
	
	//session_regenerate_id();
	$_SESSION = $nuts->dbFetch();
	$_SESSION['NutsUserID'] = $tmp_id;
	
	if($last_form_percent_rID)
	{
		$_SESSION['FormPercentRecordID'] = $last_form_percent_rID;
		$_SESSION['FormPercentParams'] = $serial;
	}
	
	foreach($tmp_keys as $tmp_key => $tmp_val)
	{
		$_SESSION[$tmp_key] = $tmp_val;
	}
	

	// verify ip only one
	$IP = $nuts->getIP();

	$nuts->doQuery("SELECT
							IP
					FROM
							NutsLog
					WHERE
							NutsGroupID = {$_SESSION['NutsGroupID']} AND
							NutsUserID = {$_SESSION['ID']}
					ORDER BY
							ID DESC
					LIMIT 1");
	if($nuts->dbNumrows() == 1)
	{
		$sqlIP = $nuts->dbGetOne();
		$sqlIP = long2ip($sqlIP);
		if($sqlIP != $IP)
		{
			nutsDestroyIt();
		}
	}
}



?>