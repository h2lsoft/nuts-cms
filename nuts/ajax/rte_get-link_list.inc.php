<?php

$link_list = array();

// we charge distinct language actived
$languages = array();
$languages[] = nutsGetDefaultLanguage();

$tmps = nutsGetOptionsLanguages('array');
foreach($tmps as $tmp)
{
	if(!empty($tmp))
		$languages[] = strtolower($tmp);
}

// get distinct zoneID for this language
$zone_ids = array(0);
foreach($languages as $lng)
{
	// homepage
	$link_list[] = array('title' => "[HOME {$lng}]", 'value' => "/{$lng}/");

	// zone
	$sql = "SELECT DISTINCT ZoneID, (SELECT Name FROM NutsZone WHERE ID = ZoneID) AS ZoneName FROM NutsPage WHERE Deleted = 'NO' AND State = 'PUBLISHED' AND Language = '$lng' ORDER BY ID";
	$nuts->doQuery($sql);
	$zones = $nuts->dbGetData();

	foreach($zones as $zone)
	{
		$c_zoneID = $zone['ZoneID'];
		$c_zone_name = $zone['ZoneName'];

		if(!empty($c_zone_name))
		{
			$link_list[] = array('title' => "--", 'value' => "#");
			$link_list[] = array('title' => "[ZONE $c_zone_name]", 'value' => "#");
		}

		// pages listings
		$nuts_page_id = 0;
		$sql_tpl = "SELECT ID, Language, MenuName, VirtualPagename, _HasChildren FROM NutsPage WHERE NutsPageID = %s AND Language = '$lng' AND ZoneID = $c_zoneID AND Deleted = 'NO' AND State = 'PUBLISHED' ORDER BY Position";
		$nuts->dbSelect($sql_tpl, array($nuts_page_id));
		$qID1 = $nuts->dbGetQueryID();
		while($pg = $nuts->dbFetch())
		{
			$name = str_replace("'", "\'", $pg['MenuName']);
			$name = str_replace('"', '`', $pg['MenuName']);

			$url = nutsGetPageUrl($pg['ID'], $lng, $pg['VirtualPagename']);
			$link_list[] = array('title' => $name, 'value' => $url);

			// recursivity for children page
			if($pg['_HasChildren'] == 'YES')
			{
				// level 2
				$nuts->dbSelect($sql_tpl, array($pg['ID']));
				$qID2 = $nuts->dbGetQueryID();
				while($pg2 = $nuts->dbFetch())
				{
					$name = str_replace("'", "\'", $pg2['MenuName']);
					$name = str_replace('"', '`', $pg2['MenuName']);

					$url = nutsGetPageUrl($pg2['ID'], $lng, $pg2['VirtualPagename']);
					$link_list[] = array('title' => ' > '.$name, 'value' => $url);

					// page has children ?
					if($pg2['_HasChildren'] == 'YES')
					{
						// level 3
						$nuts->dbSelect($sql_tpl, array($pg2['ID']));
						$qID3 = $nuts->dbGetQueryID();
						while($pg3 = $nuts->dbFetch())
						{
							$name = str_replace("'", "\'", $pg3['MenuName']);
							$name = str_replace('"', '`', $pg3['MenuName']);

							$url = nutsGetPageUrl($pg3['ID'], $lng, $pg3['VirtualPagename']);
							$link_list[] = array('title' => ' >>> '.$name, 'value' => $url);

							// level 4
							if($pg3['_HasChildren'] == 'YES')
							{
								$nuts->dbSelect($sql_tpl, array($pg3['ID']));
								while($pg4 = $nuts->dbFetch())
								{
									$name = str_replace("'", "\'", $pg4['MenuName']);
									$name = str_replace('"', '`', $pg4['MenuName']);

									$url = nutsGetPageUrl($pg4['ID'], $lng, $pg4['VirtualPagename']);
									$link_list[] = array('title' => ' >>>> '.$name, 'value' => $url);
								}
							}

							$nuts->dbSetQueryID($qID3);
						}
					}

					$nuts->dbSetQueryID($qID2);
				}
			}

			$nuts->dbSetQueryID($qID1);
		}
	}
}

die(json_encode($link_list));

