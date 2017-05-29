<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

$global_cfg_file = WEBSITE_PATH."/nuts/config.inc.php";
if(!isset($_GET['f']) || !file_exists(base64_decode($_GET['f'])))
	$cfg_file = $global_cfg_file;
else
{
	$cfg_file = base64_decode($_GET['f']);
	$plugin->trace($cfg_file);
}

if($_POST)
{
	// save to file
	$_POST['Configuration'] = trim($_POST['Configuration']);
	$_POST['Configuration'] = str_replace("\\\\", "\\", $_POST['Configuration']);
	$_POST['Configuration'] = str_replace("\\", "\\\\", $_POST['Configuration']);

	// is php file extension ?
	$fext = @strtolower(end(explode('.', $_POST['f'])));
	if($fext == 'php')
	{
		$_POST['Configuration'] = "<?php\n\n".trim($_POST['Configuration'])."\n\n";
	}

	// $_POST['f'] = str_replace('config.inc.php', '__backup_config.inc.php', $_POST['f']);
	if(!@file_put_contents($_POST['f'], $_POST['Configuration']))
	{
		$plugin->trace("`{$_POST['f']}` => post error");
		die('error');
	}
	else
	{
		$data = array();
		$data['_file'] = $_POST['f'];
		$data['Content'] = $_POST['Configuration'];
		nutsVersioningAdd('_configuration::'.basename($_POST['f']), 0, $data, [], $_SESSION['NutsUserID']);
		
		$plugin->trace("`{$_POST['f']}` modified");
		die('ok');
	}
}

// get configuration files
$select_config_files = '<option value="'.$global_cfg_file.'">Global configuration</option>"'."\n";
// $select_config_files .= '<option value="'.WEBSITE_PATH.'/.htaccess">.htaccess</option>"'."\n";
$select_config_files .= '<option value="'.NUTS_THEMES_PATH.'/default/style.css">RTE default style.css</option>"'."\n";

$cfs = glob(WEBSITE_PATH.'/plugins/*/config.inc.php');
foreach($cfs as $cf)
{
	$name = str_replace(WEBSITE_PATH.'/plugins/', '', $cf);
	$name = str_replace('/config.inc.php', '', $name);
	if($name[0] == '_')$name[0] = '';
	$name = str_replace('-', ' ', $name);
	$name = trim(ucwords($name));
	$select_config_files .= '<option value="'.$cf.'">'.ucfirst($name).'</option>"'."\n";
}


$lng = strtolower($_SESSION['Language']);
$cfg = file_get_contents($cfg_file);
$cfg = str_replace('<?php', '', $cfg);
$cfg = str_replace('?>', '', $cfg);
$cfg = trim($cfg);

$GLOBALS['Syntax'] = 'php';
if(preg_match('/\.css$/i', $cfg_file))
	$GLOBALS['Syntax'] = 'css';

$nuts->open(PLUGIN_PATH.'/form.html');
$nuts->parse('cfg', $cfg);
$plugin->render = $nuts->output();


