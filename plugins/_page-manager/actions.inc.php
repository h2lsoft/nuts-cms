<?php


/* var @nuts NutsCore */
$_GET['ID'] = (int)@$_GET['ID'];
$_GET['zoneID'] = (int)@$_GET['zoneID'];



// get_meta_keywords ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'get_meta_keywords')
{
	// get all meta keywords
	$q = urldecode($_GET['q']);
	$q = str_replace('%', '', $q);
	$q = str_replace('_', '\\_', $q);
	$q = str_replace("'", "\'", $q);

	$sql = "SELECT MetaKeywords FROM NutsPage WHERE MetaKeywords LIKE '%$q%' LIMIT 20";
	$nuts->doQuery($sql);

	$tags = "";
	$tags_done = array();
	while($row = $nuts->dbFetch())
	{
		$tmp_arr = explode(',', $row['MetaKeywords']);
		$tmp_arr = array_map('trim', $tmp_arr);

		for($i=0; $i < count($tmp_arr); $i++)
		{
			if(!in_array($tmp_arr[$i], $tags_done) && stristr($tmp_arr[$i], urldecode($_GET['q'])))
			{
				$tags = "{$tmp_arr[$i]}\n";
				$tags_done[] = $tmp_arr[$i];
			}
		}
	}
	
	$tags = trim($tags);
	die($tags);
}




// comment_delete ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'comment_delete')
{
	$_GET['CommentID'] = (int)@$_GET['CommentID'];
	$nuts->dbUpdate('NutsPageComment', array('Deleted' => 'YES'), "ID={$_GET['CommentID']} AND NutsPageID = {$_GET['ID']}");

	$plugin->trace('comment_delete', (int)$_GET['ID']);

    nutsTrigger('page-manager::comment_delete', true, "action delete on comment");

	// launch exec for all suscribers
	exit(1);
}
// comment_visible ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'comment_visible')
{
	// get current visible comment status
	$_GET['CommentID'] = (int)@$_GET['CommentID'];
	$nuts->doQuery("SELECT Visible FROM NutsPageComment WHERE ID = {$_GET['CommentID']} AND NutsPageID = {$_GET['ID']}");
	$visible = ($nuts->dbGetOne() == 'YES') ? 'NO' : 'YES';

	$_GET['CommentID'] = (int)@$_GET['CommentID'];
	$nuts->dbUpdate('NutsPageComment', array('Visible' => $visible), "ID={$_GET['CommentID']} AND NutsPageID = {$_GET['ID']}");


	$plugin->trace('comment_visible', (int)$_GET['CommentID'], $visible);

    nutsTrigger('page-manager::comment_visible', true, "action visible on comment");

	// launch exec for all suscribers
	echo $visible;
	exit(1);
}

// comment_new ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'comment_new')
{
	$IP = $nuts->getIp();
	$IP_long = ip2long($IP);
	$CID = $nuts->dbInsert('NutsPageComment', array(
												'Date' => 'NOW()',
												'NutsPageID' => $_GET['ID'],
												'NutsUserID' => $_SESSION['ID'],
												'Name' => $_POST['CommentName'],
												'Message' => nl2br(ucfirst($_POST['CommentText'])),
												'Email' => $_SESSION['Email'],
												'Website' => WEBSITE_URL,
												'Visible' => 'NO',
												'Suscribe' => 'YES',
												'IP' => $IP_long
											), array(), true);

	$plugin->trace('comment_new', (int)$CID, print_r($_POST, true));

    nutsTrigger('page-manager::comment_new', true, "action new on comment");

	// launch exec for all suscribers
	$uri = WEBSITE_URL.'/plugins/_comments/www/exec.php?action=';
	$action= base64_encode("do=show&email_admin={$_SESSION['Email']}&show&ID=$CID&lang={$_POST['Language']}&NutsPageID={$_GET['ID']}&Email={$_SESSION['Email']}");
	
	$fp = file_get_contents($uri.strrev($action));	
	exit(1);
}
// comment_list ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'comment_list')
{
	include(NUTS_PLUGINS_PATH."/_comments/config.inc.php");

	$sql = "SELECT * FROM NutsPageComment WHERE NutsPageID = {$_GET['ID']} AND Deleted = 'NO' ORDER BY Date";
	$nuts->doQuery($sql);
	$datas = array();
	while($row = $nuts->dbFetch())
	{
		$email = $row["Email"];
		$default = (empty($comments_avatar_default_image_url)) ? WEBSITE_URL.'/plugins/_comments/www/anonymous.gif': $comments_avatar_default_image_url;
		$size = $comments_avatar_size;
		$grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?d=".urlencode($default)."&s=".$size;
		$row['Avatar'] = $grav_url;
		
		$row['IP'] = long2ip($row['IP']);
		$row['VisibilityImage'] = ($row['Visible'] == 'YES') ? 'YES.gif' : 'icon-error.gif';
				
		$datas[] = $row;
	}

	$datas_json = $nuts->array2json($datas);
	
	echo $datas_json;

	exit(1);
}
// reload ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'reload')
{

    nutsTrigger('page-manager::reload', true, "action reload all pages");

	$data = nutsGetMenu($_GET['language'], (int)$_GET['zoneID'], 0, $_GET['state'], $_GET['dID']);
	echo $data;
	exit(1);	
}
// reload page ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'reload_page')
{
    nutsTrigger('page-manager::reload_page', true, "action reload on page");

	$data = nutsGetMenu($_GET['language'], (int)$_GET['zoneID'], (int)$_GET['ID'], $_GET['state'], @$_GET['dID']);
	echo $data;
	exit(1);
}
// counter page ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'counter_page')
{
	
	$data = array();	
	$data[] = nutsGetCountPages($_GET['language'], $_GET['zoneID']);
	$data[] = nutsGetCountPages($_GET['language'], $_GET['zoneID'], 'PUBLISHED');
	$data[] = nutsGetCountPages($_GET['language'], $_GET['zoneID'], 'DRAFT');
	$data[] = nutsGetCountPages($_GET['language'], $_GET['zoneID'], 'WAITING MODERATION');
			
	echo $nuts->array2json($data);
	exit(1);
}

// add page or front-office add page or add sub page ***************************************************
if((isset($_GET['_action']) && $_GET['_action'] == 'add_page') || (isset($_GET['from']) && $_GET['from'] == 'iframe' && isset($_GET['from_action']) && in_array($_GET['from_action'], array('add_page', 'add_sub_page'))))
{

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
								DateEnd
						FROM
								NutsPage
						WHERE
								ID = {$_GET['parentID']}");
		$row = $nuts->dbFetch();
		$nuts->dbUpdate('NutsPage', $row, "ID = $lastID");

		// copy page access
		if($row['AccessRestricted'] == 'YES')
		{
			$sql = "SELECT NutsGroupID FROM NutsPageAccess WHERE NutsPageID = {$_GET['parentID']}";
			$nuts->doQuery($sql);
			while($r = $nuts->dbFetch())
			{
				$nuts->dbInsert('NutsPageAccess', array('NutsGroupID' => $r['NutsGroupID'], 'NutsPageID' => $lastID));
			}
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
	echo $nuts->array2json($data);
	exit(1);
}


// duplicate page ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'duplicate_page')
{
	$_GET['parentID'] = (int)@$_GET['parentID'];


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
	$new_title = 'Copy '.$rec['MenuName'];
	
	$rec['ID'] = '';
	$rec['NutsUserID'] = $_SESSION['NutsUserID'];
	$rec['DateCreation'] = 'NOW()';
	$rec['DateUpdate'] = 'NOW()';
	$rec['VirtualPagename'] = '';
	$rec['MenuName'] = $new_title;
	$rec['Position'] = $max_position;
	$rec['_HasChildren'] = (@$_GET['duplicate_sub'] == 1) ? 'YES' : 'NO';
	$rec['State'] = 'DRAFT';	

	$lastID = $nuts->dbInsert('NutsPage', $rec, array(), true);

	// copy page access from $_GET['parentID']
	if($rec['AccessRestricted'] == 'YES')
	{
		$sql = "SELECT NutsGroupID FROM NutsPageAccess WHERE NutsPageID = {$_GET['parentID']}";
		$nuts->doQuery($sql);
		while($r = $nuts->dbFetch())
		{
			$nuts->dbInsert('NutsPageAccess', array('NutsGroupID' => $r['NutsGroupID'], 'NutsPageID' => $lastID));
		}
	}

	$plugin->trace('duplicate', $lastID);
	
	$data = array('ID' => $lastID, 'Title' => $new_title);


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
}

// rename page ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'rename_page')
{

    nutsTrigger('page-manager::rename_page', true, "action on rename page");

	$nuts->dbUpdate('NutsPage', array('MenuName' => $_GET['XName']), "ID = ".(int)$_GET['ID']);
	$plugin->trace('rename_page', (int)$_GET['ID']);
	exit(1);
}

// delete page ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'delete_page')
{
    // delete subpages
    $sub_pages = nutsPageGetChildrens((int)$_GET['ID']);
    $sub_pages = array_flatten($sub_pages);
    $sub_pages[] = (int)$_GET['ID'];
    $pagesIDs = join(',', $sub_pages);

	// delete page with sub pages
	$nuts->dbUpdate('NutsPage', array('Deleted' => 'YES', 'Position' => 0), "ID IN($pagesIDs)");
	$plugin->trace('delete_page', $pagesIDs);
	
	// get node master ID
	$nuts->doQuery("SELECT NutsPageID FROM NutsPage WHERE ID = ".(int)$_GET['ID']);
	$parentNodeID = (int)$nuts->getOne();
	
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
		$c = (int)$nuts->getOne();
		if($c == 0)	
		{
			$nuts->dbUpdate('NutsPage', array('_HasChildren' => 'NO'), 'ID = '.$parentNodeID);
		}
	}	

    nutsTrigger('page-manager::delete_page', true, "action on delete page");
	$plugin->trace('delete', (int)$_GET['ID']);
						  
	exit("$parentNodeID|$c");
}
// move page ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'move_page')
{
	// ID : source
	// NutsPageID: destination
	// Position : position desired (obsolete)
	// Positions : array with new order
	$_GET['ID'] = (int)$_GET['ID'];
	$_GET['zoneID'] = (int)$_GET['zoneID'];
	$_GET['position'] = (int)$_GET['position'];
	$_GET['nutsPageID'] = (int)$_GET['nutsPageID'];	
	$_GET['position'] = $_GET['position'] + 1;
	
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

	exit(1);
}
// data form ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'data_form')
{
	$nuts->doQuery("SELECT *, ZoneID AS PageZoneID FROM NutsPage WHERE ID = ".(int)$_GET['ID']);
	if($nuts->dbNumRows() != 1)
		die('Error: data form record no found');

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
}

// save page ***********************************************************************************
if(isset($_GET['_action']) && $_GET['_action'] == 'save_page')
{

	// forbidden special instruction
	foreach($_POST as $key => $val)
	{
		$val = str_replace('{#', '{ #', $val);
		$val = str_replace('{CONST::', '{ CONST::', $val);
		$_POST[$key] = $val;
	}
	
	// smartImageResizer
	$_POST['ContentResume'] = @smartImageResizer($_POST['ContentResume']);
	$_POST['Content'] = @smartImageResizer($_POST['Content']);	
	

	// control data
	$err = '';
	if(empty($_POST['MenuName']))
		$err .= "{$lang_msg[53]}\n";
	
	// data
	if(!empty($err))
	{
		echo $err;
	}
	else
	{
		// update own vars
		$_POST['CustomVars'] = '';
		if(count($custom_fields) > 0)
		{
			$cf_serial = array();
			foreach($custom_fields as $cf)
			{
				// hacks for date french
				if($_SESSION['Language'] && ($cf['type'] == "date" || $cf['type'] == "datetime"))
				{
					$_POST['cf'.$cf['name']] = $nuts->date2db($_POST['cf'.$cf['name']]);
				}
				
				$cf_serial[$cf['name']] = $_POST['cf'.$cf['name']];						
				$_POST['X_'.$cf['name']] = $_POST['cf'.$cf['name']];				
			}
			
			$cf_serial_str = serialize($cf_serial);
			$_POST['CustomVars'] = $cf_serial_str;
		}

		// update blocks
		$_POST['CustomBlock'] = array();
		foreach($_POST as $key => $val)
		{			
			if(preg_match('/^cf2Block/', $key))
			{
				$block_name = str_replace('cf2Block', '', $key);
				$_POST['CustomBlock'][$block_name] = $_POST[$key];
			}
		}
		$_POST['CustomBlock'] = serialize($_POST['CustomBlock']);
		

		// tags
		if(!empty($_POST['Tags']))
		{
			$_POST['Tags'] = str_replace("\r", "\n", $_POST['Tags']);
			$_POST['Tags'] = str_replace("\n\n", "\n", $_POST['Tags']);
			$_POST['Tags'] .= "\n";
		}
		
		// cache time prevent
		$_POST['CacheTime'] = (int)$_POST['CacheTime'];

		// date update
		$_POST['DateUpdate'] = nutsGetGMTDate();

		// no sitemap for page access
		if($_POST['AccessRestricted'] == 'YES')
		{
			$_POST['Sitemap'] = 'NO';
			$_POST['CacheTime'] = 0; # no cache for restricted access
		}
		
		// change ZoneID with childrens
		$_POST['ZoneID'] = $_POST['PageZoneID'];
		if($_GET['zoneID'] != $_POST['ZoneID'])
		{
			$pageID = nutsPageGetChildrens($_GET['ID'], true);
			$pageIDs = array_flatten($pageID);
			if(is_array($pageIDs) && count($pageIDs) > 0)			
			{
				$tmp = join(',', $pageIDs);
				$nuts->dbUpdate('NutsPage', array('ZoneID' => $_POST['ZoneID']), "ID IN($tmp)");
			}		
		}	
				
		// hacks date && datetime
		if($_SESSION['Language'] == 'fr')
		{
			if(!empty($_POST['DateStart']))
				$_POST['DateStart'] = $nuts->date2db($_POST['DateStart']).':00';
			
			if(!empty($_POST['DateEnd']))
				$_POST['DateEnd'] = $nuts->date2db($_POST['DateEnd']).':00';
		}
								
		// save
		$nuts->dbUpdate('NutsPage', $_POST, "ID = ".(int)$_GET['ID'], array('PageZoneID', 'cf*', 'Status', 'asm*', 'dID', 'CommentName', 'CommentText', 'PageAccess', 'PageAccessX', 'ContentView*'));


        // save content view
        $_POST['NutsPageContentViewID'] = (int)$_POST['NutsPageContentViewID'];
        $nuts->dbDelete('NutsPageContentViewFieldData', "NutsPageID = ".(int)$_GET['ID']);
        if($_POST['NutsPageContentViewID'] != 0)
        {
            // get all field ID, Name, ID and Type
            $fields = Query::factory()->select("*")->from('NutsPageContentViewField')->where('NutsPageContentViewID', '=', $_POST['NutsPageContentViewID'])->order_by("Position")->executeAndGetAll();
            foreach($fields as $field)
            {
                $cur_val = @$_POST['ContentView'.$field['Name'].'_'.$_POST['NutsPageContentViewID']];

                // datetime format
                if($_SESSION['Language'] == 'fr')
                {
                    if(strtolower($field['Type']) == 'date')
                    {
                       if(!empty($cur_val))
                            $cur_val = $nuts->date2db($cur_val);
                    }

                    if(strtolower($field['Type']) == 'datetime')
                    {
                       if(!empty($cur_val))
                            $cur_val = $nuts->date2db($cur_val).':00';
                    }
                }

                $f = array();
                $f['NutsPageContentViewID'] = $_POST['NutsPageContentViewID'];
                $f['NutsPageID'] = $_GET['ID'];
                $f['NutsPageContentViewFieldID'] = $field['ID'];
                $f['Value'] = $cur_val;

                $nuts->dbInsert('NutsPageContentViewFieldData', $f);
            }
        }


		// tags
		/*$nuts->doQuery("DELETE FROM NutsPageTag WHERE NutsPageID = {$_GET['ID']}");
		if(!empty($_POST['Tags']))
		{
			$tags = explode(',', $_POST['Tags']);
			$tags = array_map('trim', $tags);
			
			foreach($tags as $t)
				$nuts->dbInsert("NutsPageTag", array('NutsPageID' => $_GET['ID'], 'Tag' => $t));
		}*/

		// add versionning
		$nuts->dbInsert('NutsPageVersion',
											array(
													'NutsUserID' => $_SESSION['NutsUserID'],
													'NutsPageID' => $_GET['ID'],
													'Date' => 'NOW()',
													'H1' => $_POST['H1'],
													'ContentResume' => $_POST['ContentResume'],
													'Content' => $_POST['Content'],
													'Note' => $_POST['Note']
											));

		// add page access
		$nuts->doQuery("DELETE FROM NutsPageAccess WHERE NutsPageID = {$_GET['ID']}");
		if(isset($_POST['PageAccessX']))
		{
			$tmp_arr = explode(';', $_POST['PageAccessX']);
			foreach($tmp_arr as $tmp_a)
			{
				$tmp_a = (int)$tmp_a;
				if($tmp_a != 0)
				{
					$nuts->dbInsert('NutsPageAccess', array('NutsPageID' => $_GET['ID'], 'NutsGroupID' => $tmp_a));
				}
			}	
		}
		
		
		// trace action
		$plugin->trace('save', (int)$_GET['ID']);


        nutsTrigger('page-manager::save_page', true, "action on save page");


		echo "ok";
	}
		
	exit(1);
}


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

        if($rec['AccessRestricted'] == 'YES')
        {
            $sql = "SELECT NutsGroupID FROM NutsPageAccess WHERE NutsPageID = {$oldID}";
            $nuts->doQuery($sql);
            while($r = $nuts->dbFetch())
            {
                $nuts->dbInsert('NutsPageAccess', array('NutsGroupID' => $r['NutsGroupID'], 'NutsPageID' => $lastID));
            }
        }

        // replicate children
        if($rec['_HasChildren'] == 'YES')
        {
            duplicatePages($oldID, $lastID);
        }

    }



}

?>