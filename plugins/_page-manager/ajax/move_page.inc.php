<?php

// ID : source
// NutsPageID: destination
// Position : position desired (obsolete)
// Positions : array with new order
$_GET['ID'] = (int)$_GET['ID'];
$_GET['zoneID'] = (int)$_GET['zoneID'];
$_GET['position'] = (int)$_GET['position'];
$_GET['nutsPageID'] = (int)$_GET['nutsPageID'];
$_GET['position'] = $_GET['position'] + 1;

// page is locked ?
if(pageIsLocked($_GET['ID']))
{
	die($lang_msg[109]);
}

// right verification drag
if(!nutsPageManagerUserHasRight(0, 'drag', 0, $_GET['ID']))
{
	$error_message = "You can not drag this page (right `drag` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas repositionner cette page (droit `repositionner` requis)";
	die($error_message);
}

// verification if in zone
if(!$_GET['nutsPageID'])
{
	if(!nutsPageManagerUserHasRight(0, 'add_main_page', $_GET['nutsPageID']))
	{
		$error_message = (@$_SESSION['Language'] == 'fr') ? "Vous n'avez pas les droits de création de pages principales pour cette zone" : "You don't have right for main pages creation";
		die($error_message);
	}
}
else
{
	if(!nutsPageManagerUserHasRight(0, 'subpages', 0, $_GET['nutsPageID']))
	{
		$error_message = (@$_SESSION['Language'] == 'fr') ? "Vous n'avez pas les droits de création de sous-pages pour la page #{$_GET['nutsPageID']}" : "You don't have right for adding page page #{$_GET['nutsPageID']}";
		die($error_message);
	}
}


// reset hasChildren of parent
$nuts->doQuery("SELECT NutsPageID FROM NutsPage WHERE ID = {$_GET['ID']}");
$oldParentNodeID = (int)$nuts->getOne();
$nuts->dbUpdate('NutsPage', array('_HasChildren' => 'NO'), "ID = $oldParentNodeID");

// update page
//$nuts->dbUpdate('NutsPage', array('NutsPageID' => (int)$_GET['nutsPageID'], 'Position' => (int)$_GET['position']), "ID = ".(int)$_GET['ID']);
$nuts->dbUpdate('NutsPage', array('NutsPageID' => $_GET['nutsPageID']), "ID = {$_GET['ID']}");

// update node master
$nuts->dbUpdate('NutsPage', array('_HasChildren' => 'YES'), "ID = {$_GET['nutsPageID']}");

// old parent node has children
$nuts->doQuery("SELECT COUNT(*) FROM NutsPage WHERE NutsPageID = $oldParentNodeID AND Deleted = 'NO'");
$c = (int)$nuts->getOne();
if($c > 0){
	$nuts->dbUpdate('NutsPage', array('_HasChildren' => 'YES'), "ID = $oldParentNodeID");
}

// reorder oldParentID
//FB::log("reorder oldParentNodeID $oldParentNodeID");
$nuts->doQuery("SELECT ID FROM NutsPage WHERE Deleted = 'NO' AND Language = '{$_GET['language']}' AND ZoneID = {$_GET['zoneID']} AND NutsPageID = $oldParentNodeID ORDER BY Position");
$qID = $nuts->dbGetQueryID();
$pos = 1;
while($rec = $nuts->dbFetch())
{
	$nuts->doQuery("UPDATE NutsPage SET Position = $pos WHERE ID = {$rec['ID']}");
	$nuts->dbSetQueryID($qID);
	$pos++;
}

// reorder new position from array GET
$arrs = explode(';', $_GET['positions']);
$pos = 1;
foreach($arrs as $arr)
{
	$arr = (int)$arr;
	if($arr != 0)
	{
		// FB::log("UPDATE NutsPage SET Position = $pos WHERE ID = $arr");
		$nuts->doQuery("UPDATE NutsPage SET Position = $pos WHERE ID = $arr");
		$pos++;
	}
}

nutsTrigger('page-manager::move_page', true, "action on move page");
$plugin->trace('move', (int)$_GET['ID']);

die('ok');






?>