<?php

header("content-type:text/html; charset=utf-8");
$timer = time();

// includes ******************************************************************
include('config.inc.php');
include('headers.inc.php');
FB::setEnabled(FirePHP_enabled);
include('_inc/Plugin.class.php');

$nuts = new NutsCore(false);
$nuts->dbSetProtection(false); # remove data protection
$nuts->dbConnect();
include('_inc/session.inc.php');


// ajax:action *************************************************************************************************
if(isset($_GET['_action']) && in_array($_GET['_action'], ['users_online', 'rte_get-templates', 'rte_get-template', 'rte_get-link_list', 'list_search_users', 'user_bookmark-toggle']))
{
	include("ajax/{$_GET['_action']}.inc.php");
	die();
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


// reload menu with new version !
$menu_count = Query::factory()->select("COUNT(*)")->from('NutsMenuCategory')->executeAndGetOne();
if($menu_count > 0)
{
    Query::factory()->select("Name, NameFr, Color")->from('NutsMenuCategory')->order_by('Position')->execute();

    $mods_group = array();
    while($rec = $nuts->dbFetch())
    {
        $cur_name = $rec['Name'];
        if(!empty($rec['NameFr']))$cur_name = $rec['Name'];
        if($NutsUserLang == 'fr')$cur_name = $rec['NameFr'];

        $mods_group[] = array('name'  => $cur_name, 'color' => $rec['Color']);
    }
}

// logout ********************************************************************************
if(isset($_GET['mod']) && $_GET['mod'] == 'logout')
{
	nutsLogout();
}

// plugin allowed **********************************************************************************************************************************

// auto register plugins
if(!isset($_GET['ajax']) && !isset($_GET['ajaxer']))
{
    $plugins_auto_registered = array('_internal-messaging::list', '_internal-memo::edit', '_user-profile::edit', '_user-shortcuts::list');
    foreach($plugins_auto_registered as $plugin_auto_registered)
    {
        list($cur_plugin, $cur_plugin_default_action) = explode('::', $plugin_auto_registered);
        if(!nutsUserHasRight('', $cur_plugin, $cur_plugin_default_action))
        {
            Plugin::register($cur_plugin);
        }
    }
}

// if(!@in_array($_GET['mod'], array('_internal-messaging', '_internal-memo', '_user-profile'))  && (!plugin::validator() || !plugin::actValidator()))
if(!plugin::validator() || !plugin::actValidator())
{
	$_GET['mod'] = '_error';
	$_GET['do'] = 'exec';
}

// const plugin
define('PLUGIN_NAME', $_GET['mod']);
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
if(!isset($_GET['ajax']) && !isset($_GET['ajaxer']) && !isset($_GET['target']))
{
	$for = 'MAIN';
	include('_inc/trt_menu.inc.php');
}

// execution ***********************************************************************************************************
$current_theme = nutsGetTheme();
$PHPSESSID = session_id();

$nuts_lang_msgs_compiled = "";
for($i=0; $i < count($nuts_lang_msg); $i++)
{
	if(!is_array($nuts_lang_msg[$i]))
	{
		$nuts_lang_msg[$i] = str_erase("\r", $nuts_lang_msg[$i]);
		$nuts_lang_msg[$i] = str_replace("\n", '\n', $nuts_lang_msg[$i]);
		$nuts_lang_msgs_compiled .= "var nuts_lang_msg_{$i} = \"{$nuts_lang_msg[$i]}\";";
	}
}

$NUTS_HTML_HEADERS = $nuts->directIoOutput('_templates/_header.html');
$nuts->open('_templates/all.html');

// allow Visual Query Builder
$AllowVisualQueryBuilder = false;
if(nutsUserHasRight($_SESSION['NutsGroupID'], '_visual-query-builder', 'exec'))
    $AllowVisualQueryBuilder = true;
$nuts->parse('AllowVisualQueryBuilder', $AllowVisualQueryBuilder);

// popup
if(isset($_GET['popup']) && ($_GET['popup'] == 1 || $_GET['popup'] == 'true'))
{
	$nuts->parse('popup_display', 'display:none;');
	$nuts->eraseBloc('gravatar');
}
else
{
	$nuts->parse('popup_display', '');
}

// trademark
if(!NUTS_TRADEMARK)$nuts->eraseBloc('trademark');


if(!isset($_GET['ajax']) && !isset($_GET['ajaxer']) && !isset($_GET['target']) && @$_GET['popup'] != 1 && @$_GET['popup'] != 'true')
{
	// gravatar old
    $avatar_image = nutsUserGetData('', 'Avatar');
    $avatar_image = $avatar_image['Avatar'];

    if(empty($avatar_image))$avatar_image = '/nuts/img/gravatar.jpg';
    $nuts->fastParse('avatar_image');

	// verify right for page
	if(!nutsUserHasRight($_SESSION['NutsGroupID'], '_page-manager', 'exec'))$nuts->eraseBloc('writing_page');
	if(!nutsUserHasRight($_SESSION['NutsGroupID'], '_news', 'list'))$nuts->eraseBloc('writing_news');

    // update plugin_list_ac get all plugin list allowed for this group
    /*$sql = "SELECT
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
	$plugin_list = array();
    while($row = $nuts->dbFetch())
    {
        $plugin_name = $row['Name'];
        $plugin_info = spyc::YAMLLoad(WEBSITE_PATH.'/plugins/'.$row['Name'].'/info.yml');

        // get plugin label
        if(file_exists(WEBSITE_PATH.'/plugins/'.$row['Name'].'/lang/'.$_SESSION['Language'].'.inc.php'))
        {
	        include(WEBSITE_PATH.'/plugins/'.$plugin_name.'/lang/'.$_SESSION['Language'].'.inc.php');
        }
        else
        {
            $default_lang = array_map('trim', explode(',',$plugin_info['langs']));
            $default_lang = $default_lang[0];

            include(WEBSITE_PATH.'/plugins/'.$plugin_name.'/lang/'.$default_lang.'.inc.php');
        }

        $plugin_label = str_replace_latin_accents($lang_msg[0]);
        $plugin_url = $row['ExternalUrl'];

        $plugin_default_action = $plugin_info['default_action'];

        $plugin_list_ac[] = array('label' => $plugin_label, 'name' =>  $plugin_name, 'url' => $plugin_url, 'default_action' => $plugin_default_action);
	    $plugin_list[$plugin_name] = array('label' => $plugin_label, 'name' =>  $plugin_name, 'url' => $plugin_url, 'default_action' => $plugin_default_action);
    }

    $plugin_list_ac = json_encode($plugin_list_ac);
    @$nuts->parse('plugin_list_ac', $plugin_list_ac);
	*/

	// listing favorite user shortcut
	$shortcuts = Query::factory()->select("ID, Plugin")
								 ->from('NutsUserShortcut')
								 ->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])
								 ->order_by('Position')
								 ->executeAndGetAll();

	if(count($shortcuts) == 0)
	{
		$nuts->eraseBloc('shorcuts');
	}
	else
	{
		$shorcut_valids = array();
		foreach($shortcuts as $shortcut)
		{
			if(isset($plugin_list[$shortcut['Plugin']]))
			{
				$shorcut_valids[] = $plugin_list[$shortcut['Plugin']];
			}
		}

		if(count($shorcut_valids) == 0)
		{
			$nuts->eraseBloc('shorcuts');
		}
		else
		{
			$nuts->eraseBloc('no_shortcut');

			foreach($shorcut_valids as $shorcut_valid)
			{
				$nuts->parse('shorcuts.Plugin', $shorcut_valid['name']);
				$nuts->parse('shorcuts.PluginName', $shorcut_valid['label']);

				$uri = "";
				if(empty($shorcut_valid['url']))
				{
					$uri = "javascript:system_goto('index.php?mod={$shorcut_valid['name']}&do={$shorcut_valid['default_action']}', 'content');";
				}
				else
				{
					$tmp = $shorcut_valid['url'];
					if(stripos($tmp, 'javascript:') !== false)
					{
						$uri = $shorcut_valid['url'];
					}
					elseif(stripos($tmp, 'http') !== false)
					{
						$uri = $shorcut_valid['url'].'" target="blank"';
					}
					else
					{
						$uri = "javascript:system_goto('{$shorcut_valid['url']}', 'content');";
					}
				}

				$nuts->parse('shorcuts.PluginUrl', $uri);
				$nuts->loop('shorcuts');
			}

		}
	}
}
else
{
	$nuts->erasebloc('shorcuts');
}




// navigation bar
$navbar[] = array('mod' => '_home', 'do' => 'exec', 'name' => $nuts_lang_msg[3]);
if(($plugin->name != '_home' && $plugin->name != '_error') || ($plugin->name == '_home' && isset($_GET['category'])))
{
    if(!isset($_GET['category']))
    {
        // get plugin category
        $category_info = Query::factory()->select("
                                                Category AS CategoryNumber,
                                                (SELECT Name FROM NutsMenuCategory WHERE Deleted = 'NO' AND Position = NutsMenu.Category) AS CategoryName,
                                                (SELECT NameFr FROM NutsMenuCategory WHERE Deleted = 'NO' AND Position = NutsMenu.Category) AS CategoryNameFr
                                              ")
            ->from('NutsMenu')
            ->whereEqualTo('Name', $plugin->name)
            ->executeAndFetch();

        $cat = ($NutsUserLang == 'fr') ? $category_info['CategoryNameFr'] : $category_info['CategoryName'];
        $navbar[] = array('mod' => '_home', 'do' => 'exec&category='.$category_info['CategoryNumber'], 'name' => $cat);
        $navbar[] = array('mod' => $plugin->name, 'do' => $plugin->configuration['default_action'], 'name' => $plugin->real_name);
    }
    else # category selected
    {
        $_GET['category'] = (int)$_GET['category'];

        // get plugin category
        $category_info = Query::factory()->select("
                                                Category AS CategoryNumber,
                                                (SELECT Name FROM NutsMenuCategory WHERE Deleted = 'NO' AND Position = '{$_GET['category']}') AS CategoryName,
                                                (SELECT NameFr FROM NutsMenuCategory WHERE Deleted = 'NO' AND Position = '{$_GET['category']}') AS CategoryNameFr
                                              ")
            ->from('NutsMenu')
            ->whereEqualTo('Category', $_GET['category'])
            ->executeAndFetch();
        $cat = ($NutsUserLang == 'fr') ? $category_info['CategoryNameFr'] : $category_info['CategoryName'];

        $navbar[] = array('mod' => '_home', 'do' => 'exec&category='.$category_info['CategoryNumber'], 'name' => $cat);

    }
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

