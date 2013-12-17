<?php

/**
 * Get all childrens for a page
 *
 * @param type $pageID
 * @return array with page ID inside structured
 */
function nutsPageGetChildrens($pageID, $init=false)
{
	global $nuts;

	if($init)
	{
		$IDs = array();
		$init = true;
	}

	$IDs[] = $pageID;
	$nuts->doQuery("SELECT ID FROM NutsPage WHERE NutsPageID = $pageID AND Deleted = 'NO'");
	$qID = $nuts->dbGetQueryID();
	while($pg = $nuts->dbFetch())
	{
		$pgs = nutsPageGetChildrens($pg['ID']);
		if(count($pgs) > 0)
			$IDs[] = $pgs;

		$nuts->dbSetQueryID($qID);
	}

	return $IDs;



}

/**
 * Get page tree for a specific zone
 *
 * @param string $Language
 * @param int $ZoneID (0 = main menu)
 * @param int $NutsPageID
 * @param string $State
 * @return string $html_ul
 */
function nutsGetMenu($Language='', $ZoneID = 0, $NutsPageID = 0, $State = '', $directID = '')
{
	global $nuts_lang_msg, $lang_msg, $plugin;

	$ul = '';

	// select direct by ID
	$directIDMode = false;
	$directID = (int)$directID;
	if($directID != 0)
	{
		$GLOBALS['nuts']->doQuery("SELECT ID, Language, ZoneID, State, AccessRestricted  FROM NutsPage WHERE Deleted = 'NO' AND ID = $directID");
		if($GLOBALS['nuts']->dbNumRows() == 0)
		{
			$msg = <<<EOF
			@NO_TREE@
			<script>alert("{$lang_msg[66]}");</script>
EOF;
			die(trim($msg));
		}

		// reselect & force currect zone, language, status
		$directIDMode = true;
		$row2 = $GLOBALS['nuts']->dbFetch();

		$ul .= '<script>';
		$ul .= '$("#Language").val("'.$row2['Language'].'");';
		$ul .= '$("#ZoneID").val("'.$row2['ZoneID'].'");';
		// $ul .= '$("#Status").val("'.$row2['State'].'");';
		$ul .= '</script>';

		$Language = $row2['Language'];
		$ZoneID = $row2['ZoneID'];
		$State = $row2['State'];
		$AccessRestricted = $row2['AccessRestricted'];

	}



	if(empty($Language))
	{
		$Language = nutsGetDefaultLanguage();
	}

	$root = $nuts_lang_msg[41];
	if($ZoneID != 0)
	{
		// get zone name
		$GLOBALS['nuts']->doQuery("SELECT Name FROM NutsZone WHERE ID = $ZoneID");
		$root = $GLOBALS['nuts']->getOne();
	}


	if($NutsPageID == 0)
	{
		$ul .= '<ul class="simpleTree">'."\n";
		$ul .= '<li class="root" id="0"><span><b>'.$root.'</b></span>';
		$ul .= "<ul>\n";
	}

	$sql_state = '';
	if($directIDMode)
	{
		$sql_state = "ID = '".$directID."' AND ";
	}
	else
	{
		if(!empty($State))
		{

			$sql_state .= "State = '".addslashes($State)."' AND ";

			/*$parents_page_possible_ID = nutsGetPageIDSRecursive($Language, $ZoneID, $NutsPageID, $State);

			if(empty($parents_page_possible_ID))
			{
				$sql_state .= "State = '".addslashes($State)."' AND ";
			}
			else
			{
				// send a request to found all page ID with state
				$sql_state .= "(State = '".addslashes($State)."' OR ";
				$sql_state .= "ID IN ($parents_page_possible_ID) ";
				$sql_state .= " ) AND ";
			}*/
		}
		else
		{
			$sql_state = " NutsPageID = $NutsPageID AND ";
		}
	}

	$GLOBALS['nuts']->doQuery("SELECT
										ID,
										MenuName,
										_HasChildren,
                                        State,
										AccessRestricted
								FROM
										NutsPage
								WHERE
										Language = '".addslashes($Language)."' AND
										ZoneID = $ZoneID AND
										$sql_state
										Deleted = 'NO'
								ORDER BY
										Position");

	while($row = $GLOBALS['nuts']->dbFetch())
	{
		$ul2 = '';
		if($row['_HasChildren'] == 'YES' && empty($State))
		{
			$ajax_url = "index.php?mod={$plugin->name}&do={$plugin->action}&_action=reload_page&ID={$row['ID']}";
			$ajax_url .= "&language={$Language}";
			$ajax_url .= "&zoneID={$ZoneID}";
			$ajax_url .= "&state={$State}";

			$ul2 = '<ul class="ajax">';
			$ul2 .= '	<li>{url:'.$ajax_url.'}</li>';
			$ul2 .= '</ul>';
		}

		$img = '';
		if($row['State'] == 'DRAFT')
			$img = "<img src='img/icon-tag-edit.png' align='absbottom' />";
		elseif($row['State'] == 'WAITING MODERATION')
			$img = "<img src='img/icon-tag-moderator.png' align='absbottom' />";

		$img_lock = '';
		if($row['AccessRestricted'] == 'YES')
			$img_lock = "<img src='img/icon-lock.png' align='absbottom' /> ";


		$ul .= "\t".'<li id="'.$row['ID'].'"><span>'.$img_lock.$row['MenuName'].'</span>'.$img.$ul2.'</li>'."\n";
	}

	if($NutsPageID == 0)
	{
		$ul .= "</ul>\n";
		$ul .= '</ul>';
	}

	return $ul;
}

/**
 * Get count page for a specific zone
 *
 * @param string $Language
 * @param int $ZoneID
 * @param string $State
 * @return int counter
 */
function nutsGetCountPages($Language, $ZoneID, $State='')
{
	global $nuts;

	if(!empty($State))
	{
		$State = "State = '$State' AND";
	}

	$sql = "SELECT
					COUNT(*)
			FROM
					NutsPage
			WHERE
					Language = '$Language' AND
					ZoneID = $ZoneID AND
					$State
					Deleted = 'NO'";
	$nuts->doQuery($sql);

	$counter = (int) $nuts->getOne();
	return $counter;
}

/**
 * Get thumbnail url for page
 *
 * @param $pageID
 * @param $pageThumbnailOriginal
 *
 * @return mixed
 */
function nutsGetPageThumbnailUrl($pageID, $pageThumbnailOriginal)
{
	global $nuts;

	// has thumbs ?
	$no_preview = false;

	if(empty($pageThumbnailOriginal))
	{
		$thumb_file = NUTS_PAGE_THUMBNAIL_PATH.'/0-'.NUTS_PAGE_THUMBNAIL_WIDTH.'x'.NUTS_PAGE_THUMBNAIL_HEIGHT.'.jpg';
		$no_preview = true;
	}
	else
	{
		$thumb_file = NUTS_PAGE_THUMBNAIL_PATH.'/'.$pageID.'-'.NUTS_PAGE_THUMBNAIL_WIDTH.'x'.NUTS_PAGE_THUMBNAIL_HEIGHT.'.jpg';
	}

	// create thumbs ?
	if(!file_exists($thumb_file))
	{
		$im = @imagecreatetruecolor(NUTS_PAGE_THUMBNAIL_WIDTH, NUTS_PAGE_THUMBNAIL_HEIGHT);

		if($no_preview)
		{
			$bg = imagecolorallocate($im, 255, 255, 255);
			imagefilledrectangle($im, 0, 0, NUTS_PAGE_THUMBNAIL_WIDTH, NUTS_PAGE_THUMBNAIL_HEIGHT, $bg);
			imagejpeg($im, $thumb_file, 100);
		}
		else
		{
			copy(WEBSITE_PATH.$pageThumbnailOriginal, $thumb_file);
			$nuts->imgThumbnailSetOriginal($thumb_file);
			$nuts->imgThumbnail(NUTS_PAGE_THUMBNAIL_WIDTH, NUTS_PAGE_THUMBNAIL_HEIGHT, true, array(255,255,255), '', 'jpg');
		}
	}

	$thumb_file = str_replace(WEBSITE_PATH, '', $thumb_file);

	return $thumb_file;
}

/**
 * Get nut page content
 *
 * @param int $pageID
 * @param array $fields
 *
 * @return array $res
 */
function nutsGetPage($pageID, $fields)
{
	global $nuts;

	$fields_str = join(',', $fields);
	$nuts->doQuery("SELECT $fields_str FROM NutsPage WHERE ID = $pageID");
	return $nuts->dbFetch();

}

/**
 * Return formatted url for a nuts page
 *
 * @param int $ID
 * @param string $Language
 * @param string $VitualPageName
 * @param boolean $TagVersion true by defaults
 *
 * @return string
 */
function nutsGetPageUrl($ID, $Language, $virtualPagename, $TagVersion=true)
{
	// force direct url
	if(preg_match('/^http/i', $virtualPagename) || (!empty($virtualPagename) && $virtualPagename[0] == '/') || (!empty($virtualPagename) && $virtualPagename[0] == '{'))
	{
		return $virtualPagename;
	}

	if(!empty($virtualPagename))$virtualPagename = '-'.$virtualPagename;
	$url = "/$Language/{$ID}{$virtualPagename}.html";

	if($TagVersion)
	{
		$url = "{@NUTS	TYPE='PAGE'	CONTENT='URL'	ID='$ID'}";
	}

	return $url;
}

