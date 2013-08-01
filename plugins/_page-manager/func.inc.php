<?php

/**
 * Duplicate page
 */
function duplicatePages($pageID_source, $pageID_target)
{
	global $nuts;

	$sql = "SELECT * FROM NutsPage WHERE NutsPageID = $pageID_source ORDER BY Position";
	$nuts->doQuery($sql);

	$subs = array();
	while($r = $nuts->dbFetch())
		$subs[] = $r;


	// duplication page
	foreach($subs as $rec)
	{
		$oldID = $rec['ID'];

		// duplicate page exec
		$new_title = $rec['MenuName'];
		$rec['ID'] = '';
		$rec['NutsUserID'] = $_SESSION['NutsUserID'];
		$rec['DateCreation'] = 'NOW()';
		$rec['DateUpdate'] = 'NOW()';
		$rec['VirtualPagename'] = '';
		$rec['MenuName'] = $new_title;
		$rec['State'] = 'DRAFT';
		$rec['NutsPageID'] = $pageID_target;

		$lastID = $nuts->dbInsert('NutsPage', $rec, array(), true);

		// copy view data
		if($rec['NutsPageContentViewID'] != 0)
		{
			$rx = Query::factory()->select('*')
				->from('NutsPageContentViewFieldData')
				->where('NutsPageID', '=', $oldID)
				->where('NutsPageContentViewID', '=', $rec['NutsPageContentViewID'])
				->executeAndGetAll();

			foreach($rx as $tmp_rec)
			{
				$tmp_rec['NutsPageID'] = $lastID;
				$nuts->dbInsert('NutsPageContentViewFieldData', $tmp_rec, array('ID'));
			}
		}

		// copy restriction
		if($rec['AccessRestricted'] == 'YES')
		{
			$sql = "SELECT NutsGroupID FROM NutsPageAccess WHERE NutsPageID = {$oldID}";
			$nuts->doQuery($sql);
			$qID = $nuts->dbGetQueryID();
			while($r = $nuts->dbFetch())
			{
				$nuts->dbInsert('NutsPageAccess', array('NutsGroupID' => $r['NutsGroupID'], 'NutsPageID' => $lastID));
				$nuts->dbSetQueryID($qID);
			}
		}

		// copy page rights from $oldID
		$sql = "SELECT * FROM NutsPageRights WHERE NutsPageID = $oldID";
		$nuts->doQuery($sql);
		$qID = $nuts->dbGetQueryID();
		while($r = $nuts->dbFetch())
		{
			$nuts->dbInsert('NutsPageRights', array('NutsGroupID' => $r['NutsGroupID'], 'NutsPageID' => $lastID, 'Action' => $r['Action']));
			$nuts->dbSetQueryID($qID);
		}


		// replicate children
		if($rec['_HasChildren'] == 'YES')
		{
			duplicatePages($oldID, $lastID);
		}

	}
}

/**
 * Verify if a page is locked
 *
 * @param int $ID
 * @return boolean
 */
function pageIsLocked($ID)
{
	global $nuts;

	if(nutsUserGroupIs(1))return false; # exception superadmin

	Query::factory()->select('ID')
					->from('NutsPage')
					->whereID($ID)
					->whereEqualTo('Locked', 'YES')
					->whereNotEqualTo('LockedNutsUserID', $_SESSION['NutsUserID'])
					->execute();

	if($nuts->dbNumRows() != 0)
		return true;

	return false;
}















?>