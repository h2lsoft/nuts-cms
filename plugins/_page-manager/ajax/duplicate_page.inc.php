<?php

$_GET['parentID'] = (int)@$_GET['parentID'];

// verify pages rights
if(!nutsPageManagerUserHasRight(0, 'duplicate', 0, $_GET['parentID']))
{
	$error_message = "You can not duplicate this page (right `duplicate` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas dupliquer cette page (droit `dupliquer` requis)";

	$data = array();
	$data['error'] = true;
	$data['error_message'] = $error_message;
	die($nuts->array2json($data));
}


// get max position from $_GET['parentID']
$sql = "SELECT NutsPageID FROM NutsPage WHERE ID = {$_GET['parentID']}";
$nuts->doQuery($sql);
$fatherNodeID = (int)$nuts->getOne();

// get max pos
$sql = "SELECT Position FROM NutsPage WHERE NutsPageID = $fatherNodeID AND Deleted = 'NO' ORDER BY Position DESC LIMIT 1";
$nuts->doQuery($sql);
$max_position = (int)$nuts->getOne();
$max_position += 1;

// get data father page from $_GET['parentID']
$sql = "SELECT * FROM NutsPage WHERE ID = {$_GET['parentID']}";
$nuts->doQuery($sql);
$rec = $nuts->dbFetch();

// special info
$new_title = (@empty($_GET['page_title'])) ? 'Copy '.$rec['MenuName'] : urldecode($_GET['page_title']);
$new_title = ucfirst(trim($new_title));


$rec['ID'] = '';
$rec['NutsUserID'] = $_SESSION['NutsUserID'];
$rec['DateCreation'] = 'NOW()';
$rec['DateUpdate'] = 'NOW()';
$rec['VirtualPagename'] = '';
$rec['MenuName'] = $new_title;
$rec['H1'] = $new_title;
$rec['Position'] = $max_position;
$rec['_HasChildren'] = (@$_GET['duplicate_sub'] == 1) ? 'YES' : 'NO';
$rec['State'] = 'DRAFT';


$lastID = $nuts->dbInsert('NutsPage', $rec, array(), true);

// copy view data
if($rec['NutsPageContentViewID'] != 0)
{
	$rx = Query::factory()->select('*')
		->from('NutsPageContentViewFieldData')
		->where('NutsPageID', '=', $_GET['parentID'])
		->where('NutsPageContentViewID', '=', $rec['NutsPageContentViewID'])
		->executeAndGetAll();
	foreach($rx as $tmp_rec)
	{
		$tmp_rec['NutsPageID'] = $lastID;
		$nuts->dbInsert('NutsPageContentViewFieldData', $tmp_rec, array('ID'));
	}
}


// copy page access from $_GET['parentID']
if($rec['AccessRestricted'] == 'YES')
{
	$sql = "SELECT NutsGroupID FROM NutsPageAccess WHERE NutsPageID = {$_GET['parentID']}";
	$nuts->doQuery($sql);
	$qID = $nuts->dbGetQueryID();
	while($r = $nuts->dbFetch())
	{
		$nuts->dbInsert('NutsPageAccess', array('NutsGroupID' => $r['NutsGroupID'], 'NutsPageID' => $lastID));
		$nuts->dbSetQueryID($qID);
	}
}

// copy page rights from $_GET['parentID']
$sql = "SELECT * FROM NutsPageRights WHERE NutsPageID = {$_GET['parentID']}";
$nuts->doQuery($sql);
$qID = $nuts->dbGetQueryID();
while($r = $nuts->dbFetch())
{
	$nuts->dbInsert('NutsPageRights', array('NutsGroupID' => $r['NutsGroupID'], 'NutsPageID' => $lastID, 'Action' => $r['Action']));
	$nuts->dbSetQueryID($qID);
}





$plugin->trace('duplicate', $lastID);

$data = array('ID' => $lastID, 'Title' => $new_title);
$data['error'] = false;
$data['error_message'] = '';


if(@$_GET['duplicate_sub'] == 1)
{
	$page_duplicate_source = $_GET['parentID'];
	$page_duplicate_target = $lastID;

	duplicatePages($page_duplicate_source, $page_duplicate_target);

	$plugin->trace('duplicate_sub_pages', $page_duplicate_target);
}


nutsTrigger('page-manager::duplicate_page', true, "action on duplicate page");
echo $nuts->array2json($data);
exit(1);

