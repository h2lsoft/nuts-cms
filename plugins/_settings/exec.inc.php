<?php
/**
 * Plugin _settings - action Exec
 * 
 * @version 1.0
 * @date 29/05/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// ajaxer treatment ****************************************************************************************************
if(@$_GET['ajaxer'] == 1 && $_POST && !@empty($_POST['objID']))
{
    sleep(1);

    $obj_label = ucwords(strtolower(str_replace('_', ' ', $_POST['objID'])));
    $val = trim($_POST['objVal']);

    // WEBSITE_NAME, WEBSITE_URL, WEBSITE_PATH
    // NUTS_DB_HOST, NUTS_DB_USER, NUTS_DB_PASSWORD, NUTS_DB_BASE, NUTS_DB_PORT
    if(in_array($_POST['objID'], array('WEBSITE_NAME', 'WEBSITE_URL', 'WEBSITE_PATH', 'NUTS_ADMIN_EMAIL', 'NUTS_EMAIL_NO_REPLY', 'FirePHP_enabled',
                                       'NUTS_DB_HOST', 'NUTS_DB_USER', 'NUTS_DB_PASSWORD', 'NUTS_DB_BASE', 'NUTS_DB_PORT',
                                       'MetaTitle', 'MetaDescription', 'MetaKeywords', 'NUTS_ERROR404_TEMPLATE', 'NUTS_ERROR_PAGE_REDIRECT',
                                       'NUTS_LOG_ERROR_404', 'NUTS_LOG_ERROR_TAGS', 'NUTS_TIDY', 'NUTS_HTML_COMPRESS', 'NUTS_HTML_COMPRESS_TIME',
                                       'APP_TITLE', 'BACKOFFICE_LOGO_URL', 'TWITTER_LOGIN', 'FACEBOOK_PUBLISH_URL',
                                        'nuts_theme_selected'
                                      )))
    {
        // fichier de configuration non editable
        if(!is_writable(NUTS_PATH.'/config.inc.php'))
        {
            $msg = ($_SESSION['Language'] == 'fr') ? "Fichier de configuration non editable" : "Configuration file no writable";
            die($msg);
        }

        // not empty
        if(strlen($val) == 0 && !in_array($_POST['objID'], array('NUTS_DB_PASSWORD', 'NUTS_DB_PORT', 'MetaTitle', 'MetaDescription', 'MetaKeywords', 'NUTS_ERROR_PAGE_REDIRECT', 'BACKOFFICE_LOGO_URL', 'TWITTER_LOGIN', 'FACEBOOK_PUBLISH_URL')))
        {
            $msg = ($_SESSION['Language'] == 'fr') ? "$obj_label ne peut être vide" : "$obj_label can not be empty";
            die($msg);
        }

        if($_POST['objID'] == 'WEBSITE_URL')
        {
            if(!filter_var($val, FILTER_VALIDATE_URL) || $val[strlen($val)-1] == '/')
            {
                $msg = ($_SESSION['Language'] == 'fr') ? "Votre lien est incorrect" : "Your url is not correct";
                die($msg);
            }
        }

        if($_POST['objID'] == 'WEBSITE_PATH')
        {
            if(!is_dir($val) || $val[strlen($val)-1] == '/')
            {
                $msg = ($_SESSION['Language'] == 'fr') ? "Le chemin est incorrect" : "Your path is not correct";
                die($msg);
            }
        }



        // NUTS_ADMIN_EMAIL & NUTS_EMAIL_NO_REPLY
        if($_POST['objID'] == 'NUTS_ADMIN_EMAIL' || $_POST['objID'] == 'NUTS_EMAIL_NO_REPLY')
        {
            if(!email($val))
            {
                $msg = ($_SESSION['Language'] == 'fr') ? "L'adresse email est incorrecte" : "Email address is not correct";
                die($msg);
            }
        }

        if($_POST['objID'] == 'NUTS_HTML_COMPRESS_TIME')
        {
            $val = (int)$val;
            if($val == 0)
            {
                $msg = ($_SESSION['Language'] == 'fr') ? "Le temps de cache de compression doit être un nombre" : "Compress cache time but be a digit";
                die($msg);
            }
        }

        $rep = "define('{$_POST['objID']}', \"$val\");";
        if(in_array($_POST['objID'], array('FirePHP_enabled', 'NUTS_LOG_ERROR_404', 'NUTS_LOG_ERROR_TAGS', 'NUTS_TIDY', 'NUTS_HTML_COMPRESS')))
        {
            $v = ($val == 1) ? 'true' : 'false';
            $rep = "define('{$_POST['objID']}', $v);";
        }
        elseif($_POST['objID'] == 'NUTS_HTML_COMPRESS_TIME')
        {
            $rep = "define('{$_POST['objID']}', $val);";
        }
        elseif($_POST['objID'] == 'nuts_theme_selected')
        {
            $replacement = "\$nuts_theme_selected = '{$_POST['objVal']}'; // theme selected";
            fileChangeLineContents(NUTS_PATH.'/config.inc.php', '$nuts_theme_selected =', $replacement);
            die('ok');
        }

        fileChangeLineContents(NUTS_PATH.'/config.inc.php', "define('{$_POST['objID']}',", $rep);
    }
    elseif($_POST['objID'] == 'default_lang')
    {
        $nuts->dbUpdate('NutsTemplateConfiguration', array('LanguageDefault' => $_POST['objVal']));
    }
    elseif($_POST['objID'] == 'Languages')
    {
        $langs = trim($_POST['objVal']);
        $langs = strtolower($langs);
        $langs = explode(',', $langs);
        $langs = array_map('trim', $langs);
        $langs = join(', ', $langs);

        $nuts->dbUpdate('NutsTemplateConfiguration', array('Languages' => $langs));
    }




    die('ok');
}


// execution ***********************************************************************************************************
$nuts->open(PLUGIN_PATH.'/exec.html');

// parsing themes
$themes = array();
$a = glob(WEBSITE_PATH.'/library/themes/*', GLOB_ONLYDIR);
foreach($a as $theme)
{
    $theme_str = explode('/', $theme);
    $theme_str = $theme_str[count($theme_str)-1];
    $themes[] = array('value' => $theme_str, 'label' => ucfirst($theme_str));
}

foreach($themes as $theme)
{
    // preview
    $theme_preview = NUTS_THEMES_PATH."/{$theme['value']}/_preview/theme.jpg";
    if(!file_exists($theme_preview))
    {
        $img_url = NUTS_PLUGINS_URL.'/_settings/no-preview.png';
    }
    else
    {
        $img_url = str_replace(NUTS_THEMES_PATH, NUTS_THEMES_URL, $theme_preview);
    }

    // information
    $theme_info = NUTS_THEMES_PATH."/{$theme['value']}/info.yml";

    $infos = "<b style='font-size: 14px; color: #D65EB1;'>{$theme['label']}</b>";
    if(file_exists($theme_info))
    {
        $info = SPYC::YAMLLoad($theme_info);
        $infos .= "<br /><b>Version :</b> {$info['version']}";
        $infos .= "<br /><b>Author :</b> {$info['author']}";
        $infos .= "<br /><b>Website :</b> {$info['website']}";
        $infos .= "<br /><b>Email :</b> {$info['email']}";
        $infos .= "<br /><b>Lang(s) :</b> {$info['langs']}";
    }


    $nuts->parse('theme.theme', $theme['value']);
    $nuts->parse('theme.image_preview', $img_url);
    $nuts->parse('theme.infos', $infos);
    $nuts->loop('theme');
}

// pasing langs
foreach($nuts_lang_options as $lang)
{
    $nuts->parse('nuts_lang_options.label', $lang['label']);
    $nuts->parse('nuts_lang_options.value', $lang['value']);
    $nuts->loop('nuts_lang_options');
}

$sql = "SELECT * FROM NutsTemplateConfiguration WHERE Deleted = 'NO' LIMIT 1";
$nuts->doQuery($sql);
$data = $nuts->dbGetData();

$default_lang = $data[0]['LanguageDefault'];
$nuts->parse('default_lang', $default_lang);
$nuts->parse('Languages', $data[0]['Languages']);





$plugin->render = $nuts->output();



?>