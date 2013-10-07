<?php

// get information from iframe mode ?
if(@$_GET['from'] == 'iframe' && @in_array($_GET['from_action'], array('add_page', 'add_sub_page')))
{
	// add page
	if($_GET['from_action'] == 'add_page')
	{
		$nuts->doQuery("SELECT NutsPageID, Language, ZoneID FROM NutsPage WHERE ID = {$_GET['pID']}");
		$irec = $nuts->dbFetch();

		$_GET['parentID'] = $irec['NutsPageID'];
		$_GET['language'] = $irec['Language'];
		$_GET['zoneID'] = $irec['ZoneID'];

	}
	elseif($_GET['from_action'] == 'add_sub_page')
	{
		$nuts->doQuery("SELECT NutsPageID, Language, ZoneID FROM NutsPage WHERE ID = {$_GET['pID']}");
		$irec = $nuts->dbFetch();

		$_GET['parentID'] = $_GET['pID'];
		$_GET['language'] = $irec['Language'];
		$_GET['zoneID'] = $irec['ZoneID'];
	}
}

// verify rights add_page
if(
	(@$_GET['_action'] == 'add_page') ||
	(@$_GET['from'] == 'iframe' && @$_GET['from_action'] == 'add_page')
)
{

	if(!$_GET['parentID'])
	{
		if(!nutsPageManagerUserHasRight(0, 'add_main_page', $_GET['zoneID']))
		{
			$msg = (@$_SESSION['Language'] == 'fr') ? "Vous n'avez pas les droits de création de pages principales pour cette zone" : "You don't have right for main pages creation";
			$data = array();
			$data['error'] = true;
			$data['error_message'] = $msg;

			die(json_encode($data));
		}
	}
	else
	{
		if(!nutsPageManagerUserHasRight(0, 'subpages', 0, $_GET['parentID']))
		{
			$msg = (@$_SESSION['Language'] == 'fr') ? "Vous n'avez pas les droits de création de sous-pages" : "You don't have right for subpages creation";
			$data = array();
			$data['error'] = true;
			$data['error_message'] = $msg;
			die(json_encode($data));
		}
	}

}

// get max position
$nuts->dbSelect("SELECT
							MAX(Position)
					 FROM
							NutsPage
					 WHERE
					 		Language = '%s' AND
							ZoneID = %d AND
							NutsPageID = %d AND
							Deleted = 'NO'", array($_GET['language'], $_GET['zoneID'],  $_GET['parentID'])
);
$max_position = (int)$nuts->getOne();
$max_position++;


// create a new page and return ID
$nuts->dbInsert('NutsPage', array(

	'CacheTime' => 0,
	'NutsPageID' => $_GET['parentID'],
	'NutsUserID' => $_SESSION['ID'],
	'ContentType' => 'TEXT',
	'Language' => $_GET['language'],
	'ZoneID' => (int)$_GET['zoneID'],
	'H1' => $lang_msg[1],
	'MenuName' => $lang_msg[1],
	'State' => 'DRAFT',
	'Position' => $max_position,
	'DateCreation' => nutsGetGMTDate(),
	'DateUpdate' => 'NULL'
));

$lastID = $nuts->getMaxID('NutsPage', 'ID');
$plugin->trace('add_page', (int)$lastID);

if($_GET['parentID'] > 0)
{
	// update parent with children
	$nuts->dbUpdate('NutsPage', array('_HasChildren' => 'YES'), "ID = {$_GET['parentID']}");

	// copy father properties
	$nuts->doQuery("SELECT
								HeaderImage,
								Template,
								MenuVisible,
								TopBar,
								BottomBar,
								Comments,
								AccessRestricted,
								Event,
								CustomVars,
								Tags,
								CustomBlock,
								Sitemap,
								SitemapChangefreq,
								SitemapPriority,
								MetaRobots,
								DateStartOption,
								DateStart,
								DateEndOption,
								DateEnd,
								NutsPageContentViewID
						FROM
								NutsPage
						WHERE
								ID = {$_GET['parentID']}");
	$row = $nuts->dbFetch();
	$nuts->dbUpdate('NutsPage', $row, "ID = $lastID");


	// copy view data
	if($row['NutsPageContentViewID'] != 0)
	{
		$rx = Query::factory()->select('*')
			->from('NutsPageContentViewFieldData')
			->where('NutsPageID', '=', $_GET['parentID'])
			->where('NutsPageContentViewID', '=', $row['NutsPageContentViewID'])
			->executeAndGetAll();
		foreach($rx as $tmp_rec)
		{
			$tmp_rec['NutsPageID'] = $lastID;
			$nuts->dbInsert('NutsPageContentViewFieldData', $tmp_rec, array('ID'));
		}
	}


	// copy page access
	if($row['AccessRestricted'] == 'YES')
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



}

nutsTrigger('page-manager::add_page', true, "action add new page");
$plugin->trace('add', $lastID);


// get information from iframe mode ?
if(@$_GET['from'] == 'iframe' && @in_array($_GET['from_action'], array('add_page', 'add_sub_page')))
{
	$uri = "/nuts/index.php?mod=_page-manager&do=exec&pID=$lastID&popup=1&parent_refresh=0&from=iframe&from_action=reload";
	$nuts->redirect($uri);
}

$data = array('ID' => $lastID, 'Title' => $lang_msg[1]);
$data['error'] = false;

echo $nuts->array2json($data);
exit(1);








?>