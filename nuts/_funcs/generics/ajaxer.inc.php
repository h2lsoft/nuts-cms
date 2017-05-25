<?php
/**
 * Ajaxer
 * @package Functions
 * @version 1.0
 */

/**
 * Verify if ajaxer is requested
 * @return bool
 */
function ajaxerRequested()
{
	return (@$_GET['ajaxer'] == 1);
}

/**
 * Trigger an error for ajaxer (useful in batch mode)
 * @param string $msg
 */
function ajaxerActionErrorTrigger($msg)
{
	die("ko;$msg");
}

/**
 * Message success for ajaxer (useful in batch mode)
 * @param string $msg (if empty display ok or ok;+msg)
 */
function ajaxerActionSuccess($msg="")
{
	if(!empty($msg))$msg = ';'.$msg;
	die("ok$msg");
}


/**
 * Verify if action if allowed
 * @param string or array $action
 *
 * @return bool
 */
function ajaxerAction($action)
{
	if(is_array($action))return (in_array(@$_GET['_action'], $action));
	return (@$_GET['_action'] == $action);
}

/**
 * Return selected ID separated by comma
 */
function ajaxerGetIDS()
{
	$IDS = @explode(';', $_GET['IDS']);

	$tmp = '';
	foreach($IDS as $tmpID)
	{
		if(!empty($tmpID))
		{
			if(!empty($tmp))$tmp .= ',';
			$tmp .= (int)$tmpID;
		}
	}

	return $tmp;
}

/**
 * Construct url for ajaxer
 *
 * @param string $action (ajax action)
 * @param string $plugin_name (plugin name if empty current plugin)
 * @param string $plugin_default_action (if empty 'list')
 * @param string|array $params_added parameters added
 *
 * @return mixed
 */
function ajaxerUrlConstruct($action, $plugin_name='', $plugin_default_action='list', $params_added='')
{
	if(empty($plugin_name))$plugin_name = PLUGIN_NAME;
	if(empty($plugin_default_action))$plugin_default_action = 'list';

	$uri = "index.php?mod={$plugin_name}&do={$plugin_default_action}&ajaxer=1&_action={$action}";

	if(is_array($params_added))
	{
		$tmp = '';
		foreach($params_added as $key => $val)
		{
			$tmp .= "&{$key}=".urlencode($val);
		}
		
		$params_added = $tmp;
	}

	if(!empty($params_added))
		$uri .= '&'.$params_added;
	
	$uri .= "&t=".time();
	$uri = str_replace('&&', '&', $uri);
	
	return $uri;
}

/**
 * Control parameter ajax parameter
 *
 * @param string $index
 * @param string $cast (int, float)
 * @param string $method (get by default, post, session)
 */
function ajaxerParameterRequired($index, $cast='', $method='get')
{
	$method = strtolower($method);

	if($method == 'get')
	{
		if(!isset($_GET[$index]))die("Error: parameter `$index` not found in \$_GET");
		$v = &$_GET[$index];
	}
	elseif($method == 'post')
	{
		if(!isset($_POST[$index]))die("Error: parameter `$index` not found in \$_POST");
		$v = &$_POST[$index];
	}
	elseif($method == 'session')
	{
		if(!isset($_SESSION[$index]))die("Error: parameter `$index` not found in \$_SESSION");
		$v = &$_SESSION[$index];
	}

	// control validation
	if(!empty($cast))
	{
		if($cast == 'int')$v = (int)$v;
		if($cast == 'float')$v = (float)$v;
	}
}

/**
 * Automatic cast super globals with key ID or finish by ID for $_GET, $_POST
 */
function autocastSuperGlobals()
{
	foreach($_GET as $key => $val){
		if(!is_array($val) && ($key == 'ID' || preg_match('/ID$/', $key)))
			$_GET[$key] = (int)$val;
	}

	foreach($_POST as $key => $val){
		if(!is_array($val) && ($key == 'ID' || preg_match('/ID$/', $key)))
			$_POST[$key] = (int)$val;
	}

}

/**
 * Get ID from string useful for example with ajax component
 *
 * @param $str
 * @param string optional $separator (default '(')
 * @return int|null if zero
 */
function getIDFromString($str, $separator='(')
{
	$tmp = explode($separator, $str);

	$ID = '';
	if(count($tmp) >= 2)
	{
		$ID = (int)end($tmp);
		if($ID == 0)$ID = '';
	}

	return $ID;
}