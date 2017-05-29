<?php
/**
 * NutsUser
 *
 * @package Nuts-Component
 * @version 1.0
 */


/**
 * Verify if nuts user is logon
 *
 * @param string $access_type front-office or back-office (default `front-office`)
 * @return bool
 */
function nutsUserIsLogon($access_type='front-office')
{
	if(!session_id())@session_start();

	if($access_type == 'front-office')
	{
		if(
			isset($_SESSION['NutsGroupID']) && $_SESSION['NutsGroupID'] != '' &&
			isset($_SESSION['NutsUserID']) && $_SESSION['NutsUserID'] != '' &&
			isset($_SESSION['FrontofficeAccess']) && $_SESSION['FrontofficeAccess'] == 'YES'
		)
			return true;
	}
	else
	{
		if(
			isset($_SESSION['NutsGroupID']) && $_SESSION['NutsGroupID'] != '' &&
			isset($_SESSION['NutsUserID']) && $_SESSION['NutsUserID'] != ''
		)
			return true;
	}

	return false;
}

/**
 * Redirection to login page
 *
 * @param string type: login (default) or forbidden or logon
 */
function nutsAccessRestrictedRedirectPage($type='login')
{
	/* @var $nuts Page */
	global $nuts;

	if($type == 'login')
		$d_const = 'LOGIN_PAGE_URL_'.strtoupper($nuts->language);
	elseif($type == 'logon')
		$d_const = 'LOGON_PAGE_URL_'.strtoupper($nuts->language);
	elseif($type == 'forbidden')
		$d_const = 'PRIVATE_PAGE_FORBIDDEN_URL_'.strtoupper($nuts->language);

	$uri = constant($d_const);
	if($uri == '')
	{
		die("Error: `$d_const` not defined");
	}

	// login case add current page redirection
	if($type == 'login')
	{
		$cur_uri = (isset($_SERVER['SCRIPT_URL'])) ? $_SERVER['SCRIPT_URL'] : $_SERVER['REQUEST_URI'];

		if($cur_uri != $uri)
		{
			if(strpos($uri, '?') === false)
				$uri .= '?';
			$uri .= "&r=$cur_uri";
		}
	}



	$nuts->redirect($uri);

}

/**
 * User logout
 *
 * @param array $preserve_session_key
 */
function nutsUserLogout($preserve_session_key=array())
{
	/* @var $nuts Page */
	global $nuts;

	$session_keys = array();
	foreach($preserve_session_key as $pk)
	{
		if(isset($_SESSION[$pk]))
			$session_keys[$pk] = $_SESSION[$pk];
	}

	$_SESSION = $session_keys;

	$nuts->redirect(WEBSITE_URL.'/'.$nuts->language);
}

/**
 * Check user group
 *
 * @param int $NutsGroupID
 * @return boolean
 */
function nutsUserGroupIs($NutsGroupID)
{
	$res = false;

	if(@(int)$_SESSION['NutsGroupID'] == $NutsGroupID)
	{
		return true;
	}


	return $res;
}

/**
 * Return Email list for a group
 *
 * @param int $NutsGroupID
 * @return string email separated by comma
 */
function nutsGetEmail($NutsGroupID)
{
	global $nuts;

	$sql = "SELECT DISTINCT Email FROM NutsUser WHERE Deleted = 'NO' AND NutsGroupID = $NutsGroupID";
	$nuts->doQuery($sql);

	$emails = '';
	while($row = $nuts->dbFetch())
	{
		if(!empty($emails))$emails .= ',';
		$emails .= $row['Email'];
	}

	return $emails;
}

/**
 * Is Nuts User exists
 *
 * @param string $login_key must be Login or Email
 * @param string $login_value
 * @param int $CurrentUserID (optional) user exception useful for change a login for an existing user
 * @return mixed false or NutsUserID
 */
function nutsUserExists($login_key, $login_value, $CurrentUserID = 0)
{
	/* @var $nuts Page */
	global $nuts;

	$sql = "SELECT
					ID
			FROM
					NutsUser
			WHERE
					$login_key = '%s' AND
					Deleted = 'NO' AND
					ID != $CurrentUserID
			LIMIT 1";
	$nuts->dbSelect($sql, array($login_value));
	if($nuts->dbNumRows() == 1)
		return $nuts->dbGetOne();

	return false;
}

/**
 * Activate or refresh Session parameters
 *
 * @param int $NutsUserID
 * @param string $sql_fields_added (add sql fields in session)
 * @param array $preserve_session_key exception ($nuts_preserve_session_key configuration is automatically included)
 */
function nutsUserLogin($NutsUserID, $sql_fields_added = "", $preserve_session_key = array())
{
	/* @var $nuts Page */
	global $nuts, $nuts_session_preserve_keys;

	$preserve_session_key = array_merge($nuts_session_preserve_keys, $preserve_session_key);
	$preserve_session_key = array_unique($preserve_session_key);

	$NutsUserID = (int)$NutsUserID;
	if(!empty($sql_fields_added))$sql_fields_added = ', '.$sql_fields_added;
	$nuts->dbSelect("SELECT
							NutsUser.ID,
							NutsUser.ID AS uID,
							NutsUser.Login,
							NutsUser.Email,
							NutsUser.Gender,
							NutsUser.FirstName,
							NutsUser.LastName,
							NutsUser.NutsGroupID,
							NutsUser.Language,
							NutsUser.Timezone,
							NutsUser.Country,
							FrontofficeAccess,
							BackofficeAccess
							$sql_fields_added
					FROM
							NutsUser,
							NutsGroup
					WHERE
							NutsUser.NutsGroupID = NutsGroup.ID AND
							NutsUser.ID = %s AND
							NutsUser.Active = 'YES' AND
							NutsGroup.FrontofficeAccess = 'YES' AND
							NutsGroup.Deleted = 'NO' AND
							NutsUser.Deleted = 'NO'", array($NutsUserID));

	$row = $nuts->dbFetchAssoc();

	$session_keys = array();
	foreach($preserve_session_key as $pk)
	{
		if(isset($_SESSION[$pk]))
			$session_keys[$pk] = $_SESSION[$pk];
	}

	$_SESSION = $row;
	$_SESSION['NutsUserID'] = $row['ID'];
	$_SESSION = array_merge($_SESSION, $session_keys);

	$nuts->dbUpdate('NutsUser', array('LastConnection' => 'NOW()'), "ID={$_SESSION['NutsUserID']}");



}

/**
 * Initialize form from session
 *
 * @param string $sql_field_mapping
 */
function nutsUserFormInit($sql_field_mapping)
{
	/* @var $nuts Page */
	global $nuts;

	$NutsUserID = (int)$_SESSION['ID'];
	$sql = "SELECT
					$sql_field_mapping
			FROM
					NutsUser
			WHERE
					ID = $NutsUserID";
	$nuts->doQuery($sql);
	$rec = $nuts->dbfetch();
	$nuts->formInit($rec);
}

/**
 * Update current user profil
 *
 * @param array $fields
 * @param boolean $session_reload
 * @param array $exclude_fields
 */
function nutsUserUpdate($fields, $session_reload=true, $exclude_fields=array())
{
	/* @var $nuts Page */
	global $nuts;

	$exclude_fields[] = 'ID';
	$exclude_fields[] = 'Deleted';
	$exclude_fields = array_unique($exclude_fields);

	$NutsUserID = (int)$_SESSION['ID'];
	$nuts->dbUpdate('NutsUser', $fields, "ID=$NutsUserID", $exclude_fields);

	if($session_reload)
	{
		include(NUTS_PLUGINS_PATH.'/_login/config.inc.php');
		nutsUserLogin($NutsUserID, $session_add_sql_fields, $session_preserve_keys);
	}

}

/**
 * Get user data
 *
 * @param int $NutsUserID default $_SESSION['uID']
 * @param array $fields default *
 */
function nutsUserGetData($NutsUserID="", $fields="*")
{
	/* @var $nuts Page */
	global $nuts;

	if(empty($NutsUserID))
		$NutsUserID = (int)$_SESSION['NutsUserID'];

	$NutsUserID = (int)$NutsUserID;

	$nuts->doQuery("SELECT $fields FROM NutsUser WHERE ID = $NutsUserID");
	$rec = $nuts->dbFetch();

	return $rec;
}

/**
 * Verify if user is correct
 *
 * @param string $login Login verification alphanum + `_`
 * @return boolean
 */
function nustUserLoginIsValid($login)
{
	$login = str_replace('_', '', $login);
	return ctype_alnum($login);
}

/**
 * Verify if password is correct
 *
 * @param string $pass Password verification alphanum + `_` + `-`
 * @return boolean
 */
function nustUserPasswordIsValid($pass)
{
	$pass = str_replace(array('_', '-'), '', $pass);
	return ctype_alnum($pass);
}

/**
 * Register new user
 *
 * @param array fields
 * @param array fields exception (optional)
 * @return int userID
 */
function nutsUserRegister($fields, $except=array())
{
	/* @var $nuts Page */
	global $nuts;

	$fields['LastConnection'] = 'NOW()';
	$USER_ID = $nuts->dbInsert('NutsUser', $fields, $except, true);

	if(array_key_exists('Password', $fields))
		nutsUserSetPassword($USER_ID, $fields['Password']);

	return $USER_ID;
}

/**
 * Update user password
 *
 * @param int $NutsUserID
 * @param string $password
 */
function nutsUserSetPassword($NutsUserID, $password)
{
	/* @var $nuts Page */
	global $nuts;


	// $password = utf8_encode($password);

	$sql = "UPDATE
					NutsUser
			SET
					Password = ENCODE('$password', '".NUTS_CRYPT_KEY."')
			WHERE
					ID = $NutsUserID";
	$nuts->doQuery($sql);

}

/**
 * Get user password
 *
 * @param int $NutsUserID
 * @return string $password uncrypted password
 */
function nutsUserGetPassword($NutsUserID)
{
	/* @var $nuts Page */
	global $nuts;

	$sql = "SELECT
					DECODE(Password, '".NUTS_CRYPT_KEY."') AS Password
			FROM
					NutsUser
			WHERE
					ID = $NutsUserID";
	$nuts->doQuery($sql);
	$password = $nuts->dbGetOne();


	return $password;

}

/**
 * Verify right for user
 *
 * @param int $NutsGroupID
 * @param     $right
 * @param     $zoneID
 * @param int $pageID
 *
 * @return bool
 */
function nutsPageManagerUserHasRight($NutsGroupID=0, $right, $zoneID, $pageID=0)
{
	global $nuts;

	$NutsGroupID = (int)$NutsGroupID;
	if(!$NutsGroupID)$NutsGroupID = $_SESSION['NutsGroupID'];

	// superadmin allows all rights
	if($NutsGroupID == 1)return true;


	// add_main_page
	if(!$pageID)
	{
		if($right == 'add_main_page')
		{
			// main zone ?
			if(!$zoneID)
			{
				return nutsUserHasRight('', '_page-manager', 'main pages creation');
			}
			else
			{
				// groups allowed in page manager
				$zone_rights = Query::factory()->select('Rights')
					->from('NutsZone')
					->whereID($zoneID)
					->executeAndGetOne();

				$zone_rights = (array)unserialize($zone_rights);
				return in_array($NutsGroupID, $zone_rights);
			}
		}
	}
	else
	{
		// check if page author
		$authorID = Query::factory()->select('NutsUserID')->from('NutsPage')->whereID($pageID)->executeAndGetOne();
		if($authorID == $_SESSION['NutsUserID'])
			return true;


		// check if group has right
		Query::factory()->select('ID')
			->from('NutsPageRights')
			->whereEqualTo('NutsGroupID', $NutsGroupID)
			->whereEqualTo('NutsPageID', $pageID)
			->whereEqualTo('Action', $right)
			->limit(1)
			->execute();

		if($nuts->dbNumRows() == 1)
			return true;
	}


	return false;

}

/**
 * Convert user Date to GMT date depend on user date format
 * @param $date
 *
 * @return string sql_date
 */
function nutsConvertUserDateToGMT($user_date)
{
	if(empty($user_date) || $user_date == '0000-00-00 00:00:00' || $user_date == '0000-00-00')return '';

	// reformat date fr or en
	$convert_date = $user_date;
	$convert_date_sql = '';
	$user_date_type = '';
	if(preg_match('#[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}#', $convert_date))
	{
		$date_format = 'd/m/Y H:i';
		list($d, $m, $Y) = explode('/', $convert_date);
		list($Y, $hours) = explode(' ', $Y);
		list($H, $i) = explode(':', $hours);
		$hours = trim($H);
		$convert_date_sql = "$Y-$m-$d $H:$i";
	}
	elseif(preg_match('#[0-9]{2}/[0-9]{2}/[0-9]{4}#', $convert_date))
	{
		$date_format = 'd/m/Y';
		list($d, $m, $Y) = explode('/', $convert_date);
		$convert_date_sql = "$Y-$m-$d";

	}
	elseif(preg_match('#[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}#', $convert_date))
	{
		$date_format = 'Y-m-d H:i';
		$convert_date_sql = $convert_date;
	}
	elseif(preg_match('#[0-9]{4}-[0-9]{2}-[0-9]{2}#', $convert_date))
	{
		$date_format = 'Y-m-d';
		$convert_date_sql = $convert_date;
	}

	$inv_timezone = $_SESSION['Timezone'] * -1;
	$convert_date_sql = strtotime("$convert_date_sql $inv_timezone hour");
	$convert_date_gmt = date($date_format, $convert_date_sql);

	return $convert_date_gmt;
}

/**
 * Return date in GMT
 *
 * @param sql-datetime $date
 * @param string $format php date desired format
 * @param string $output user|other (user => return date with its own timezone)
 * @return date
 */
function nutsGetGMTDateUser($date, $format='', $output='user')
{
	if($date == '0000-00-00 00:00:00' || $date == '0000-00-00')return '';
	return $date;


	/*if(empty($format))
	{
		$format = 'Y-m-d H:i:s';
		if(strlen($date) == 10)
			$format = 'Y-m-d';
	}

	$timezone = $_SESSION['Timezone'];

	$year   = substr($date, 0, 4);
	$month  = substr($date, 5, 2);
	$day    = substr($date, 8, 2);
	$hour   = substr($date, 11, 2);
	$minute = substr($date, 14, 2);
	$second = substr($date, 17, 2);

	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	//Offset is in hours from gmt, including a - sign if applicable.
	//So lets turn offset into seconds
	$offset = $timezone * 60 * 60;

	if($output == 'user')
		$timestamp = $timestamp + $offset;
	else
		$timestamp = $timestamp - $offset;

	//Remember, adding a negative is still subtraction ;)
	$d = date($format, $timestamp);
	if(strlen($date) == 10)
		list($d) = explode(' ', $d);

	return $d;*/
}

/**
 * Verify if user has right
 *
 * @param int $NutsGroupID (empty = NutsGroupID in SESSION)
 * @return boolean
 */
function nutsUserHasRight($NutsGroupID='', $plugin, $right)
{
	global $nuts;

	$NutsGroupID = (int)$NutsGroupID;
	if(!$NutsGroupID)$NutsGroupID = $_SESSION['NutsGroupID'];
	
	if($NutsGroupID == 1)return true; # super admin all rights


	$sql = "SELECT
					ID
			FROM
					NutsMenuRight
			WHERE
					NutsGroupID = $NutsGroupID AND
					Name = '$right' AND
					NutsMenuID IN(SELECT ID FROM NutsMenu WHERE Name = '$plugin')
			LIMIT
					1";
	$nuts->doQuery($sql);

	if(!(int)$nuts->dbNumRows())
		return false;



	return true;
}

/**
 * Returns full name FirstName LastName of nuts user
 *
 * @param int ID optionnal = current user
 *
 * @return string
 */
function getNutsUserName($NutsUserID='')
{
	global $nuts;

	if(empty($NutsUserID))$NutsUserID = $_SESSION['NutsUserID'];

	$sql = "SELECT CONCAT(FirstName,' ', LastName) FROM NutsUser WHERE ID = $NutsUserID";
	$nuts->doQuery($sql);

	return $nuts->dbGetOne();

}

/**
 * Get an array with distinct list of email
 *
 * @param int $NutsGroupID
 * @param int $NutsUserID optionnal
 *
 * @return array
 */
function getNutsEmailList($NutsGroupID='', $NutsUserID='')
{
	global $nuts;


	$sql_added = '';
	if(!empty($NutsGroupID))
	{
		$sql_added .= "NutsGroupID = $NutsGroupID AND \n";
	}

	if(!empty($NutsUserID))
	{
		$sql_added .= "ID = $NutsUserID AND \n";
	}



	$sql = "SELECT
					DISTINCT Email
			FROM
					NutsUser
			WHERE
					$sql_added
					Deleted = 'NO'";

	$nuts->doQuery($sql);

	$arr = array();
	while($row = $nuts->dbFetch())
	{
		$arr[] = $row['Email'];
	}


	return $arr;
}
