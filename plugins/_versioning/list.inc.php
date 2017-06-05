<?php

include __DIR__.'/config.inc.php';

// assign table to db
$plugin->listSetDbTable('NutsVersion', "(SELECT Login FROM NutsUser WHERE ID = NutsUserID) AS Login", "NutsUserID = {$_SESSION['NutsUserID']}", "ORDER BY ID DESC");

// search
$plugin->listSearchAddFieldSelectSql('Application');
$plugin->listSearchAddFieldText('RecordID', 'Record ID');


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('Application', '', '; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('RecordID', 'Record ID', 'center; width:30px', false);
$plugin->listAddCol('File', '', '', false);
$plugin->listAddCol('Date', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('Login', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('Replace', ' ', 'center; width:30px; white-space:nowrap;', false);



// render list
$plugin->listAllowExcelExport = false;
$plugin->listCopyButton = false;
$plugin->listSearchOpenOnload = true;
$plugin->listWaitingForUserSearching = true;
$plugin->listWaitingForUserSearchingMessage = "Please search for application and record ID";
$plugin->listRender(0, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

 
	if(!$row['RecordID'])$row['RecordID'] = '-';
	
	$data = unserialize($row['DataSerialized']);
	$row['File'] = (isset($data['_file'])) ? str_erase(WEBSITE_PATH, $data['_file']) : '';
	
	$row['Replace'] = <<<EOF
	<a href="javascript:if((c=confirm('Wold you like to replace this record ?')))popupModalV2('?mod=_versioning&do=replace&ID={$row['ID']}&popup=1', 'content');"><i class="icon-redo"></i> Replace</a>
EOF;
	


	return $row;
}


