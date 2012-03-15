<?php

$for = 'HOME';
include(WEBSITE_PATH.'/nuts/_inc/trt_menu.inc.php');
// $plugin->trace();

// Maintenance **********************************************************************

// purge logs
include(WEBSITE_PATH."/plugins/_logs/config.inc.php");
if($cf_purge_days > 0)
{
	$gmtdate = nutsGetGMTDate();
	$sql = "DELETE FROM NutsLog WHERE DATE_ADD(DateGMT, INTERVAL $cf_purge_days DAY) <= '$gmtdate' ";
	$nuts->doQuery($sql);
}

$img_error = '<img src="img/icon-error.gif" align="absbottom" />';
$img_warning = '<img src="img/icon-tag-moderator.png" align="absbottom" />';

// view ftp version one time by day
if($_SESSION['NutsGroupID'] == 1)
{
	$r = $plugin->getData("SELECT
									ID
							FROM
									NutsLog
							WHERE
									DATE_FORMAT(DateGMT, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') AND
									NutsGroupID = 1 AND
									NutsUserID = {$_SESSION['NutsUserID']} AND
									Application = '_home_updater'
							LIMIT
									1");

	// not found today *********************************************************
	if(count($r) == 0)
	{
		$last_version = @file_get_contents("http://www.nuts-cms.com/_last-version.php");

		$msg = '';
		if(!$last_version)
		{
			// $msg = $img_error." Error: please verify service updater `{$ftp_server}` (firewall actived in your server ?)";
			// $msg = "<script>notify('error', 'Error: please verify service updater `{$ftp_server}` (firewall actived in your server ?)');</script>";
		}
		else
		{
			if(NUTS_VERSION < $last_version)
			{
				// $msg = $img_warning." New version available, please <a href=\"javascript:system_goto('index.php?mod=_updater&do=exec','content');\">run updater</a>";
				$msg = "<script>notify('normal', 'New version available, please run plugin updater');</script>";
			}
		}

		// draw message
		if(!empty($msg))
		{
			// $menu = '<div id="home_updater">'.$msg.'</div>'.$menu;
			$menu = $menu.$msg;
		}

		nutsTrace('_home_updater', 'exec', 'get version', $msg);
	}

	// plugin are not installed ? **********************************************
	$r = $plugin->getData("SELECT
									ID
							FROM
									NutsMenu
							WHERE
									Category = '' OR
									ISNULL(Category)
							LIMIT
									1");
	if(count($r) > 0)
	{
		$menu = '<div id="home_updater">'.$img_error.' You have some plugins not installed correctly, please launch plugins module and after right manager module.</div>'.$menu;
	}

	// alert 404 & error tags *********************************************************
	$sql = "SELECT COUNT(*) FROM NutsLog WHERE Application = '_fo-error' AND Deleted = 'NO'";
	$nuts->doQuery($sql);
	$c = (int)$nuts->dbGetOne();
	if($c > 0)
	{
		$menu = '<div id="home_updater">'.$img_error.' Your system has '.$c.' error(s) please <a href="javascript:system_goto(\'index.php?mod=_control-center&do=exec\',\'content\');">run control center module</a>.</div>'.$menu;
	}

	// website maintenance *********************************************************
	if(WEBSITE_MAINTENANCE)
	{
		$menu = '<div id="home_updater">'.$img_warning.' Your website is in maintenance, please <a href="javascript:system_goto(\'index.php?mod=_control-center&do=exec\',\'content\');">run control center module</a>.</div>'.$menu;
	}

}




$plugin->render = $menu;


?>