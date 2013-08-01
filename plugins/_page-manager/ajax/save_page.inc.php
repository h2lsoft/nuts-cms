<?php

// edit page right *****************************************************************************************************
if(!nutsPageManagerUserHasRight(0, 'edit', 0, $_GET['ID']))
{
	$error_message = "You can not edit this page (right `edit` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas éditer cette page (droit `éditer` requis)";
	die($error_message);
}


// lock page right *****************************************************************************************************
if($_POST['Locked'] == 'YES')
{
	// check previous state and user
	$l = Query::factory()->select('Locked, LockedNutsUserID')->from('NutsPage')->whereID($_GET['ID'])->executeAndFetch();
	if($l['Locked'] == 'NO')
	{
		// check if user has right locked
		if(!nutsPageManagerUserHasRight(0, 'lock', 0, $_GET['ID']))
		{
			$error_message = "You can not lock this page (right `lock` required)";
			if($_SESSION['Language'] == 'fr')
				$error_message = "Vous ne pouvez pas verrouiller cette page (droit `verrouiller` requis)";
			die($error_message);
		}
	}
	elseif($l['Locked'] == 'YES')
	{
		if(!nutsUserGroupIs(1) && $l['LockedNutsUserID'] != $_SESSION['NutsUserID'])
		{
			$error_message = "Page is already locked, please contact your system administrator";
			die($error_message);
		}
	}
}


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
	// Update Locked
	if($_POST['Locked'] == 'YES')
		$_POST['LockedNutsUserID'] = $_SESSION['NutsUserID'];

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

	// thumbnail
	$thumb_path = NUTS_PAGE_THUMBNAIL_PATH.'/'.$_GET['ID'].'-'.NUTS_PAGE_THUMBNAIL_WIDTH.'x'.NUTS_PAGE_THUMBNAIL_HEIGHT.'.jpg';
	@unlink($thumb_path);

	// take first from content
	if(empty($_POST['Thumbnail']))
	{
		$img = array();
		preg_match_all('/<img [^>]+>/iS', $_POST['Content'], $imgs);
		if(count($imgs[0]) > 0)
		{
			$first_img_src = '';
			foreach($imgs[0] as $img)
			{
				$src = $nuts->extractStr($img, 'src="', '"');

				if(preg_match('#^/library/media/images/user/#i', $src))
				{
					// check width and height
					list($width, $height, $type, $attr) = @getimagesize(WEBSITE_PATH.$src);
					$width = (int)@$width;
					$height = (int)@$height;
					if($width >= NUTS_PAGE_THUMBNAIL_WIDTH && $height >= NUTS_PAGE_THUMBNAIL_HEIGHT)
					{
						$first_img_src = $src;
						break;
					}
				}
			}

			if(!empty($first_img_src))
			{
				$_POST['Thumbnail'] = $first_img_src;
			}
		}
	}



	// save
	$nuts->dbUpdate('NutsPage', $_POST, "ID = ".(int)$_GET['ID'], array('PageZoneID', 'cf*', 'Status', 'asm*', 'dID', 'CommentName', 'CommentText', 'PageAccess', 'PageAccessX', 'ContentView*', 'RightMatrix*'));


	// save content view
	$nuts->dbDelete('NutsPageContentViewFieldData', "NutsPageID = ".(int)$_GET['ID']);
	$_POST['NutsPageContentViewID'] = (int)$_POST['NutsPageContentViewID'];
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

			// smartimage resizer
			if(strtolower($field['Type']) == 'htmlarea')
			{
				$cur_val = smartImageResizer($cur_val);
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


	// rights matrix with protection
	$authorID = Query::factory()->select('NutsUserID')->from('NutsPage')->whereID($_GET['ID'])->executeAndGetOne();
	if(nutsUserGroupIs(1) || $_SESSION['NutsUserID'] == $authorID)
	{
		$nuts->doQuery("DELETE FROM NutsPageRights WHERE NutsPageID = {$_GET['ID']}");
		if(count(@$_POST['RightMatrix']) > 0)
		{
			$all_rights = array();
			foreach($_POST['RightMatrix'] as $nuts_group_id => $rights_keys)
			{
				foreach($rights_keys as $right => $value)
				{
					if($value == 1)
					{
						$f = array();
						$f['NutsPageID'] = $_GET['ID'];
						$f['NutsGroupID'] = (int)$nuts_group_id;
						$f['Action'] = $right;
						$all_rights[] = $f;

						$nuts->dbInsert('NutsPageRights', $f);
					}
				}
			}

			// apply to subpages
			if(@$_POST['RightMatrixApplySubPages'] == 1)
			{
				// subpages
				$sub_pages = nutsPageGetChildrens($_GET['ID']);
				$sub_pages = array_flatten($sub_pages);
				foreach($sub_pages as $pageID)
				{
					$pageID = (int)$pageID;
					if($pageID != 0 && $pageID != $_GET['ID'])
					{
						$nuts->doQuery("DELETE FROM NutsPageRights WHERE NutsPageID = $pageID");
						foreach($all_rights as $all_right)
						{
							$all_right['NutsPageID'] = $pageID;
							$nuts->dbInsert('NutsPageRights', $all_right);
						}
					}
				}
			}
		}
	}


	// trace action
	$plugin->trace('save', (int)$_GET['ID']);
	nutsTrigger('page-manager::save_page', true, "action on save page");
	die('ok');
}

exit(1);


?>