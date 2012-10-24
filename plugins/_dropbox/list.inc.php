<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');


// _action:view *************************************************************************
if(@$_GET['_action'] == 'view' || @$_GET['_action'] == 'download')
{
	$_GET['ID'] = @(int)$_GET['ID'];

	$sql = "SELECT ID, Name, XFile FROM NutsDropbox WHERE Deleted = 'NO' AND ID = {$_GET['ID']} AND (GroupAllowed LIKE '%[{$_SESSION['NutsGroupID']}]%' OR LockedUserID = {$_SESSION['NutsUserID']})";
	$nuts->doQuery($sql);
	if($nuts->dbNumRows() == 0)
	{
		die("File not exists or you have no permission for this file");
	}
	else
	{
		// prevent from hacking
		$rec = $nuts->dbFetch();
		$tmp_file = $rec['XFile'];
		$basefile = basename($tmp_file);
		$file = PLUGIN_PATH.'/_files/'.$basefile;
		if(!file_exists($file))
			die("File not exists in path `$file`");

		$size = filesize($file);
		$filename = basename($file);
		
		$exts = explode('.', $filename);
		$ext = strtolower(end($exts));
		$virtual_filename = preg_replace("/[\W]/ui", ' ', $rec['Name']);
		$virtual_filename = trim($virtual_filename).'.'.$ext;


		if($_GET['_action'] == 'view' && (in_array($ext, $dropbox_images_view_allowed) || in_array($ext, $dropbox_files_view_allowed)))
		{
			if(in_array($ext, $dropbox_images_view_allowed))header("Content-Type: image/$ext; name=\"$virtual_filename\"");

            if($ext == 'pdf')
                header("Content-Type: application/pdf; name=\"$virtual_filename\"");
            else
                header("Content-Disposition: inline; filename=\"$virtual_filename\"");

			header("Content-Transfer-Encoding: binary");
            header("Content-Length: $size");
			header("Expires: 0");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			readfile($file);
		}
		else
		{
			header("Content-Type: application/force-download; name=\"$virtual_filename\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: $size");
			header("Content-Disposition: attachment; filename=\"$virtual_filename\"");
			header("Expires: 0");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			readfile($file);
		}
	}
	exit();
}





// assign table to db
$plugin->listSetDbTable('NutsDropbox', "", "(GroupAllowed LIKE '%[{$_SESSION['NutsGroupID']}]%' OR LockedUserID = {$_SESSION['NutsUserID']})");


// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Category', $lang_msg[9], '', '', "GroupAllowed LIKE '%[{$_SESSION['NutsGroupID']}]%'");
$plugin->listSearchAddFieldTextAjaxAutoComplete('Name', $lang_msg[1], 'countains', '', '', '', '', "GroupAllowed LIKE '%[{$_SESSION['NutsGroupID']}]%'", '');


// create fields
$plugin->listAddCol('ID', '', 'center; width:10px; white-space:nowrap;', true); // with order by
$plugin->listAddCol('Category', $lang_msg[9], '; width:30px; white-space:nowrap;', true); // with order by
$plugin->listAddCol('State', '&nbsp;', 'center; width:10px;', false);
$plugin->listAddCol('Name', $lang_msg[1], '; width:30px; white-space:nowrap;', true); 
$plugin->listAddCol('Download', ' ', 'center; width:30px;', false);
$plugin->listAddCol('Description', $lang_msg[2], '', false);


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $lang_msg, $nuts;

	$file_name = basename($row['XFile']);

	$row['View'] = '';
	//if(preg_match('/.(jpg|jpeg|png|gif|pdf|swf|flv)$/i', $row['XFile']))
	//{
		$view = '<a title="'.$lang_msg[4].'" class="tt" href="/nuts/index.php?mod=_dropbox&do=list&_action=view&ID=[ID]" target="_blank">'.$row['Name'].'</a>';
		$row['View'] = str_replace('[ID]', $row['ID'], $view);
	//}
	
	// download
	$dl = '<a title="'.$lang_msg[5].'" class="tt"  href="/nuts/index.php?mod=_dropbox&do=list&_action=download&ID=[ID]" target="_blank"><img src="img/icon-save.png" /></a>';
	$row['Download'] = str_replace('[ID]', $row['ID'], $dl);


	// add icon extension on name
	$row['Name'] =  getImageExtension($row['XFile']).' '.$row['View'];


	// locked ?
	$row['State'] = '';
	if($row['Locked'] == 'YES')
	{
		$qID = $nuts->dbGetQueryID();
		$nuts->doQuery("SELECT Login FROM NutsUser WHERE ID = {$row['LockedUserID']}");
		$tt = sprintf($lang_msg[12], $nuts->dbGetOne());
		$state = '<a title="'.$tt.'" class="tt"><img src="img/icon-lock.png" /></a>';
		
		$row['State'] = $state;

		$nuts->dbSetQueryID($qID);
	}




	return $row;
}



?>