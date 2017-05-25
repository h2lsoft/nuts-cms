<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// action: replace version
if(isset($_GET['_action']) && $_GET['_action'] == 'set')
{
	$_GET['ID'] = (int)@$_GET['ID'];
	$_GET['NutsPageID'] = (int)@$_GET['NutsPageID'];

	$nuts->doQuery("SELECT * FROM NutsPageVersion WHERE ID = {$_GET['ID']} AND Deleted = 'NO'");
	$rec = $nuts->dbFetch();

    $nuts->dbUpdate('NutsPage', array(
										'NutsUserID' => $rec['NutsUserID'],
										'DateUpdate' => 'NOW()',
										'H1' => $rec['H1'],
										'H2' => $rec['H2'],
										'Note' => $rec['Note'],
										'ContentResume' => $rec['ContentResume'],
										'Content' => $rec['Content'],
										'NutsPageContentViewID' => $rec['NutsPageContentViewID']

                              ),
					"ID = {$_GET['NutsPageID']}");
	$nuts->dbInsert('NutsPageVersion', $rec, array('ID'));
	$nuts->dbUpdate('NutsPageVersion', array('Deleted' => 'YES'), "ID={$_GET['ID']}");
}

// action: replace version
$maxPage = 0;
if($plugin->listUserIsSearching())
{
	$_GET['NutsPageID'] = (int)$_GET['NutsPageID'];
	$sql = "SELECT ID FROM NutsPageVersion WHERE Deleted ='NO' AND NutsPageID = {$_GET['NutsPageID']} ORDER BY ID DESC LIMIT 1";
	$nuts->doQuery($sql);
	$maxPage = $nuts->dbGetOne();
}






// assign table to db
$plugin->listSetDbTable('NutsPageVersion', "(SELECT CONCAT(LastName,' ', FirstName) FROM NutsUser WHERE ID = NutsUserID) AS Author");

// search engine
$plugin->listSearchAddFieldText('NutsPageID', 'Page ID');


// create fields
$plugin->listAddCol('View', ' ', 'center; width:30px', false);
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Date', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('Author', $lang_msg[1], 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('H1', '', '', false);
$plugin->listAddCol('Note', '', 'font-size:10px;', false);
$plugin->listAddCol('Option', ' ', 'center; width:30px; white-space:nowrap;', false);


$plugin->listSetFirstOrderBySort('desc');

// open search
$plugin->listWaitingForUserSearching = true;
$plugin->listWaitingForUserSearchingMessage = $lang_msg[2];
$plugin->listSearchOpenOnload = true;
if($plugin->listUserIsSearching())$plugin->listSearchOpenOnload = false;


// render list
$plugin->listRender(20, 'hookData');



function hookData($row)
{
	global $maxPage, $lang_msg;

	$row['View'] = <<<EOF
	<a href="javascript:popupModal('?mod=_page-versionning&do=viewer&ID={$row['ID']}&popup=1', 'Version Preview', 1024, 768);"><img src="img/list_view.png" /></a>
EOF;

	// no option for max page
	$row['Option'] = <<<EOF
	<a href="javascript:system_goto('?mod=_page-versionning&do=list&_action=set&ID={$row['ID']}&NutsPageID={$_GET['NutsPageID']}&NutsPageID_operator=_equal_&user_se=1&popup=1', 'content');"><i class="icon-redo"></i>{$lang_msg[4]}</a>
EOF;

	if($maxPage == $row['ID'])$row['Option'] = '';

	return $row;
}

