<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table PLUGIN_PATH
include(PLUGIN_PATH."/config.inc.php");

$plugin->formDBTable(array('NutsDropbox'));

$exts_allowed = join(', ', $dropbox_allowed_exts);

$nuts->doQuery("SELECT ID AS value, Name AS label FROM NutsGroup WHERE Deleted = 'NO' ORDER BY Priority, Name");
$nuts_groups = $nuts->dbGetData();
$select_attributes = '';

//  class="checkbox-list"

// fields
$plugin->formAddFieldTextAjaxAutoComplete('Category', $lang_msg[9], true);
$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldText('Description', $lang_msg[2], false, 'ucfirst');
$plugin->formAddFieldFile('X', $lang_msg[6], true, PLUGIN_PATH.'/_files', '', $dropbox_max_size, $exts_allowed, '', 'nuts/index.php?mod=_dropbox&do=list&_action=view&ID='.@$_GET['ID']);
$plugin->formAddFieldBooleanX('Locked', $lang_msg[10], true, $lang_msg[11]);

$plugin->formAddFieldsetStart('', $lang_msg[3]);
$plugin->formAddFieldSelectMultiple('NutsGroup[]', '&nbsp;', false, $nuts_groups, '', '', '', true);
$plugin->formAddFieldsetEnd();

$plugin->formAddException('NutsGroup');

// rules exception
if($_POST)
{
	$_POST['Category'] = ucfirst($_POST['Category']);
	
	if(!isset($_POST['NutsGroup']))$_POST['NutsGroup'] = array();
	if(count($_POST['NutsGroup']) == 0)
	{
		$nuts->addError('NutsGroup', $lang_msg[7]);
	}
	else
	{
		$tmp_str = '';
		foreach($_POST['NutsGroup'] as $gr)
			$tmp_str .= " [$gr] ";
		$tmp_str = trim($tmp_str);
		$_POST['GroupAllowed'] = $tmp_str;
	}

	// locked
	$_POST['LockedUserID'] = $_SESSION['NutsUserID'];


	// check user permission
	if($_GET['ID'])
	{
		$sql = "SELECT LockedUserID FROM NutsDropbox WHERE ID = {$_GET['ID']} AND Locked = 'YES'";
		$nuts->doQuery($sql);
		if($nuts->dbNumRows() == 1)
		{
			$LockedUserID = (int)$nuts->dbGetOne();
			if($LockedUserID != $_SESSION['NutsUserID'])
			{
				$nuts->doQuery("SELECT Login FROM NutsUser WHERE ID = $LockedUserID");
				$user = $nuts->dbGetOne();
				$error_msg = sprintf($lang_msg[12], $user);
				$nuts->addError('Name', $error_msg);
			}
		}
	}

	// versionning ?
	if($_GET['ID'] && $nuts->formGetTotalError() == 0 && (isset($_FILES['X']) && !$_FILES['X']['error']))
	{
		$nuts->doQuery("SELECT XFile FROM NutsDropbox WHERE ID = {$_GET['ID']}");
		$old_filename = $nuts->dbGetOne();
		$exts = explode('.', $old_filename);
		$ext = strtolower(end($exts));

		// rename process
		$i = 1;
		$found = false;
		do
		{
			$new_filename = str_replace("{$_GET['ID']}.", "{$_GET['ID']}.v{$i}.", $old_filename);
			$arr = (array)glob(PLUGIN_PATH.'/_files/'."{$_GET['ID']}.v{$i}.*");
			if(count($arr) == 0)
			{
				rename(PLUGIN_PATH.'/_files/'.$old_filename, PLUGIN_PATH.'/_files/'.$new_filename);
				$found = true;
			}
			
			$i++;

		}while(!$found);

	}
}

