<?php

/* @var $plugin Page */
/* @var $nuts Page */

include(PLUGIN_PATH.'/config.inc.php');

// special func *************************************************************************
function rrmdir($dir, $parent) {
   if(is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if($object != "." && $object != "..") {
         if(filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object, $parent); else @unlink($dir."/".$object);
       }
     }
     reset($objects);
     if($dir != $parent)@rmdir($dir);
   }
}

// to use this function to totally remove a directory, write:
// recursive_remove_directory('path/to/directory/to/delete');
function recursive_remove_directory($directory, $empty=FALSE)
{
    if(substr($directory,-1) == '/')
    {
        $directory = substr($directory,0,-1);
    }

    // if the path is not valid or is not a directory ...
    if(!file_exists($directory) || !is_dir($directory))
    {
        return FALSE;


    }elseif(!is_readable($directory))
    {
        return FALSE;

    }else{

        $handle = @opendir($directory);
        while (FALSE !== ($item = @readdir($handle)))
        {
            // if the filepointer is not the current directory
            // or the parent directory
            if($item != '.' && $item != '..')
            {
                // we build the new path to delete
                $path = $directory.'/'.$item;

                if(is_dir($path))
                {
                    @recursive_remove_directory($path);
                }else{
                    @unlink($path);
                }
            }
        }

        @closedir($handle);

        // if the option to empty is not set to true
        if($empty == FALSE)
        {
            if(!rmdir($directory))
            {
                return FALSE;
            }
        }
        // return success
        return TRUE;
    }
}
// ------------------------------------------------------------



function nutsDirectoryToArray($directory, $recursive=true) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory. "/" . $file)) {
					if($recursive) {
						$array_items = array_merge($array_items, nutsDirectoryToArray($directory. "/" . $file, $recursive));
					}
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				} else {
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}

// controller *************************************************************************
if(!isset($_GET['step']))$_GET['step'] = 0;
$_GET['step'] = (int)$_GET['step'];

// execution *************************************************************************
if(($conn_id_rep = @ftp_connect($ftp_server, $ftp_port, $ftp_timeout)))
	$conn_id_rep_login = @ftp_login($conn_id_rep, $ftp_user, $ftp_pwd);

@ftp_pasv($conn_id_rep, true);

// ftp connection
if(!$conn_id_rep || !$conn_id_rep_login)
{
	$output = "Error: please verify service updater `{$ftp_server}` (firewall actived in your server ?) \n";
}
else
{
	// <editor-fold defaultstate="collapsed" desc="get version *******************************************************">
	@ftp_chdir($conn_id_rep, $ftp_dir);

	$last_version = '';
	$next_version = '';

	$buff = ftp_nlist($conn_id_rep, '.');

	$versions = array();
	foreach($buff as $f)
	{
		if(preg_match('/\.txt$/', $f))
		{
			$f_num = str_replace('.txt', '', $f);
			/*if($last_version < $f_num)$last_version = $f_num;
			if(empty($next_version))$next_version = $last_version;
			if($f_num > NUTS_VERSION && $f_num)$next_version = $last_version;*/
			$versions[] = $f_num;
		}
	}

	sort($versions);
	foreach($versions as $v)
	{
		if($last_version < $v)$last_version = $v;
		// we find the first element greater than current version
		if($v > NUTS_VERSION && empty($next_version))$next_version = $v;
	}
	if(empty($next_version))$next_version = NUTS_VERSION;
	// </editor-fold>

	// output ************************************************************
    if($_GET['step'] == -2)
    {
        $action_end = ($debug_mode) ? 'off' : 'on';
        $output = "<b>-> Turn debug mode to $action_end</b>";

        $updater_cfg_path = WEBSITE_PATH.'/plugins/_updater/config.inc.php';
        $c = file_get_contents($updater_cfg_path);

        if($action_end == 'off')
        {
            $ori = '$debug_mode = true;';
            $rep = '$debug_mode = false;';
        }
        else
        {
            $ori = '$debug_mode = false;';
            $rep = '$debug_mode = true;';
        }

        $c = str_replace($ori, $rep, $c);
        if(@!file_put_contents($updater_cfg_path, $c))
        {
            $output .= " :  <i>failed</i> file is not writable";
        }
        else
        {
            $debug_mode =  ($action_end == 'off') ? false : true;
        }

    }
    elseif($_GET['step'] == -1)
	{
		$output = "<b>-> Get last updater engine</b>";

		$updater_path = WEBSITE_PATH.'/plugins/_updater/exec.inc.php';
		if(!is_writable($updater_path))
		{
			$output .= " :  <i>failed</i> file is not writable";
		}
		else
		{
			// backup version
			if(!copy($updater_path, $updater_path.'.backup'))
			{
				$output .= " :  <i>failed</i> backup updater engine (verify your rights)";
			}
			else
			{
				if(!@ftp_get($conn_id_rep, WEBSITE_PATH.'/plugins/_updater/exec.inc.php', "_last-updater.php", FTP_BINARY))
				{
					$err = true;
					$php_last_error = error_get_last();
					$output .= " : <i>failed</i> (".@$php_last_error['message'].")";

					// restore backup
					@copy($updater_path.'.backup', $updater_path);
					$output .= " original copy restored";
				}
				else
				{
					@unlink($updater_path.'.backup');
					$output .= " : <u>success ! You can update your Nuts CMS version</u>";
				}
			}
		}
	}
	elseif($_GET['step'] == 0)
	{
		if($next_version == NUTS_VERSION)$output = "<u>You have the latest version</u>";
		elseif($next_version < NUTS_VERSION)$output = "<i>Error: version minor than your nuts version</i>";
		elseif($next_version > NUTS_VERSION){$btn_display = "block"; $output = "";}
	}
	elseif($_GET['step'] == 1 && $next_version == NUTS_VERSION)
	{
		$output = "<u>You have the latest version, no update available</u>";
	}
	elseif($_GET['step'] == 1 && $next_version > NUTS_VERSION)
	{
		set_time_limit(0);
		$err = false;
		$original_maintenance = WEBSITE_MAINTENANCE;
		$output = '';

		// <editor-fold defaultstate="collapsed" desc="check tmp dir">
		if(!$err)
		{
			$output .= "<b>-> Check tmp dir `".WEBSITE_PATH.'/_tmp'."`</b>\n";
			if(!is_dir(WEBSITE_PATH.'/_tmp') && !@mkdir(WEBSITE_PATH.'/_tmp', 0755))
			{
				$err = true;
				$output .= "<i>-> Error tmp directory not found and no creation possible `".WEBSITE_PATH.'/_tmp'."`</i>\n";
				usleep(100);
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="clean _tmp dir">
		if(!$err)
		{
			$tmp_dir = WEBSITE_PATH.'/_tmp';
			if(is_dir($tmp_dir))
			{
				rrmdir($tmp_dir, $tmp_dir);
				$tmp_files_listed = glob("$tmp_dir/*", GLOB_MARK);
				if(is_array($tmp_files_listed) && count($tmp_files_listed) > 0)
				{
					$err = true;
					$output .= "<i>-> Error can't delete tmp directory contents `".WEBSITE_PATH.'/_tmp'."`</i>\n";
				}
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="get commands version">
		if(!$err)
		{
			$output .= "<b>-> Get file `$next_version.txt`</b> ";
			if(!@ftp_get($conn_id_rep, WEBSITE_PATH.'/_tmp/'.$next_version.'.txt', $next_version.".txt", FTP_BINARY))
			{
				$err = true;
				$php_last_error = error_get_last();
				$output .= " : <i>failed</i> (".@$php_last_error['message'].")";
			}
			$output .= "\n";
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="verify write commands">
		if(!$err)
		{
			// all files
			$ins = file_get_contents(WEBSITE_PATH.'/_tmp/'.$next_version.'.txt');
			$ins = explode("\n", $ins);
			foreach($ins as $in)
			{
				$in = trim($in);
				if(!empty($in) && $in[0] != '#')
				{
					if(preg_match('/^\[W\]/', $in))
					{
						$in = str_replace('[W]', '', $in);
						$in = trim($in);
						list($file, $line) = explode("\t", $in);
						if(!is_writable(WEBSITE_PATH.'/'.$file))
						{
							$err = true;
							$output .= "<i>Error: file `$file` not writable </i>\n";
						}
					}
				}
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="verify main file /nuts/info.yml">
		if(!$err)
		{
			$ins = file_get_contents(WEBSITE_PATH.'/_tmp/'.$next_version.'.txt');
			$ins = explode("\n", $ins);
			$file = 'nuts/info.yml';
			if(!is_writable(WEBSITE_PATH.'/'.$file))
			{
				$err = true;
				$output .= "<i>Error: file  `$file` not writable</i>\n";
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="get complete ftp file list">
		if(!$err)
		{
			$output .= "<b>-> Get complete files list</b>\n";

			@ftp_chdir($conn_id_rep, $next_version);

			// if(!($files_list = @ftp_nlist($conn_id_rep, '-aFpR .')))
			if(!($files_list =  @ftp_rawlist($conn_id_rep, '-A .', true)))
			{
				$output .= "<i>Error: couldn't get file list </i>\n";
				$err = true;
			}


			// here the magic begins!
			$last_path = "";
			$structure = array();
			foreach($files_list as $file_list)
			{
				$line = explode(" ", $file_list);

				$f = end($line);
				if(!empty($f) && $f[strlen($f)-1] == ':')
				{
					$last_path = str_replace(':', '/', $f);
				}

                // add exception
				if(!empty($f) && $f[strlen($f)-1] != ':' && (strpos($f, '.') !== false || (in_array($f, array('en', 'fr')) && $last_path == '')))
				{
					$fs = explode('.', $f);
                    $fs = end($fs);

					// file extension only, extension 002 is allowed for folder with number
					if(strlen($fs) <= 4 && !ctype_digit($fs))
						$structure[] = './'.$next_version.'/'.$last_path.$f;
				}
			}

			$files_list = array_unique($structure);

		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="create the real path for each dir/file">
		if(!$err)
		{
			/*$files_list_parsed = array();
			$last_parent_path = './'.$next_version.'/';
			foreach($files_list as $f)
			{
				if(!preg_match('/\//', $f) && !empty($f) && $f != 'dwsync.xml')
				{
					$files_list_parsed[] = $last_parent_path.$f;
				}
				elseif(strlen($f) > 0 && ($f[0] == '.' && $f[strlen($f)-1] == ':'))
				{
					$last_parent_path = str_replace(':', '/', $f);
					$last_parent_path = str_replace('./', './'.$next_version.'/', $last_parent_path);
				}

				if(empty($f))
				{
					$last_parent_path = './'.$next_version.'/';
				}
			}
			$files_list = array_unique($files_list_parsed);*/

		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="verify tmp directory creation/permission">
		if(!$err)
		{
			$output .= "<b>-> Verify tmp directory creation/permission</b>\n";
			$path_done = array();
			foreach($files_list as $fl)
			{
				$fl = str_replace('./'.$next_version.'/', '', $fl);

				$dirs = explode('/', $fl);
				$path = '';
				for($i = 0;  $i < count($dirs)-1; $i++)
				{
					if(!empty($path))$path .= '/';
					$path .= $dirs[$i];
					if(!in_array($path, $path_done))
					{
						if(!is_dir(WEBSITE_PATH.'/_tmp/'.$path))
						{
							$res = (!$chmod_default) ? mkdir(WEBSITE_PATH.'/_tmp/'.$path, 0755) : mkdir(WEBSITE_PATH.'/_tmp/'.$path, $chmod_default);
							if(!$res)
							{
								$output .= "<i>Error: couldn't create dir `".WEBSITE_PATH.'/_tmp/'.$path."` </i>\n";
								$err = true;
							}
							if(!empty($system_correct_user) && !@chown(WEBSITE_PATH.'/_tmp/'.$path, $system_correct_user))
							{
								$output .= "<i>Error: couldn't chown dir `".WEBSITE_PATH.'/_tmp/'.$path."` </i>\n";
								$err = true;
							}
						}
						usleep(100);

						if(!is_writable(WEBSITE_PATH.'/_tmp/'.$path))
						{
							$output .= "<span style='color:red'>Error: dir is not writable `".WEBSITE_PATH.'/_tmp/'.$path."` </span>\n";
							$err = true;
						}
						usleep(100);

						$path_done[] = $path;
					}
				}
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="verify file by file writable">
		if(!$err)
		{
			$output .= "<b>-> Verify file by file right</b>\n";
			foreach($files_list as $fl)
			{
				$fl = str_replace('./'.$next_version.'/', '', $fl);
				$fl2 = str_replace('htaccess', '.htaccess', $fl);

				if(file_exists(WEBSITE_PATH.'/'.$fl) && !is_writable(WEBSITE_PATH.'/'.$fl))
				{
					$err = true;
					$output .= "File not writable `$fl2`: <span style='color:red'>failed</span> \n";
				}
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="put ftp files to tmp directory">
		if(!$err)
		{
			$output .= "<b>-> Put ftp files to tmp directory</b>\n";
			foreach($files_list as $fl)
			{
				$fl = str_replace('./'.$next_version.'/', '', $fl);
				$fl2 = str_replace('htaccess', '.htaccess', $fl);

				if(!@ftp_get($conn_id_rep, WEBSITE_PATH.'/_tmp/'.$fl2, $fl, FTP_BINARY))
				{
					$err = true;
					$php_error = error_get_last();
					$output .= "    -> Get file `$fl2` ";
					$output .= ": <i>failed (".$php_error["message"].") </i>\n";
				}
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="change default maintenance file">
		if(!$debug_mode && !$original_maintenance && !$err)
		{
			$output .= "<b>-> Change the status of maintenance to ON</b>\n";

			$config_file = 'config.inc.php';
			$config_file_txt = file_get_contents($config_file);
			$config_file_txt = str_replace("define('WEBSITE_MAINTENANCE', false);", "define('WEBSITE_MAINTENANCE', true);", $config_file_txt);

			if(!@file_put_contents($config_file, $config_file_txt))
			{
				$err = true;
				$output .= "<i>-> Error can't change maintenance status</i>\n";
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="move file to real destination">
		if(!$err)
		{
			$output .= "<b> -> Move tmp files to web root</b>\n";

			$folder_source = WEBSITE_PATH.'/_tmp';

			if($debug_mode)
				$folder_dest = WEBSITE_PATH.'/_tmp-test';
			else
				$folder_dest = WEBSITE_PATH;

			$files = nutsDirectoryToArray($folder_source);
			$files = array_unique($files);
			sort($files);

			foreach($files as $file)
			{
				$file_dest = str_replace($folder_source, $folder_dest, $file);
				if(!is_dir($file) && $file != $folder_source."/".$next_version.".txt")
				{
					// create directory
					$directories = str_replace($folder_source, "", $file);
					$directories = trim($directories);
					$directories = explode('/', $directories);

					$current_dir = $folder_dest;
					for($i=0; $i < count($directories)-1; $i++)
					{
						$current_dir .= "/".$directories[$i];
						$current_dir = str_replace('//', '/', $current_dir);
						if(!is_dir($current_dir))
						{
							$res = (!$chmod_default) ? mkdir($current_dir, 0755) : mkdir($current_dir, $chmod_default);
							if(!$res)
							{
								$err = true;
								$php_error = error_get_last();
								$output .= "    <i>-> Error failed to create dir `$current_dir` (".$php_error["message"].")</i> \n";
							}

							if(!empty($system_correct_user) && !@chown($current_dir, $system_correct_user))
							{
								$output .= "    <i>-> Error: couldn't chown dir `".$current_dir."` </i>\n";
								$err = true;
							}
							usleep(100);
						}
					}

					// copy file
					if(!@copy($file, $file_dest))
					{
						$output .= "    <i>-> Error: couldn't copy file to `".$file_dest."`</i>\n";
						usleep(100);
					}
				}
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="execute final commands">
		if(!$err)
		{
			$output .= "<b>-> Execute final commands</b>\n";
			$ins = file_get_contents(WEBSITE_PATH.'/_tmp/'.$next_version.'.txt');
			$ins = explode("\n", $ins);
			$w_mode_already = false;

			// all files
			foreach($ins as $in)
			{
				$in = trim($in);
				if(!empty($in) && $in[0] != '#')
				{
					$cmd = substr($in, 0, 3);
					$in = str_replace($cmd, '', $in);
					$in = trim($in);

					// plugin installation
					if($cmd == '[I]')
					{
						$params = explode("\t", $in);
						$p = $params[0];
						$cat = $params[1];
						$cat_nb = $cat-1;

						if($debug_mode)
						{
							$output .= "    -> [DEBUG] Install plugin `$p` in `{$mods_group[$cat_nb]['name']}` \n";
						}
						else
						{
							$output .= "    -> Install plugin `$p` in `{$mods_group[$cat_nb]['name']}` \n";
							$nuts->dbInsert('NutsMenu', array('Name' => $p, 'Category' => $cat));
						}
						$w_mode_already = false;
					}
					// query execution
					elseif($cmd == '[Q]')
					{
						$q = $in;

						if($debug_mode)
						{
							$output .= "    -> [DEBUG] Execute query  `$q` \n";
						}
						else
						{
							$output .= "    -> Execute query  `$q` \n";
							$nuts->doQuery($q);
						}
						$w_mode_already = false;
					}
					// write mode
					elseif($cmd == '[W]')
					{
						$params = explode("\t", $in);
						$f = $params[0];
						$line = $params[1];

						if($debug_mode)
						{
							$output .= "    -> [DEBUG] Write line in `$f` => `$line` \n";
						}
						else
						{
							$output .= "    -> Write line in `$f` => `$line` \n";
							if(($content = file_get_contents(WEBSITE_PATH.'/'.$f)))
							{
								if(!$w_mode_already)
								{
									$str_rep = "\n\n/** update $next_version */\n$line\n?>";
								}
								else
								{
									$str_rep = "$line\n?>";
								}

								$content = str_replace("\n\n\n?>", "\n?>", $content);
								$content = str_replace("\n\n?>", "\n?>", $content);
								$content = str_replace('?>', $str_rep, $content);
								$content = str_replace("?>", "\n?>", $content);
								file_put_contents(WEBSITE_PATH.'/'.$f, $content);

								$w_mode_already = true;
							}
						}
					}
                    // delete mode
					elseif($cmd == '[X]')
                    {
                        $params = explode("\t", $in);
                        $f = @$params[0];
                        $f = trim($f);

                        if(!empty($f))
                        {
                            // folder ?
                            if($f[strlen($f)-1] == '*')
                            {
                                if($debug_mode)
                                {
                                    $output .= "    -> [DEBUG] Remove folder `$f` \n";
                                }
                                else
                                {
                                    $output .= "    -> Remove folder `$f`: ";
                                    $f = str_replace('/*', '/', $f);
                                    $output .= (!@recursive_remove_directory(WEBSITE_PATH.$f)) ? '<i>Error</i>' : 'ok';
                                    $output .= " \n";
                                }
                            }
                            else
                            {
                                if($debug_mode)
                                {
                                    $output .= "    -> [DEBUG] Remove file `$f` \n";
                                }
                                else
                                {
                                    $output .= "    -> Remove file `$f`: ";
                                    $output .= (!@unlink(WEBSITE_PATH.$f)) ? '<i>Error</i>' : 'ok';
                                    $output .= " \n";
                                }
                            }
                        }
                    }
				}
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="update nuts version">
		if(!$err && !$debug_mode)
		{
			$output .= " <b>-> Update Nuts version to $next_version </b> \n";
			if(($content = file_get_contents(WEBSITE_PATH.'/nuts/info.yml')))
			{
				$content = str_replace('version: '.NUTS_VERSION, 'version: '.$next_version, $content);
				file_put_contents(WEBSITE_PATH.'/nuts/info.yml', $content);
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="clean _tmp dir">
		$tmp_dir = WEBSITE_PATH.'/_tmp';
		if(is_dir($tmp_dir))
		{
			rrmdir($tmp_dir, $tmp_dir);
			$tmp_files_listed = glob("$tmp_dir/*", GLOB_MARK);
			if(is_array($tmp_files_listed) && count($tmp_files_listed) > 0)
			{
				$err = true;
				$output .= "<i>-> Error can't delete tmp directory contents `".WEBSITE_PATH.'/_tmp'."`</i>\n";
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="clean _tmp-test dir">
		$tmp_dir = WEBSITE_PATH.'/_tmp-test';
		if($debug_mode && is_dir($tmp_dir))
		{
			rrmdir($tmp_dir, $tmp_dir);
			$tmp_files_listed = glob("$tmp_dir/*", GLOB_MARK);
			if(is_array($tmp_files_listed) && count($tmp_files_listed) > 0)
			{
				$err = true;
				$output .= "<i>-> Error can't delete tmp directory contents `".WEBSITE_PATH.'/_tmp-test'."`</i>\n";
			}
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="change maintenance">
		if(!$original_maintenance && !$debug_mode && !$err)
		{
			$output .= "<b> -> Change the status of maintenance to OFF</b>\n";

			$config_file = 'config.inc.php';
			$config_file_txt = file_get_contents($config_file);
			$config_file_txt = str_replace("define('WEBSITE_MAINTENANCE', true);", "define('WEBSITE_MAINTENANCE', false);", $config_file_txt);

			if(!@file_put_contents($config_file, $config_file_txt))
			{
				$err = true;
				$output .= "    <i>-> Error can't change maintenance status</i>\n";
			}
		}
		// </editor-fold>

		// no error
		if(!$err)
		{
			if($debug_mode)
			{
				$output .= "\n<u>YOU CAN CHANGE DEBUG MODE, NO ERROR DETECTED</u>";
			}
			else
			{

				// reload all rights for SUPERADMIN group **************************************************************
				$sql = "DELETE FROM NutsMenuRight WHERE NutsGroupID = 1";
				$nuts->doQuery($sql);


				$sql = "SELECT ID, Name FROM NutsMenu WHERE Deleted = 'NO'";
				$nuts->doQuery($sql);
				while($row = $nuts->dbFetch())
				{
					$pluginID = $row['ID'];
					$plugin_name = $row['Name'];
					$tmp_cfg = NUTS_PLUGINS_PATH.'/'.$plugin_name.'/info.yml';

					if(file_exists($tmp_cfg))
					{
						$yaml = Spyc::YAMLLoad($tmp_cfg);
						$plugin_actions = explode(',', $yaml['actions']);
						$plugin_actions = array_map('trim', $plugin_actions);

						if(count($plugin_actions) > 0)
						{
							foreach($plugin_actions as $plugin_action)
							{
								if(!empty($plugin_action))
								{
									$f = array();
									$f['NutsMenuID'] = $pluginID;
									$f['NutsGroupID'] = 1;
									$f['Name'] = $plugin_action;
									$nuts->dbInsert('NutsMenuRight', $f, array());
								}
							}
						}
					}
				}
			}

			$output .= "\nReload all rights for SuperAdmin group";
			$output .= "\n\nNuts is updated, please to <a href=\"".WEBSITE_URL."/nuts/index.php?mod=_updater&do=exec\">reload updater</a> then go to <a href=\"".WEBSITE_URL."/nuts/index.php?mod=_right-manager&do=edit\">right manager</a>";
		}
		else
		{
			$output .= "\n\n<i>YOU HAVE SOME ERRORS PLEASE RETRY UPDATER PROCESS</i>";
		}

		// display button to relaunch the updater
		if($err)$btn_display = '';
	}

}

if($debug_mode)
{
	$output = "<i>[WARNING ! YOU ARE IN DEBUG MODE !]</i>\n\n".$output;
}


// command B
$output = str_replace("<b>", '<span style="color:orange">', $output);
$output = str_replace("</b>", '</span>', $output);

// success U
$output = str_replace("<u>", '<span style="color:lightgreen">', $output);
$output = str_replace("</u>", '</span>', $output);

// error I
$output = str_replace("<i>", '<span style="color:lightpink">', $output);
$output = str_replace("</i>", '</span>', $output);

$output = str_replace("    ", '&nbsp;&nbsp;&nbsp;&nbsp;', $output);

$output = nl2br($output);

$GLOBALS['system_correct_user'] = $system_correct_user;

// debug mode style
if($debug_mode)
{
    $debug_style = 'color:white; background:green';
}
else
{
    $debug_style = 'color:white; background:red';
}


$plugin->directRender(PLUGIN_PATH.'/exec.html');


?>