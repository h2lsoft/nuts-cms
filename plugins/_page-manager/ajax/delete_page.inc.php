<?php

// lock verification ***************************************************************************************************

// page is locked ?
if(pageIsLocked($_GET['ID']))die($lang_msg[109]);

// delete subpages
$sub_pages = nutsPageGetChildrens((int)$_GET['ID']);
$sub_pages = array_flatten($sub_pages);
$sub_pages[] = (int)$_GET['ID'];
$pagesIDs = join(',', $sub_pages);

// subpage is locked
foreach($sub_pages as $tmpPageID)
{
	if(pageIsLocked($tmpPageID))
		die($lang_msg[110].$tmpPageID);
}

// page rights verification ********************************************************************************************
if(!nutsPageManagerUserHasRight(0, 'delete', 0, $_GET['ID']))
{
	$error_message = "You can not delete this page (right `delete` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas supprimer cette page (droit `supprimer` requis)";
	die($error_message);
}

// subpage rights
foreach($sub_pages as $tmpPageID)
{
	if(!nutsPageManagerUserHasRight(0, 'delete', 0, $tmpPageID))
	{
		$error_message = "You can not delete the page #$tmpPageID (right `delete` required)";
		if($_SESSION['Language'] == 'fr')
			$error_message = "Vous ne pouvez pas supprimer page #$tmpPageID (droit `supprimer` requis)";
		die($error_message);
	}
}

// execution ***********************************************************************************************************


// delete page with sub pages
$nuts->dbUpdate('NutsPage', array('Deleted' => 'YES'), "ID IN($pagesIDs)");
$plugin->trace('delete_page', $pagesIDs);


// get node master ID
$nuts->doQuery("SELECT NutsPageID FROM NutsPage WHERE ID = ".(int)$_GET['ID']);
$parentNodeID = (int)$nuts->dbGetOne();

$c = -1;
if($parentNodeID > 0)
{
	// parent node has children ?
	$nuts->doQuery("SELECT
								COUNT(*)
						FROM
								NutsPage
						WHERE
								NutsPageID = $parentNodeID AND
								Deleted = 'NO'");
	$c = (int)$nuts->dbGetOne();
	if($c == 0)
		$nuts->dbUpdate('NutsPage', array('_HasChildren' => 'NO'), 'ID = '.$parentNodeID);
}

nutsTrigger('page-manager::delete_page', true, "action on delete page");
$plugin->trace('delete', (int)$_GET['ID']);

exit("$parentNodeID|$c");

