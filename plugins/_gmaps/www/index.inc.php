<?php
/**
 * Plugin gmaps - Front office
 *
 * @version 1.0
 * @date 08/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Page */
/* @var $nuts Page */


$map_id = (int)$plugin->getPluginParameter(0);
Query::factory()->select('*')->from('NutsGMaps')->whereID($map_id)->execute();

if(!$nuts->dbNumRows())
{
	$plugin->setNutsContent("no map found ID: "+$map_id);
}
else
{
	$plugin->openPluginTemplate();

	include_once($plugin->plugin_path.'/config.inc.php');

	if($include_plugin_js)
	{
		$plugin->addHeaderFile('js', 'https://maps.google.com/maps/api/js?sensor=true', false);
		$plugin->addHeaderFile('js', '/plugins/_gmaps/www/gmaps.js', false);
	}


	$rec = $nuts->dbFetch();
	$nuts->parse('ID', $rec['ID']);
	$nuts->parse('Type', $rec['Type']);
	$nuts->parse('Latitude', $rec['Latitude']);
	$nuts->parse('Longitude', $rec['Longitude']);
	$nuts->parse('Zoom', $rec['Zoom']);
	$nuts->parse('Width', $rec['Width']);
	$nuts->parse('Height', $rec['Height']);

	// POIS
	$markers = Query::factory()->select('*')
							   ->from('NutsGMapsPOI')
							   ->whereEqualTo('NutsGMapsID', $rec['ID'])
							   ->executeAndGetAll();

	$pois = $markers;
	if($rec['Type'] == 'STATIC')
	{
		$tmp = array();
		foreach($markers as $marker)
		{
			$tmp[] = array(
								'lat' => $marker['Latitude'],
								'lng' => $marker['Longitude'],
								'icon' => $marker['Icon'],
								'size' => strtolower($marker['Size']),
								'color' => $marker['Color']
						   );
		}

		$markers = $tmp;

	}

	$nuts->parse('Markers', json_encode($markers));

	if($rec['Type'] != 'CLASSIC' || !count($markers))
	{
		$nuts->eraseBloc('pois');
	}
	else
	{
		for($i=0; $i < count($pois); $i++)
		{
			$nuts->parse('poi.Title', $pois[$i]['Title']);
			$nuts->parse('poi.Address', $pois[$i]['Address']);
			$nuts->parse('poi.City', $pois[$i]['City']);
			$nuts->parse('poi.Country', $pois[$i]['Country']);
			$nuts->parse('poi.i', $i);
			$nuts->loop('poi');
		}
	}

	$plugin->setNutsContent();
}

