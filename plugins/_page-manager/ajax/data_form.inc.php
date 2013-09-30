<?php

$nuts->doQuery("SELECT *, ZoneID AS PageZoneID, (SELECT CONCAT(FirstName,' ',LastName, ' (',Login,')') FROM NutsUser WHERE ID = LockedNutsUserID) AS LockedUsername FROM NutsPage WHERE ID = ".(int)$_GET['ID']);
if($nuts->dbNumRows() != 1)
	die('Error: data form record not found');

$row = $nuts->dbFetch();
$row = array_map('trim', $row);

// get the author
$nuts->doQuery("SELECT
							NutsUser.FirstName,
							NutsUser.LastName,
							NutsUser.Email,
							NutsUser.Language,
							NutsUser.NutsGroupID,
							NutsUser.Timezone,
							NutsGroup.Name AS GroupName
					 FROM
					 		NutsGroup,
							NutsUser
					 WHERE
					 		NutsUser.NutsGroupID = NutsGroup.ID AND
							NutsUser.ID = {$row['NutsUserID']}");
$row2 = $nuts->dbFetch();
$row = array_merge($row, $row2);


// get number of comments
$nuts->doQuery("SELECT COUNT(*) FROM NutsPageComment WHERE NutsPageID = ".(int)$_GET['ID']);
$row['CommentsNb'] = (int)$nuts->dbGetOne();

/*
// get tags
$tags = array();
$nuts->doQuery("SELECT Tag FROM NutsPageTag WHERE NutsPageID = ".(int)$_GET['ID']);
while($row2 = $nuts->dbFetch())
{
	$tags[] = $row2['Tag'];
}
$row['Tag'] = join(', ', $tags);
*/

// get all content view all field ID, Name, ID and Type
if($row['NutsPageContentViewID'] != 0)
{
	$fields = Query::factory()->select("*")
		->from('NutsPageContentViewField')
		->where('NutsPageContentViewID', '=', $row['NutsPageContentViewID'])
		->order_by("Position")
		->executeAndGetAll();

	foreach($fields as $field)
	{
		Query::factory()->select('Value')
			->from('NutsPageContentViewFieldData')
			->where('NutsPageContentViewID', '=', $row['NutsPageContentViewID'])
			->where('NutsPageContentViewFieldID', '=', $field['ID'])
			->where('NutsPageID', '=', (int)$_GET['ID'])
			->execute();

		$val = $nuts->dbGetOne();

		if(strtolower($field['Type']) == 'date' || strtolower($field['Type']) == 'datetime')
		{
			$val = $nuts->db2date($val);
		}

		$row['ContentView'.$field['Name'].'_'.$row['NutsPageContentViewID']] = $val;
	}
}



// get special vars
if(count($custom_fields) > 0)
{
	// dynamic creation of a column for NutsPage
	$nuts->doQuery("SHOW FIELDS FROM NutsPage LIKE 'X_%'");
	$Xcols = array();
	while($Xcol = $nuts->dbFetch())
	{
		$Xcols[] = $Xcol['Field'];
	}

	foreach($custom_fields as $custom_field)
	{
		$col_name = 'X_'.str_replace(' ', '', $custom_field['name']);
		if(!in_array($col_name, $Xcols))
		{
			$sql = "ALTER TABLE NutsPage ADD COLUMN `$col_name` VARCHAR(255)";
			if($custom_field['type'] == 'date')
				$sql = "ALTER TABLE NutsPage ADD COLUMN `$col_name` DATE";
			elseif($custom_field['type'] == 'datetime')
				$sql = "ALTER TABLE NutsPage ADD COLUMN `$col_name` DATETIME";
			elseif($custom_field['type'] == 'textarea')
				$sql = "ALTER TABLE NutsPage ADD COLUMN `$col_name` TEXT";

			$nuts->doQuery($sql);
		}
	}

	$arr = unserialize($row['CustomVars']);
	foreach($custom_fields as $cf)
	{
		$row['cf'.$cf['name']] = '';
		if(isset($arr[$cf['name']]))
		{
			if($_SESSION['Language'] == 'fr')
			{
				if($cf['type'] == 'date' || $cf['type'] == 'datetime')
					$arr[$cf['name']] = $nuts->db2date($arr[$cf['name']]);
			}

			$row['cf'.$cf['name']] = (string)$arr[$cf['name']];
		}
	}
}

// blocks conversion
$row['BlocksNb'] = 0;
$row['BlocksNames'] = array();
if(!empty($row['CustomBlock']))
{
	$arr = unserialize($row['CustomBlock']);
	foreach($arr as $key => $val)
	{
		$row['cf2Block'.$key] = array_unique($val);
		$row['BlocksNb'] += count($val);

		if(!in_array($key, $row['BlocksNames']))
			$row['BlocksNames'][] = $key;
	}
}

// PageAccess
$page_access = array();
if($row['AccessRestricted'] == 'YES')
{
	$sql = "SELECT NutsGroupID FROM NutsPageAccess WHERE NutsPageID = {$_GET['ID']}";
	$nuts->doQuery($sql);
	while($r2 = $nuts->dbFetch())
	{
		$page_access[] = $r2['NutsGroupID'];
	}
}

$row['PageAccess'] = $page_access;


// get navbar
$navbar_separator = ' 	<b>&raquo;</b> ';
$navbar_arr = array();

if($row['NutsPageID'] != 0)
{
	$tmp_parentID = $row['NutsPageID'];
	do
	{
		$sql = "SELECT ID, NutsPageID, MenuName, State, AccessRestricted FROM NutsPage WHERE ID = $tmp_parentID LIMIT 1";
		$nuts->doQuery($sql);
		$father_found = (int)$nuts->dbNumRows();
		if($father_found)
		{
			$tmp_rec = $nuts->dbFetch();
			// $navbar_arr[] = $navbar_separator.' <a href="#">'.$tmp_rec['MenuName'].'</a>';
			$tmp_str = $tmp_rec['MenuName'];

			// lock icon ?
			if($tmp_rec['AccessRestricted'] == 'YES')
			{
				$tmp_str = '<img src="img/icon-lock.png" align="absmiddle" /> '.trim($tmp_str);
			}

			// state icon
			if($tmp_rec['State'] == 'DRAFT')
				$tmp_str = trim($tmp_str).' <img src="img/icon-tag-edit.png" align="absmiddle" />';
			elseif($tmp_rec['State'] == 'WAITING MODERATION')
				$tmp_str = trim($tmp_str).' <img src="img/icon-tag-moderator.png" align="absmiddle" />';

			// link creator
			$tmp_str = '<a href="javascript:setTreeEdit('.$tmp_rec['ID'].');">'.trim($tmp_str).' (#'.$tmp_rec['ID'].')'.'</a>';

			$navbar_arr[] = $navbar_separator.' '.trim($tmp_str);
			$tmp_parentID = (int)$tmp_rec['NutsPageID'];
		}

	} while($father_found);
}

// add current page
$navbar = '';
if(count($navbar_arr) > 0)
{
	$navbar_arr2 = array_reverse($navbar_arr);
	$navbar = join(' ', $navbar_arr2);
}

$img_lock = "";
if($row['AccessRestricted'] == 'YES')
{
	$img_lock = '<img src="img/icon-lock.png" align="absmiddle" />';
}

$img_state = "";
// state icon
if($row['State'] == 'DRAFT')
	$img_state = '<img src="img/icon-tag-edit.png" align="absmiddle" />';
elseif($row['State'] == 'WAITING MODERATION')
	$img_state = '<img src="img/icon-tag-moderator.png" align="absmiddle" />';

$navbar .= $navbar_separator." $img_lock {$row['MenuName']} (#{$row['ID']}) $img_state";
$row['NavigationBar'] = trim(str_replace('  ', ' ', $navbar));


// hacks fr datetime
if($_SESSION['Language'] == 'fr')
{
	$row['DateStart'] = $nuts->db2date($row['DateStart']);
	$row['DateEnd'] = $nuts->db2date($row['DateEnd']);
}


nutsTrigger('page-manager::data_form', true, "action on get data for a page");
echo $nuts->array2json($row);
exit();





?>