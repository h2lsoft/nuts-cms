<?php
/**
 * File Browser v1.5
 */

// init ****************************************************************************************************************
if(@!$_GET['ajax'])$_GET['ajax'] = false;
if(@!$_GET['action'])$_GET['action'] = '';
if(!@in_array($_GET['editor'], array('standalone', 'tinymce', 'edm')))$_GET['editor'] = 'standalone';
if(@!$_GET['returnID'])$_GET['returnID'] = '';

$timer = time();


// controller **********************************************************************************************************
if(isset($_GET['XPHPSESSID']))$_COOKIE['PHPSESSID'] = $_GET['XPHPSESSID'];
session_start();

include('../../../../../nuts/config.inc.php');
include(WEBSITE_PATH.'/nuts/headers.inc.php');
include('inc/func.inc.php');
include('inc/edm_func.inc.php');

// check right access
if(@$_SESSION['NutsGroupID'] == '' || @$_SESSION['NutsUserID'] == '')
    systemError("You must be logged");

// init
$nuts = new NutsCore();
$nuts->dbConnect();


// Figure out which language file to load
$lang_file = "lang/{$_SESSION['Language']}.php";
if(file_exists($lang_file))
    require_once($lang_file);
else
    require_once("lang/en.php");

// vars
if(in_array($_GET['editor'], array('standalone', 'tinymce')))
{
    define('EDM_ADMINISTRATOR', false);
    $app_title = "File Explorer";
    $tree_hidden_folders = array();
    $paste_forbidden_folders = array();

    // init filters
    if(!@in_array($_GET['filter'], array('image', 'media', 'file')))
        $_GET['filter'] = 'file';

    if($_GET['filter'] == 'image')
    {
        $root_name = translate('My Images');
        $upload_path = WEBSITE_PATH."/library/media/images/user/";
        $upload_pathX = '/library/media/images/user/';
    }
    elseif($_GET['filter'] == 'media')
    {
        $root_name = translate('My Medias');
        $upload_path = WEBSITE_PATH."/library/media/multimedia/";
        $upload_pathX = '/library/media/multimedia/';
    }
    else
    {
        $root_name = translate('My Files');
        $upload_path = WEBSITE_PATH."/library/media/other/";
        $upload_pathX = '/library/media/other/';

        // $tree_hidden_folders[] = $upload_path.'nuts_drop_box';
        // $tree_hidden_folders[] = $upload_path.'nuts_press_kit';
    }

    // folder forbidden (paste, upload, delete)
    foreach($tree_hidden_folders as $f)
        $paste_forbidden_folders[] = str_replace(WEBSITE_PATH, '', $f);

    $absolute_url = false; // When FALSE url will be returned absolute without hostname, like /upload/file.jpg.
    $absolute_url_disabled = false; // When TRUE changing from absolute to relative is not possible.

    include('config.inc.php');

    // init path
    $load_folder = '';
    if(!$_GET['ajax'])
    {
        if(@empty($_GET['path']))
        {
            if($_GET['filter'] == 'image')$load_folder = "/library/media/images/user/";
            elseif($_GET['filter'] == 'media')$load_folder = "/library/media/multimedia/";
            elseif($_GET['filter'] == 'file')$load_folder = "/library/media/other/";
        }
        else
        {
            if($_GET['filter'] == 'image')
            {
                if(!is_dir(WEBSITE_PATH."/library/media/images/user/{$_GET['path']}"))
                    systemError("Path not found `/library/media/images/user/{$_GET['path']}`");
                $load_folder = "/library/media/images/user/{$_GET['path']}/";
            }
            elseif($_GET['filter'] == 'media')
            {
                if(!is_dir(WEBSITE_PATH."/library/media/multimedia/{$_GET['path']}"))
                    systemError("Path not found `/library/media/multimedia/{$_GET['path']}`");
                $load_folder = "/library/media/multimedia/{$_GET['path']}/";
            }
            elseif($_GET['filter'] == 'file')
            {
                if(!is_dir(WEBSITE_PATH."/library/media/other/{$_GET['path']}"))
                    systemError("Path not found `/library/media/other/{$_GET['path']}`");
                $load_folder = "/library/media/other/{$_GET['path']}/";
            }
        }
    }

    // allowed actions
    $allowedActions = array(
        'upload' => ($_SESSION['AllowUpload'] == 'YES') ? true : false,
        'cut_paste' => ($_SESSION['AllowEdit'] == 'YES') ? true : false,
        'copy_paste' => ($_SESSION['AllowEdit'] == 'YES') ? true : false,
        'rename' => ($_SESSION['AllowEdit'] == 'YES') ? true : false,
        'delete' => ($_SESSION['AllowDelete'] == 'YES') ? true : false,
        'create_folder' => ($_SESSION['AllowFolders'] == 'YES') ? true : false
    );

    // init view cookie
    if(!empty($_COOKIE["pdw-view"]))$viewLayout = $_COOKIE["pdw-view"];

}
elseif($_GET['editor'] = 'edm')
{
    $app_title = "Document Manager";

    $_GET['filter'] = 'file';
    $tree_hidden_folders = array();
    $paste_forbidden_folders = array();

    // right access
    if(!nutsUserHasRight('', '_edm', 'exec'))
        systemError("Access forbidden");

    include(NUTS_PLUGINS_PATH.'/_edm/config.inc.php');

    $root_name = translate('Root');
    $upload_path = WEBSITE_PATH."/plugins/_edm/_repository/";
    $upload_pathX = '/plugins/_edm/_repository/';
    $load_folder = '';

    // check .htaccess ok
    if(!file_exists($upload_path.".htaccess"))
    {
        die("Security error: File `$upload_path.htaccess` not exists");
    }

    // administrator
    if(nutsUserHasRight('', '_edm', 'administration'))
        define('EDM_ADMINISTRATOR', true);
    else
        define('EDM_ADMINISTRATOR', false);


    // allowed actions
    $allowedActions = array(
        'upload' => false,
        'cut_paste' => false,
        'copy_paste' => false,
        'rename' => false,
        'delete' => false,
        'create_folder' => false
    );

    if(@empty($_COOKIE["edm-pdw-view"]))$_COOKIE["edm-pdw-view"] = "details";

    // init view cookie
    if(!empty($_COOKIE["edm-pdw-view"]))$viewLayout = $_COOKIE["edm-pdw-view"];

}



// max file size
$max_file_size_in_bytes = $max_file_size; // 1MB in bytes
$max_file_size_in_bytes = (int)str_replace('M', "", $max_file_size_in_bytes);
$max_file_size_in_bytes *= 1024 * 1024;
$max_upload_size = min(let_to_num(ini_get('post_max_size')), let_to_num(ini_get('upload_max_filesize')));
if($max_file_size_in_bytes > $max_upload_size)
    $max_file_size_in_bytes = $max_upload_size;


// Get local settings from language file
$datetimeFormat = $lang["datetime format"];				// 24 hours, AM/PM, etc...
$dec_seperator = $lang["decimal seperator"]; 			// character in front of the decimals
$thousands_separator = $lang["thousands separator"];	// character between every group of thousands
$allowedActionsX = json_encode($allowedActions);


// init ****************************************************************************************************************


// init allowed files from NutsFileExplorerMimesType
$sql_added = '';
if(in_array($_GET['editor'], array('standalone', 'tinymce')))
{
    $sql_added = "FileExplorer = 'YES'";
}
else
{
    $sql_added = "EDM = 'YES'";
}

$cfg_files = Query::factory()->select('Extension, Mimes')
                            ->from('NutsFileExplorerMimesType')
                            ->where($sql_added)
                            ->executeAndGetAll();
$filetypes = array();
foreach($cfg_files as $f){
    $mimes = explode(CR, trim($f['Mimes']));
    $mimes = array_map('trim', $mimes);
    $filetypes[strtolower(trim($f['Extension']))] = $mimes;
}

$filetypes_exts = array();
$filetypes_mimes = array();
foreach($filetypes as $filetype => $mimes)
{
    $filetypes_exts[] = $filetype;
    $filetypes_mimes = array_merge($filetypes_mimes, $mimes);
}

// ajax ****************************************************************************************************************
$exe = (in_array($_GET['editor'], array('standalone', 'tinymce'))) ? 'file_explorer' : 'edm';
$ajax_action_file = "ajax/$exe/{$_GET['action']}.inc.php";
if(in_array($_GET['editor'], array('standalone', 'tinymce', 'edm')) && file_exists($ajax_action_file))
{

    $resp = array();
    $resp['result'] = '';
    $resp['message'] = '';
    $resp['html'] = '';

    include($ajax_action_file);

    die(json_encode($resp));
}


// execution ***********************************************************************************************************
$nuts->open('templates/all.html');

// filters
$filter_image_selected = ($_GET['filter'] == 'image') ? 'selected' : '';
$filter_media_selected = ($_GET['filter'] == 'media') ? 'selected' : '';
$filter_flash_selected = ($_GET['filter'] == 'flash') ? 'selected' : '';
$nuts->fastparse('filter_image_selected');
$nuts->fastparse('filter_media_selected');
$nuts->fastparse('filter_flash_selected');

// custom filters
if(!count($customFilters))
{
    $nuts->eraseBloc('custom_filters');
}
else
{
    foreach($customFilters as $key => $value)
    {
        $nuts->parse('custom_filters.label', $key);
        $nuts->parse('custom_filters.value', $value);
        $nuts->loop('custom_filters');
    }
}

$out = $nuts->output();

preg_match_all('#<i18n>(.*)</i18n>#sUi', $out, $matches);
if(count($matches) == 2)
{
    $matches = $matches[1];
    foreach($matches as $pattern)
    {
        $out = str_replace("<i18n>$pattern</i18n>", translate($pattern), $out);
    }
}

$nuts->dbClose();
die($out);


?>