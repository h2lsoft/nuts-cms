<?php

/**
 * Crypt/Decrypt a string
 *
 * @param string $str
 * @param bool $crypt
 *
 * @return crypted string
 */
function nutsCrypt($str, $crypt=true)
{
	global $nuts;

	$qID = $nuts->dbGetQueryID();

	if($crypt)
	{
		$sql = "SELECT ENCODE('".addslashes($str)."', '".NUTS_CRYPT_KEY."') AS str";
	}
	else
	{
		$sql = "SELECT DECODE('".addslashes($str)."', '".NUTS_CRYPT_KEY."') AS str";
	}

	$nuts->doQuery($sql);


	$str = $nuts->dbGetOne();
	$nuts->dbSetQueryID($qID);



	return $str;
}

/**
 * Destroy nuts session and return in login page
 */
function nutsDestroyIt($error='')
{
	$_COOKIE['NutsRemember'] = '';
	setcookie ("NutsRemember", "", time() - 3600);

	$_SESSION['NutsUserID'] = '';
	$_SESSION = array();
	unset($_SESSION);
	session_destroy();

	//header("Location: login.php");
	//exit();

	// ip_different
	$uri_added = '';
	if($error == 'ip_different')
	{
		$uri_added .= 'error=ip_different';
	}

	// redirection
	$redirect_uri = '';

	if(
		isset($_SERVER['REQUEST_URI']) &&
		$_SERVER['REQUEST_URI'] != '/nuts/index.php?mod=logout' &&
		strpos($_SERVER['REQUEST_URI'], '&do=add') === false &&
		strpos($_SERVER['REQUEST_URI'], '&do=view') === false &&
		strpos($_SERVER['REQUEST_URI'], '&do=edit') === false &&
		strpos($_SERVER['REQUEST_URI'], '&do=delete') === false

	)
	{
		if(!empty($uri_added))$uri_added .= '&';

		// remove ajax parameter
		$query_string = $_SERVER['REQUEST_URI'];
		$query_string = str_replace('&ajax=1', '', $query_string);
		$query_string = str_replace('&target=list', '', $query_string);
		$query_string = str_replace('&target=content', '', $query_string);
		$query_string = str_replace('mod=logout', '', $query_string);
		$query_string = str_replace('&popup=&', '', $query_string);
		$query_string = str_replace('parentID=&', '&', $query_string);
		$query_string = str_replace('&&', '&', $query_string);

		$uri_added .= 'r='.urlencode($query_string);
	}

	if(!empty($uri_added))$uri_added = '?'.$uri_added;
	die("<script>document.location.href='login.php$uri_added';</script>");
}

/**
 * Logout treatment
 */
function nutsLogout()
{
	nutsTrace('_system', 'logout', '');
	nutsDestroyIt();
}
