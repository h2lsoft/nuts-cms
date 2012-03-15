<?php
/**
 * Utils for Nuts
 * @version 1.0
 */


/**
 * Verify if nuts user is logon
 *
 * @return boolean
 */
function nutsUserIsLogon(){

	if(!session_id())@session_start();

	if(
		isset($_SESSION['NutsGroupID']) && $_SESSION['NutsGroupID'] != '' &&
		isset($_SESSION['NutsUserID']) && $_SESSION['NutsUserID'] != '' &&
		isset($_SESSION['FrontofficeAccess']) && $_SESSION['FrontofficeAccess'] == 'YES'
	  )

		return true;

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
 */
function nutsUserUpdate($fields, $session_reload=true)
{
	/* @var $nuts Page */
	global $nuts;

	$NutsUserID = (int)$_SESSION['ID'];
	$nuts->dbUpdate('NutsUser', $fields, "ID=$NutsUserID", array('ID', 'Deleted'));	

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






?>