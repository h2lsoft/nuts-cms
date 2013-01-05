<?php
/**
 * NUTS CMS - Installer
 */

function msg($res, $msg)
{
	$str = '<div class="msg_'.$res.'">'.$msg.'</div>';
	return $str;
}

if($_POST && @$_GET['ajax'] == 1 && @$_POST['wi_step'])
{
	$error = false;
	$error_msg = "";
	$result = "";
	
	$website_root = @parse_url($_POST['WEBSITE_URL']);
		
	// 1.welcome
	if($_POST['wi_step'] == 1)
	{		
		if(@empty($_POST['WEBSITE_PATH']) || !is_dir($_POST['WEBSITE_PATH']))
		{
			$error = true;
			$error_msg = "`{$_POST['WEBSITE_PATH']}` doesn't exist";
		}
		elseif(@empty($_POST['WEBSITE_URL']))
		{
			$error = true;
			$error_msg = "`WEBSITE URL` is empty";
		}
		elseif(@!empty($website_root['path']))
		{
			$error = true;
			$error_msg = "`WEBSITE URL` must at root (ex: http://www.mywebsite.com or http://localhost or http://mywebsite.localhost)";			
		}		
		elseif(@empty($_POST['ADMIN_EMAIL']))
		{
			$error = true;
			$error_msg = "Admin email is empty";
		}
		elseif(@!filter_var($_POST['NO_REPLY_EMAIL'], FILTER_VALIDATE_EMAIL))
		{
			$error = true;
			$error_msg = "No-reply email is not a correct email address";
		}
		elseif(@!filter_var($_POST['ADMIN_EMAIL'], FILTER_VALIDATE_EMAIL))
		{
			$error = true;
			$error_msg = "Admin email is not a correct email address";
		}
		else # system compatibility
		{
			// PHP 5.1 or higher
			$php_v = phpversion();
			$msg = "Php version 5.1 or higher";
			$res = ($php_v <= 5.1) ? 'error' : 'ok';				
			$result .= msg($res, $msg);
			
			// upload file
			$msg = "Upload file right";
			$res = (!ini_get('file_uploads')) ? 'error' : 'ok';				
			$result .= msg($res, $msg);
			
			// gd library
			$msg = "GD library installed";
			$res = (!extension_loaded('gd')) ? 'error' : 'ok';				
			$result .= msg($res, $msg);
			
			// pdo mysql
			$msg = "PDO library installed with MySQL";
			$res = (!extension_loaded('pdo')) ? 'error' : 'ok';
			$result .= msg($res, $msg);
			
			// magic_quotes_gpc OFF
			$msg = "Magic quotes gpc is Off";
			$res = (ini_get('magic_quotes_gpc')) ? 'error' : 'ok';
			$result .= msg($res, $msg);
			
			// register_globals
			$msg = "Register globals is Off";
			$res = (ini_get('register_globals')) ? 'error' : 'ok';
			$result .= msg($res, $msg);
		}
	}
	else
	{
		define('WEBSITE_PATH', $_POST['WEBSITE_PATH']);
		define('WEBSITE_URL', $_POST['WEBSITE_URL']);
	}
	
	// 3. System compatibility
	if($_POST['wi_step'] == 2)
	{
		
		// files checking
		$files = array();
		
		$files[] = WEBSITE_PATH.'/robots.txt';
		$files[] = WEBSITE_PATH.'/.htaccess';
		$files[] = WEBSITE_PATH.'/nuts_auto_compress.js';
		$files[] = WEBSITE_PATH.'/nuts_auto_compress.css';
		$files[] = WEBSITE_PATH.'/__cache';
		// $files[] = WEBSITE_PATH.'/_tmp';
        $files[] = WEBSITE_PATH.'/library/js/tiny_mce/plugins/file_browser/cache';
		$files[] = WEBSITE_PATH.'/nuts_uploads';
		$files[] = WEBSITE_PATH.'/plugins/_gallery/_tmp';
		$files[] = WEBSITE_PATH.'/plugins/_dropbox/_files';
		$files[] = WEBSITE_PATH.'/nuts/config.inc.php';
		$files[] = WEBSITE_PATH.'/nuts/url_rewriting_rules.inc.php';
        $files[] = WEBSITE_PATH.'/plugins/_edm/_repository';

		// special for library media
		$files[] = WEBSITE_PATH.'/library/media/images';
		$files[] = WEBSITE_PATH.'/library/media/images/avatar';
		$files[] = WEBSITE_PATH.'/library/media/images/gallery';
		$files[] = WEBSITE_PATH.'/library/media/images/gallery_images';
		$files[] = WEBSITE_PATH.'/library/media/images/gallery_images_hd';
		$files[] = WEBSITE_PATH.'/library/media/images/news';
		$files[] = WEBSITE_PATH.'/library/media/multimedia';
		$files[] = WEBSITE_PATH.'/library/media/other';
        $files[] = WEBSITE_PATH.'/library/media/images/user/nuts_news_models';
        $files[] = WEBSITE_PATH.'/library/media/images/user/nuts_block_preview';
        $files[] = WEBSITE_PATH.'/library/media/images/user/nuts_rss';

		$arr = (array)glob(WEBSITE_PATH.'/plugins/*/config.inc.php');
		$files = array_merge($files, $arr);
		
		$arr = (array)glob(WEBSITE_PATH.'/plugins/*/info.yml');
		$files = array_merge($files, $arr);
		
		$error_nb = 0;
		foreach($files as $file)
		{
			$type = (is_dir($file)) ? 'folder' : 'file';
			$res = (is_writable($file)) ? 'ok' : 'error';			
			$msg = ucfirst($type)." `$file` is not writable";
			
			if($res == 'error')
			{
				$result .= msg($res, $msg);
				$error_nb++;
			}
		}
		
		if(!$error_nb)
		{
			$result = msg('ok', "No wright folders/files problems detected");
		}
		
	}
	// 4. DB conf
	if($_POST['wi_step'] == 4)
	{
		try {
				$pdo = new PDO(
						'mysql:host='.$_POST['DB_HOST'].';dbname='.$_POST['DB_NAME'],
						$_POST['DB_LOGIN'],
						$_POST['DB_PASS'],
						array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
					);				
				$error = false;
				
				// launch big treatment **********************************************************************
				$sqls = file_get_contents('install.sql')."\n".file_get_contents('after.sql');
                if($_POST['DB_LANG'] == 'FR')
                    $sqls .= "\n".file_get_contents('after_FR.sql');

				$sqls = str_replace('MyNuts', $_POST['DB_NAME'], $sqls);
				$sqls = str_replace(":= 'admin@domain.com';", ":= '{$_POST['ADMIN_EMAIL']}';", $sqls);				
				$sqls = str_replace("\r", "", $sqls);
				$sqls = explode(";\n", $sqls);
				
				foreach($sqls as $sql)
				{
					$sql = trim($sql);
					if(!empty($sql))
					{
						if(preg_match("/^CREATE TABLE/", $sql))
						{
							$sql = str_replace('TYPE=', 'ENGINE=', $sql);
                            $sql = str_replace('[[WEBSITE_NAME]]', $_POST['WEBSITE_NAME'], $sql);
                            $sql = str_replace('[[WEBSITE_URL]]', $_POST['WEBSITE_URL'], $sql);
						}
						
						$pdo->query($sql);
					}
				}
				
				// writing file config ************************************************************************
				$cfg = file_get_contents('config.inc.php');
				$arr = array();				
				$arr['WEBSITE_NAME'] = $_POST['WEBSITE_NAME'];
				$arr['WEBSITE_PATH'] = $_POST['WEBSITE_PATH'];
				$arr['WEBSITE_URL'] = $_POST['WEBSITE_URL'];
				$arr['ADMIN_EMAIL'] = $_POST['ADMIN_EMAIL'];
				$arr['NO_REPLY_EMAIL'] = $_POST['NO_REPLY_EMAIL'];
				$arr['NUTS_CRYP_KEY'] = utf8_decode(md5(uniqid('ncr')));
				$arr['NUTS_RTE_FILEBROWSER_OBFUSCATE_KEY'] = md5(uniqid('nobf'));
				$arr['DB_HOST'] = $_POST['DB_HOST'];
				$arr['DB_LOGIN'] = $_POST['DB_LOGIN'];
				$arr['DB_PASS'] = $_POST['DB_PASS'];
				$arr['DB_NAME'] = $_POST['DB_NAME'];
				foreach($arr as $key => $val)
				{
					$cfg = str_replace("[[$key]]", $val, $cfg);
				}				
				
				// remove line protection
				$cfg = str_replace("if(is_dir('install'))", "# if(is_dir('install'))", $cfg);				
				file_put_contents('../nuts/config.inc.php', $cfg);
				
				// update crypt key ****************************************************************************
				$pdo->query("UPDATE NutsUser SET `Password` = ENCODE('admin', '{$arr['NUTS_CRYP_KEY']}')");
								
				// writing file sitemap key ************************************************************************
				$sub = 'adse861d2M1df3sdf55';
				$f = file_get_contents('../plugins/_sitemap/config.inc.php');
				$f = str_replace($sub, uniqid('key'), $f);
				file_put_contents('../plugins/_sitemap/config.inc.php', $f);
								
				// writing file search-engine key ************************************************************************
				$sub = 'CAkuc7ax5CheTHus';
				$f = file_get_contents('../plugins/_search-engine/config.inc.php');
				$f = str_replace($sub, uniqid('key'), $f);
				file_put_contents('../plugins/_search-engine/config.inc.php', $f);
				
				// writing sitemap.xml ************************************************************************
				$f = file_get_contents('../sitemap.xml');
				$f = str_replace('http://nuts-test/', $_POST['WEBSITE_URL'].'/', $f);
				file_put_contents('../sitemap.xml', $f);
				
				// writing spider ************************************************************************
				$sql = "UPDATE NutsSpider SET Url = REPLACE(Url, 'http://nuts-test/', '{$_POST['WEBSITE_URL']}/')";
				$pdo->query($sql);
				
				// writing form builder ************************************************************************
				$sql = "UPDATE NutsForm SET FormValidMailerFrom = '{$_POST['NO_REPLY_EMAIL']}', FormValidMailerTo = '{$_POST['ADMIN_EMAIL']}'";
				$pdo->query($sql);


                // fr replace url_rewrint contents
                if($_POST['DB_LANG'] == 'FR')
                {
                    $f = file_get_contents('../nuts/url_rewriting_rules.inc.php');
                    $f = str_replace('/en/', '/fr/', $f);
                    file_put_contents('../nuts/url_rewriting_rules.inc.php', $f);
                }
								
				
		} catch (PDOException $e) {
			
			$error = true;
			$GLOBALS['error_msg'] = "MySQL :\n\n".$e->getMessage();
			
		}
		
		
	}
	
	
	die(json_encode(array('error' => $error, 'error_msg' => $error_msg, 'result' => $result)));
	
	
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="chrome=1">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="robots" content="noindex" />
		<title>NUTS CMS - Installer</title>
		
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>		
		<script type="text/javascript" src="jquery.blockUI.js"></script>		
		<script language="javascript" src="../library/js/php.js"></script>
		<script language="javascript" src="func.js"></script>
		
		<link rel="stylesheet" type="text/css" href="../nuts/css/style.css" />
		<link rel="stylesheet" media="screen" href="style.css" />
		
		<script>
		var wi_step = 1;
		</script>
		
	</head>
	
	<body>
		
	<!-- header -->
	<div id="header">
		<span><a href=""><img src="../nuts/img/logo.png" alt="Nuts Cms" align="left" /></a></span>
	</div>
	<!-- header -->
	
	<!-- menu -->
	<div id="menu">&nbsp;</div>
	<!-- /menu -->
	
	
	<!-- ni_left -->
	<div id="ni_left">
		
		<ol>
			<li class="selected">Welcome</li>
			<li>System compatibility</li>
			<li>System configuration</li>
			<li>Database configuration</li>
			<li>Finish</li>
		</ol>
		
	</div>
	<!-- /ni_left -->
	
	
	<!-- ni_content -->
	<div id="ni_content">
		
		
		<!-- welcome -->
		<div class="ni_content_text" style="display: block;">
			
			<h1>Welcome !</h1>
			<h4>
				Welcome to Nuts&trade; CMS Installer wizard.		
			</h4>
			
			<fieldset>
				<legend>Global configuration</legend>
				
				<p>
					<label>Website name</label>
					<input type="text" id="WEBSITE_NAME" name="WEBSITE_NAME" value="<?php echo @ucwords(@str_replace(array('www.', '-'), array('',' '), $_SERVER['SERVER_NAME'])); ?>" />
				</p>
				
				<p>
					<label>Website url (full with no slash at end)</label>
					<input type="text" id="WEBSITE_URL" name="WEBSITE_URL" value="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>" />					
				</p>
				<p>
					<label>Website path (full with no slash at end)</label>
					<input type="text" id="WEBSITE_PATH" name="WEBSITE_PATH" value="<?php echo $_SERVER['DOCUMENT_ROOT']; ?>" />					
				</p>
				<p>
					<label>No-reply email (use by default for automatic email)</label>
					<input type="text" id="NO_REPLY_EMAIL" name="NO_REPLY_EMAIL" value="no-reply@<?php echo str_replace('www.', '', $_SERVER['HTTP_HOST']); ?>" />
				</p>
				
				<fieldset>
					
					<legend>Super admin</legend>
					
						<p>
							<label>Admin email (super administrator)</label>
							<input type="text" id="ADMIN_EMAIL" name="ADMIN_EMAIL" value="" />
						</p>
					
				</fieldset>
				
			</fieldset>			
		</div>
		<!-- /welcome -->
		
		
		<!-- System compatibility -->
		<div class="ni_content_text">
			
			<h1>System compatibility</h1>
			<h4>
				Nuts&trade; CMS Installer wizard will check your system
			</h4>
			
			<fieldset>
				<legend>System compatibility</legend>
				
				<p id="result_2"></p>
				
			</fieldset>
			
			
		</div>
		<!-- /System compatibility -->
		
		
		<!-- writable rights -->
		<div class="ni_content_text">
			
			<h1>System configuration</h1>
			<h4>
				Nuts&trade; CMS Installer wizard will check your system folders/files rights
			</h4>
			
			<fieldset>
				<legend>System configuration</legend>
				
				<p id="result_3">
					
				</p>
				
			</fieldset>
			
			
		</div>
		<!-- /writable rights -->
		
		<!-- db conf -->
		<div class="ni_content_text">
			<h1>Database configuration</h1>
			<h4>
				Nuts&trade; CMS Installer wizard database access
			</h4>
			<fieldset>
				<legend>Database configuration</legend>
				
				<p>
					<label>Host</label>
					<input type="text" id="DB_HOST" name="DB_HOST" value="localhost" />					
				</p>
				<p>
					<label>Login</label>
					<input type="text" id="DB_LOGIN" name="DB_LOGIN" value="" />					
				</p>
				<p>
					<label>Password</label>
					<input type="password" id="DB_PASS" name="DB_PASS" value="" />
				</p>
				<p>
					<label>Database</label>
					<input type="text" id="DB_NAME" name="DB_NAME" value="" />					
				</p>
				<p>
					<label>Data language</label>

					<select id="DB_LANG" name="DB_LANG">
                        <option value="EN" selected>English</option>
                        <option value="FR">Français</option>
                    </select>

				</p>


			</fieldset>
			
		</div>
		<!-- /db conf -->
		
		
		<!-- finish -->
		<div class="ni_content_text">
			<h1>Finish !</h1>
			<h4>Nuts&trade; CMS Installer 100%</h4>
			
			
			<fieldset>
				
				<legend>Installation complete</legend>
				
				<div class="warning">
					
					<img src="../nuts/img/icon-tag-moderator.png" alt=" " align="absmiddle" />					
					<span>&bull; For security reason, please delete the folder `/install/`</span><br />					
					
					<br />					
					<br />
					
					<b>You can login with :</b><br />
					<br />
					
					<b>Login :</b> `admin`<br />
					<b>Password :</b> `admin`<br />
					<br />
					<span>&bull; Don't forget to change your password</span><br />					
					<br />
					
					<input type="checkbox" id="Register" value="1" checked /> Allow registration installation: only admin email, website url and website name will be sended (recommanded)<br />
					<span>No confidential data will be transmitted and we will not use or sell your details for unsolicited emails.</span>
					<br />
					<br />					
					
					
					<input type="button" value="&raquo; Go to Back-office" onclick="registration()">
					
				</div>
				
			</fieldset>
			
			
		</div>
		<!-- /finish -->
		
	
		<!-- ni_content_bottom_bar -->
		<div id="ni_content_bottom_bar">
			
			<input type="button" id="btn_prev" value="&laquo; Previous"  onclick="stepPrev()" />
			<input type="button" id="btn_next" value="Next &raquo;" onclick="stepNext()" />
			
		</div>
		<!-- /ni_content_bottom_bar -->
		
		
	</div>
	<!-- /ni_content -->

	
	<script>
	function registration()
	{
		if(!$('#Register:checked'))
		{
			document.location.href='/nuts/login.php';
		}	
		else
		{
			url = 'http://www.nuts-cms.com/_new_install.srv.php';
			$.get(url, {w_u: escape($('#WEBSITE_URL').val()), w_n: escape($('#WEBSITE_NAME').val()), a_e: escape($('#ADMIN_EMAIL').val())});			
			
			setTimeout(function(){				
				document.location.href = '/nuts/login.php';				
			}, 3000); 
			
			
			
		}
	}
	</script>
	
	
				
	</body>
	
</html>