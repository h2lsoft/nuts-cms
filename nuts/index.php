<?php

header("content-type:text/html; charset=utf-8");

// no cache
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header("Cache-Control: no-cache");
//header("Pragma: no-cache");

$timer = time();


// includes ******************************************************************
include('config.inc.php');
include('headers.inc.php');
FB::setEnabled(FirePHP_enabled);

include('_inc/Plugin.class.php');

$nuts = new NutsCore();
$nuts->dbSetProtection(false); # remove data protection
$nuts->dbConnect();
include('_inc/session.inc.php');


// ajax:users_online *************************************************************************
if(@$_GET['_action'] == 'users_online')
{
	$users_online = array();

	// get user online last 2 minutes
	$sql = "SELECT
					NutsUserID,
					(SELECT Application FROM NutsUser WHERE ID = NutsUserID ORDER BY DateGMT DESC LIMIT 1) AS Application,
					(SELECT Avatar FROM NutsUser WHERE ID = NutsUserID) AS Avatar,
					(SELECT Login FROM NutsUser WHERE ID = NutsUserID) AS Name

			FROM
					NutsLog
			WHERE
					NutsUserID != 0 AND
					TIMESTAMPDIFF(MINUTE, DateGMT, UTC_TIMESTAMP()) <= 2 AND
					Application != 'front-office'
			GROUP BY
					NutsUserID
			ORDER BY
					DateGMT DESC";
	$nuts->doQuery($sql);
	while($row = $nuts->dbFetch())
	{
		$row['Application'] = str_replace(array('-','_'), ' ', $row['Application']);
		$row['Application'] = trim($row['Application']);

		// $gravatar_url = 'http://www.gravatar.com/avatar/'.md5($row['Email']).'?s=60&d=http%3A%2F%2Fwww.nuts-cms.com%2Fnuts%2Fimg%2Fgravatar.jpg';
        $gravatar_url = $row['Avatar'];
        if(empty($gravatar_url))$gravatar_url = '/nuts/img/gravatar.jpg';
		$users_online[] = array('avatar_url' => $gravatar_url, 'Name' => $row['Name'], 'ID' => $row['NutsUserID'], 'Application' => $row['Application']);
	}
	
	die(json_encode($users_online));
}

// ajax:list_search_users *************************************************************************
if(@$_GET['_action'] == 'list_search_users')
{
    $data = array();
    $data['error'] = false;
    $data['error_msg'] = '';

    // action verification
    if(!@in_array($_GET['_action2'], array('list', 'add', 'delete', 'select')))
    {
        $data['error'] = true;
        $data['error_msg'] = 'action not correct';
        die(json_encode($data));
    }

    // plugin verification
    if(!isset($_GET['plugin']))
    {
        $data['error'] = true;
        $data['error_msg'] = 'plugin not correct';
        die(json_encode($data));
    }

    if(!nutsUserHasRight('', $_GET['plugin'], 'list'))
    {
        $data['error'] = true;
        $data['error_msg'] = 'plugin not allowed';
        die(json_encode($data));
    }

    // action list
    if($_GET['_action2'] == 'list')
    {
        $sql = "SELECT
                        ID, Name
                FROM
                        NutsUserListSearches
                WHERE
                        Deleted = 'NO' AND
                        Plugin = '{$_GET['plugin']}' AND
                        NutsUserID = {$_SESSION['NutsUserID']}
                ORDER BY
                        Name";
        $nuts->doQuery($sql);
        $data['list'] = array();
        while($row = $nuts->dbFetch())
            $data['list'][] = $row;
    }

    // action add
    if($_GET['_action2'] == 'add')
    {
        if(@empty($_GET['name']))
        {
            $data['error'] = true;
            $data['error_msg'] = 'search name not correct';
            die(json_encode($data));
        }

        if(@empty($_POST['serialized']))
        {
            $data['error'] = true;
            $data['error_msg'] = 'search serialized not correct';
            die(json_encode($data));
        }

        // save
        $f = array();
        $f['NutsUserID'] = $_SESSION['NutsUserID'];
        $f['Plugin'] = $_GET['plugin'];
        $f['Name'] = ucfirst($nuts->xssProtect(str_replace('"', "`", $_GET['name'])));
        $f['Serialized'] = $_POST['serialized'];

        $nuts->dbInsert('NutsUserListSearches', $f);

    }

    // action delete
    if($_GET['_action2'] == 'delete')
    {
        $_GET['ID'] = (int)@$_GET['ID'];
        if(!$_GET['ID'])
        {
            $data['error'] = true;
            $data['error_msg'] = 'ID not correct';
            die(json_encode($data));
        }

        $nuts->dbUpdate('NutsUserListSearches', array('Deleted' => 'YES'), "ID={$_GET['ID']} AND NutsUserID = {$_SESSION['NutsUserID']}");
    }

    // action select
    if($_GET['_action2'] == 'select')
    {
        $_GET['ID'] = (int)@$_GET['ID'];
        if(!$_GET['ID'])
        {
            $data['error'] = true;
            $data['error_msg'] = 'ID not correct';
            die(json_encode($data));
        }

        $sql = "SELECT
                        Serialized
                FROM
                        NutsUserListSearches
                WHERE
                        Deleted = 'NO' AND
                        ID = {$_GET['ID']} AND
                        NutsUserID = {$_SESSION['NutsUserID']}";
        $nuts->doQuery($sql);
        $serialized = $nuts->dbGetOne();
        $data['serialized'] = $serialized;
    }




    die(json_encode($data));
}



// execution *************************************************************************


// nuts information **********************************************************************
$nuts_info = spyc::YAMLLoad('info.yml');
$nuts_info['version'] =  str_replace(',', '.', $nuts_info['version']);
define('NUTS_VERSION', $nuts_info['version']);

$NutsUserLang = strtolower($_SESSION['Language']);

if(!file_exists('lang/'.$NutsUserLang.'.inc.php'))
	include('lang/en.inc.php');
else
	include('lang/'.$NutsUserLang.'.inc.php');

// include custom menu
include('_custom_menu.inc.php');


// logout ********************************************************************************
if(isset($_GET['mod']) && $_GET['mod'] == 'logout')
{
	nutsLogout();
}

// plugin ********************************************************************************
if(!@in_array($_GET['mod'], array('_internal-messaging', '_internal-memo', '_user-profile'))  && (!plugin::validator() || !plugin::actValidator()))
{
	$_GET['mod'] = '_error';
	$_GET['do'] = 'exec';
}

// const plugin
define('PLUGIN_PATH', WEBSITE_PATH.'/plugins/'.$_GET['mod']);
define('PLUGIN_URL', WEBSITE_URL.'/plugins/'.$_GET['mod']);
define('PLUGIN_UPLOADS_PATH', NUTS_UPLOADS_PATH.'/'.$_GET['mod']);
define('PLUGIN_UPLOADS_URL', NUTS_UPLOADS_URL.'/'.$_GET['mod']);

if(file_exists(PLUGIN_PATH.'/lang/'.$NutsUserLang.'.inc.php'))
	include(PLUGIN_PATH.'/lang/'.$NutsUserLang.'.inc.php');
else
{
	$yaml = Spyc::YAMLLoad(PLUGIN_PATH.'/info.yml');
	$default_lang = array_map('trim', explode(',',$yaml['langs']));
	$default_lang = $default_lang[0];
	
	include(PLUGIN_PATH.'/lang/'.$default_lang.'.inc.php');
}
	

$plugin = new Plugin();
include(PLUGIN_PATH.'/'.$_GET['do'].'.inc.php');

// get all groups
if(!isset($_GET['ajax']) || !isset($_GET['target']))
{
	$for = 'MAIN';
	include('_inc/trt_menu.inc.php');
}

// execution *****************************************************************************
$current_theme = nutsGetTheme();
$PHPSESSID = session_id();

$nuts->open('_templates/all.html');

// popup
if(isset($_GET['popup']) && $_GET['popup'])
{
	$nuts->parse('popup_display', 'display:none;');
	$nuts->eraseBloc('gravatar');
}
else
{
	$nuts->parse('popup_display', '');
}

// trademark
/*if(!NUTS_TRADEMARK)
	$nuts->eraseBloc('trademark');*/


if(!isset($_GET['ajax']) && !isset($_GET['target']) && !isset($_GET['popup']))
{
	// gravatar old
	/*$email_hash = md5($_SESSION['Email']);
	$gravatar_encoded = urlencode(WEBSITE_URL.'/nuts/img/gravatar.jpg');
	$nuts->fastParse('email_hash');
	$nuts->fastParse('gravatar_encoded');*/

    $avatar_image = nutsUserGetData('', 'Avatar');
    $avatar_image = $avatar_image['Avatar'];

    if(empty($avatar_image))$avatar_image = '/nuts/img/gravatar.jpg';
    $nuts->fastParse('avatar_image');

	
	// verify right for page
	if(!nutsUserHasRight($_SESSION['NutsGroupID'], '_page-manager', 'exec'))$nuts->eraseBloc('wrinting_page');
	if(!nutsUserHasRight($_SESSION['NutsGroupID'], '_news', 'list'))$nuts->eraseBloc('wrinting_news');

	
	

	// update plugin_list_ac get all plugin list allowed for this group
	$sql = "SELECT
					DISTINCT NutsMenu.Name,
					NutsMenu.ExternalUrl
			FROM
					NutsMenu,
					NutsMenuRight
			WHERE
					NutsMenu.ID = NutsMenuRight.NutsMenuID AND
					NutsMenuRight.NutsGroupID = {$_SESSION['NutsGroupID']} AND
					NutsMenu.Visible = 'YES'
			ORDER BY
					NutsMenu.Name ";
	$nuts->doQuery($sql);
	$plugin_list_ac = array();
	while($row = $nuts->dbFetch())
	{
		$plugin_name = $row['Name'];
		$plugin_info = spyc::YAMLLoad(WEBSITE_PATH.'/plugins/'.$row['Name'].'/info.yml');
		
		// get plugin label
		if(file_exists(WEBSITE_PATH.'/plugins/'.$row['Name'].'/lang/'.$_SESSION['Language'].'.inc.php'))
			include(WEBSITE_PATH.'/plugins/'.$plugin_name.'/lang/'.$_SESSION['Language'].'.inc.php');
		else
		{
			$default_lang = array_map('trim', explode(',',$plugin_info['langs']));
			$default_lang = $default_lang[0];
			
			include(WEBSITE_PATH.'/plugins/'.$plugin_name.'/lang/'.$default_lang.'.inc.php');

		}			

		$plugin_label = $lang_msg[0];
		$plugin_url = $row['ExternalUrl'];
		
		$plugin_default_action = $plugin_info['default_action'];
		
		$plugin_list_ac[] = array('label' => $plugin_label, 'name' =>  $plugin_name, 'url' => $plugin_url, 'default_action' => $plugin_default_action);
	}

	$plugin_list_ac = json_encode($plugin_list_ac);
	$nuts->fastParse('plugin_list_ac');
	
}

// navigation bar
$navbar[] = array('mod' => '_home', 'do' => 'exec', 'name' => $nuts_lang_msg[3]);
if($plugin->name != '_home' && $plugin->name != '_error')
{
	$navbar[] = array('mod' => $plugin->name, 'do' => $plugin->configuration['default_action'], 'name' => $plugin->real_name);
}

// configuration
if($_SESSION['NutsGroupID'] != 1 || !file_exists("../plugins/{$plugin->name}/config.inc.php"))
{
	$nuts->eraseBloc('admin_configuration_image');
}
else
{
	$cfg_uri = NUTS_PLUGINS_PATH.'/'.$plugin->name.'/config.inc.php';
	$cfg_uri = base64_encode($cfg_uri);
	$cfg_uri = 'index.php?mod=_configuration&do=edit&f='.$cfg_uri;
	$nuts->parse('admin_configuration_image.config_url', $cfg_uri);
}



$nuts->loadArrayInBloc('navbar', $navbar);

$out = $nuts->output();
$nuts->dbClose();

if(!isset($_GET['ajax']) || !isset($_GET['target']))
{
	echo $out;
}
else
{
	
    $out = $nuts->getAjaxBloc($_GET['target'], $out);    
    echo $out;
}


?>