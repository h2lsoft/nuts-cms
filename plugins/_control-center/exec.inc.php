<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

$cfg_file = WEBSITE_PATH.'/nuts/config.inc.php';

// action *************************************************************************
if(@$_GET['_action'] == 'maintenance' || @$_GET['_action'] == 'error404' || @$_GET['_action'] == 'error500' || @$_GET['_action'] == 'log_error_404' || @$_GET['_action'] == 'log_error_nuts_tags' ||
   @$_GET['_action'] == 'html_compress' || @$_GET['_action'] == 'html_compress_time' || @$_GET['_action'] == 'firebug' || @$_GET['_action'] == 'tidy')
{
	$val = $_GET['val'];

	$contents = file_get_contents($cfg_file);

	// maintenance
	if($_GET['_action'] == 'maintenance')
	{
		if($val == 'NO')
		{
			$rep = "define('WEBSITE_MAINTENANCE', true);";
			$rep2 = "define('WEBSITE_MAINTENANCE', false);";
		}
		else
		{
			$rep = "define('WEBSITE_MAINTENANCE', false);";
			$rep2 = "define('WEBSITE_MAINTENANCE', true);";
		}
	}

	// error 404
	elseif($_GET['_action'] == 'error404')
	{
		$rep = "define('NUTS_ERROR404_TEMPLATE', '".NUTS_ERROR404_TEMPLATE."');";
		$rep2 = "define('NUTS_ERROR404_TEMPLATE', '".$val."');";
	}

	// error 500
	elseif($_GET['_action'] == 'error500')
	{
		$rep = "define('NUTS_ERROR_PAGE_REDIRECT', \"".NUTS_ERROR_PAGE_REDIRECT."\");";
		$rep2 = "define('NUTS_ERROR_PAGE_REDIRECT', \"".$val."\");";
	}

	// log error 404
	elseif($_GET['_action'] == 'log_error_404')
	{
		if($val == 'NO')
		{
			$rep = "define('NUTS_LOG_ERROR_404', true);";
			$rep2 = "define('NUTS_LOG_ERROR_404', false);";
		}
		else
		{
			$rep = "define('NUTS_LOG_ERROR_404', false);";
			$rep2 = "define('NUTS_LOG_ERROR_404', true);";
		}
	}

	// error nuts tags
	elseif($_GET['_action'] == 'log_error_nuts_tags')
	{
		if($val == 'NO')
		{
			$rep = "define('NUTS_LOG_ERROR_TAGS', true);";
			$rep2 = "define('NUTS_LOG_ERROR_TAGS', false);";
		}
		else
		{
			$rep = "define('NUTS_LOG_ERROR_TAGS', false);";
			$rep2 = "define('NUTS_LOG_ERROR_TAGS', true);";
		}
	}

	// html_compress
	elseif($_GET['_action'] == 'html_compress')
	{
		if($val == 'NO')
		{
			$rep = "define('NUTS_HTML_COMPRESS', true);";
			$rep2 = "define('NUTS_HTML_COMPRESS', false);";
		}
		else
		{
			$rep = "define('NUTS_HTML_COMPRESS', false);";
			$rep2 = "define('NUTS_HTML_COMPRESS', true);";
		}
	}
	// html_compress_time
	elseif($_GET['_action'] == 'html_compress_time')
	{
		$rep = "define('NUTS_HTML_COMPRESS_TIME', ".NUTS_HTML_COMPRESS_TIME.");";
		$rep2 = "define('NUTS_HTML_COMPRESS_TIME', ".(int)$val.");";
	}

	// firebug
	elseif($_GET['_action'] == 'firebug')
	{
		if($val == 'NO')
		{
			$rep = "define('FirePHP_enabled', true);";
			$rep2 = "define('FirePHP_enabled', false);";
		}
		else
		{
			$rep = "define('FirePHP_enabled', false);";
			$rep2 = "define('FirePHP_enabled', true);";
		}
	}
	// tidy
	elseif($_GET['_action'] == 'tidy')
	{
		if($val == 'NO')
		{
			$rep = "define('NUTS_TIDY', true);";
			$rep2 = "define('NUTS_TIDY', false);";
		}
		else
		{
			$rep = "define('NUTS_TIDY', false);";
			$rep2 = "define('NUTS_TIDY', true);";
		}
	}

	$contents = str_replace($rep, $rep2, $contents);
	if(!@file_put_contents($cfg_file, $contents))
	{
		die("error@@@File `$cfg_file` not changed (check permission ?)");
	}
	else
	{
		die("ok@@@File `$cfg_file` updated");
	}
}
elseif(@$_GET['_action'] == 'ip_allowed')
{
	$val = $_GET['val'];

	$val = explode(',', $val);
	$val = array_map('trim', $val);
	$nval = join(", ", $val);

	$contents = file_get_contents($cfg_file);

	// catch define('WEBSITE_MAINTENANCE_IPS', '');
	$rep = "define('WEBSITE_MAINTENANCE_IPS', '".WEBSITE_MAINTENANCE_IPS."');";
	$rep2 = "define('WEBSITE_MAINTENANCE_IPS', '".$nval."');";

	$contents = str_replace($rep, $rep2, $contents);
	if(!@file_put_contents($cfg_file, $contents))
	{
		die("error@@@File `$cfg_file` not changed (check permission ?)");
	}
	else
	{
		die("ok@@@File `$cfg_file` updated");
	}
}
elseif(@$_GET['_action'] == 'clear_cache')
{
	$cache_files = (array)glob(WEBSITE_PATH.'/__cache/*.html');
	foreach($cache_files as $file)
	{
		@unlink($file);
	}
	$cache_files = (array)glob(WEBSITE_PATH.'/__cache/*.html');
	$cache_files_rest = count($cache_files);

	if($cache_files_rest > 0)
	{
		die("error@@@Some files in cache are not deleted (please reapply)@@@$cache_files_rest");
	}
	else
	{
		die("ok@@@No files in cache@@@0");
	}
}
elseif(@$_GET['_action'] == 'unblock')
{
	$date = $_GET['date'];
	$ip = $_GET['ip'];

	$sql = "DELETE FROM
						NutsLog
			WHERE
					Application = '_system' AND
					Action = 'login' AND
					IP = INET_ATON('$ip') AND
					DateGMT LIKE '$date %'";

	$nuts->doQuery($sql);

	// add plugin trace
	$plugin->trace("unblock ip `$ip` at $date");


	die("ok@@@IP `$ip` unblocked for $date");

}
elseif(@$_GET['_action'] == 'log')
{
	$id = (int)@$_GET['id'];
	$sql = "DELETE FROM NutsLog WHERE ID = $id";

	$nuts->doQuery($sql);


	die("ok@@@Message #$id deleted");

}
elseif(@$_GET['_action'] == 'htaccess' || @$_GET['_action'] == 'robots')
{
	$file = (@$_GET['_action'] == 'htaccess') ? '.htaccess' : 'robots.txt';
	$contents = @$_POST['contents'];
	if(@!file_put_contents(WEBSITE_PATH.'/'.$file, $contents))
	{
		die("error@@@file `$file` error, please try again or verify if file is writable");
	}
	else
	{
		die("ok@@@file `$file` saved");
	}
}
elseif(@$_GET['_action'] == 'errors_all')
{
	$sql = "DELETE FROM NutsLog WHERE Application = '_fo-error'";
	$nuts->doQuery($sql);
	die("ok@@@All error messages has been deleted");

}
// execution *************************************************************************
$nuts->open(PLUGIN_PATH.'/form.html');

// maintenance
$maintenance_yes_selected = '';
$maintenance_no_selected = '';
if(WEBSITE_MAINTENANCE == false)
	$maintenance_no_selected = 'selected';
else
	$maintenance_yes_selected = 'selected';

$nuts->parse('maintenance_yes_selected', $maintenance_yes_selected);
$nuts->parse('maintenance_no_selected', $maintenance_no_selected);

// error 404
$error404_yes_selected = '';
$error404_no_selected = '';
if(NUTS_LOG_ERROR_404 == false)
	$error404_no_selected = 'selected';
else
	$error404_yes_selected = 'selected';
$nuts->parse('error404_yes_selected', $error404_yes_selected);
$nuts->parse('error404_no_selected', $error404_no_selected);

// error tags
$error_nuts_tags_yes_selected = '';
$error_nuts_tags_no_selected = '';
if(NUTS_LOG_ERROR_TAGS == false)
	$error_nuts_tags_no_selected = 'selected';
else
	$error_nuts_tags_yes_selected = 'selected';
$nuts->parse('error_nuts_tags_yes_selected', $error_nuts_tags_yes_selected);
$nuts->parse('error_nuts_tags_no_selected', $error_nuts_tags_no_selected);


// html_compress
$html_compress_yes_selected = '';
$html_compress_no_selected = '';
if(NUTS_HTML_COMPRESS == false)
	$html_compress_no_selected = 'selected';
else
	$html_compress_yes_selected = 'selected';
$nuts->parse('html_compress_yes_selected', $html_compress_yes_selected);
$nuts->parse('html_compress_no_selected', $html_compress_no_selected);
$nuts->parse('html_compress_time', NUTS_HTML_COMPRESS_TIME);

// firebug
$firebug_yes_selected = '';
$firebug_no_selected = '';
if(FirePHP_enabled == false)
	$firebug_no_selected = 'selected';
else
	$firebug_yes_selected = 'selected';
$nuts->parse('firebug_yes_selected', $firebug_yes_selected);
$nuts->parse('firebug_no_selected', $firebug_no_selected);

// tidy
$tidy_yes_selected = '';
$tidy_no_selected = '';
if(NUTS_TIDY == false)
	$tidy_no_selected = 'selected';
else
	$tidy_yes_selected = 'selected';
$nuts->parse('tidy_yes_selected', $tidy_yes_selected);
$nuts->parse('tidy_no_selected', $tidy_no_selected);

// php config
$icon_error = 'icon-error.gif';
$icon_warning = 'icon-tag-moderator.png';
$icon_ok = 'icon-accept.gif';

// register_globals
$img_register_globals = (ini_get('register_globals')) ? $icon_error : $icon_ok;
$nuts->parse('img_register_globals', $img_register_globals);

// magic_quotes_gpc
$img_magic_quotes_gpc = (ini_get('magic_quotes_gpc')) ? $icon_error : $icon_ok;
$nuts->parse('img_magic_quotes_gpc', $img_magic_quotes_gpc);

// allow_url_fopen
$img_allow_url_fopen = (!ini_get('allow_url_fopen')) ? $icon_error : $icon_ok;
$nuts->parse('img_allow_url_fopen', $img_allow_url_fopen);

// default_charset
$img_default_charset = (!stristr(ini_get('default_charset'), 'utf-8')) ? $icon_warning : $icon_ok;
$nuts->parse('img_default_charset', $img_default_charset);
$nuts->parse('default_charset_val', ini_get('default_charset'));

// ftp_extension
$img_ftp_extension = (!extension_loaded('ftp')) ? $icon_error : $icon_ok;
$nuts->parse('img_ftp_extension', $img_ftp_extension);

// tidy_extension
$img_tidy_extension = (!extension_loaded('tidy')) ? $icon_warning : $icon_ok;
$nuts->parse('img_tidy_extension', $img_tidy_extension);

// error 404
$nuts->parse('error404', NUTS_ERROR404_TEMPLATE);

// error 500
$nuts->parse('error500', NUTS_ERROR_PAGE_REDIRECT);

// ip allowed
$ips_a = WEBSITE_MAINTENANCE_IPS;
$ips_a = str_replace("'", '', $ips_a);
$nuts->parse('ip_allowed', $ips_a);
$nuts->parse('IpUser', $nuts->getIP());

// cache
$arr = (array)glob(WEBSITE_PATH.'/__cache/*.html');
$nb_cache = count($arr);
$nuts->parse('nb_cache', $nb_cache);


// website errors
$sql = "SELECT
				ID AS WebErrorID,
				INET_NTOA(IP) AS WIp,
				DateGMT AS error_date,
				Action AS error_type,
				Resume AS error_url
		FROM
				NutsLog
		WHERE
				Application = '_fo-error' AND
				Deleted = 'NO'
		ORDER BY
				DateGMT DESC";
$nuts->doQuery($sql);
$nuts->parseDbRow("website_errors", '<img src="img/icon-accept.gif" align="absbottom" /><b> your system is ok</b>');



// ip blocked
$sql = "SELECT
				CONCAT(IP,'_',DATE_FORMAT(DateGMT, '%Y%m%d')) as IpID,
				INET_NTOA(IP) AS IpOk,
				DATE_FORMAT(DateGMT, '%Y-%m-%d') AS Date
		FROM
				NutsLog
		WHERE
				Application = '_system' AND
				Action = 'login' AND
				Resume LIKE 'error => %'
		GROUP BY
				IP, Date HAVING COUNT(*) >= 5
		ORDER BY
				Ip,
				DateGMT DESC";
$nuts->doQuery($sql);
$nuts->parseDbRow("ips_blocked", "<p>No ip blocked</p>");

// special init for robots.txt
if(!file_exists(WEBSITE_PATH.'/robots.txt'))
{
	$r_contents = "
User-agent: *
Disallow: /nuts/";

	$r_contents = trim($r_contents);
	@file_put_contents(WEBSITE_PATH.'/robots.txt', $r_contents);

}

// files checking
$files[] = WEBSITE_PATH.'/robots.txt';
$files[] = WEBSITE_PATH.'/.htaccess';
$files[] = WEBSITE_PATH.'/nuts_auto_compress.js';
$files[] = WEBSITE_PATH.'/nuts_auto_compress.css';
$files[] = WEBSITE_PATH.'/__cache';
$files[] = WEBSITE_PATH.'/_tmp';
$files[] = WEBSITE_PATH.'/nuts_uploads';
$files[] = WEBSITE_PATH.'/plugins/_gallery/_tmp';
$files[] = WEBSITE_PATH.'/plugins/_dropbox/_files';
$files[] = WEBSITE_PATH.'/nuts/config.inc.php';
$files[] = WEBSITE_PATH.'/nuts/url_rewriting_rules.inc.php';


// special for library media
$files[] = WEBSITE_PATH.'/library/media/images';
$files[] = WEBSITE_PATH.'/library/media/images/gallery';
$files[] = WEBSITE_PATH.'/plugins/_gallery/_tmp';
$files[] = WEBSITE_PATH.'/library/media/images/gallery_images';
$files[] = WEBSITE_PATH.'/library/media/images/gallery_images_hd';
$files[] = WEBSITE_PATH.'/library/media/images/news';
$files[] = WEBSITE_PATH.'/library/media/multimedia';
$files[] = WEBSITE_PATH.'/library/media/other';


$arr = (array)glob(WEBSITE_PATH.'/plugins/*/config.inc.php');
$files = array_merge($files, $arr);

$arr = (array)glob(WEBSITE_PATH.'/plugins/*/info.yml');
$files = array_merge($files, $arr);


$errors = 0;
foreach($files as $file)
{
	$type = (is_dir($file)) ? 'folder' : 'file';
	$res = (is_writable($file)) ? 'accept' : 'error';

	if(!is_writable($file))
	{
		$file = '<span style="color:red; font-weight:bold;">'.$file.'</span>';
		$errors++;
	}

	$nuts->parse('files.type', $type);
	$nuts->parse('files.res', $res);
	$nuts->parse('files.file', $file);
	$nuts->loop('files');
}

if(!$errors)
	$nuts->eraseItem('sytem_errors');
else
	$nuts->parse('sytem_errors', "$errors errors");


// htaccess
$htaccess = @file_get_contents(WEBSITE_PATH.'/.htaccess');
$nuts->parse('htaccess', $htaccess);

// robots.txt
$robots = @file_get_contents(WEBSITE_PATH.'/robots.txt');
$nuts->parse('robots', $robots);



$plugin->render = $nuts->output();






?>