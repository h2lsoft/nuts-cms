<?php
/**
 * Plugin gmaps - Form layout
 * 
 * @version 1.0
 * @date 08/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// erase with current gallery config
if(@$_GET['ID'] != 0 || @$_GET['cID'] != 0)
{
	$curID = (@$_GET['ID'] != 0) ? $_GET['ID'] : $_GET['cID'];
	$_GET['NutsGMapsID'] = Query::factory()->select('NutsGMapsID')->from('NutsGMapsPOI')->whereID($curID)->executeAndGetOne();
}


// sql table
$plugin->formDBTable(array('NutsGMapsPOI'));

// fields
$plugin->formAddFieldText('Title', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldText('Address', $lang_msg[2], true, 'ucfirst');
$plugin->formAddFieldText('ZipCode', $lang_msg[3], true, 'ucfirst');
$plugin->formAddFieldTextAjaxAutoComplete('City', $lang_msg[4], true, 'ucfirst');
$plugin->formAddFieldTextAjaxAutoComplete('Country', $lang_msg[5], true, 'ucfirst');

$plugin->formAddFieldsetStart('GPS');
$btn = <<<EOF
<input type="button" value="Geocoder" id="geocoder" />
EOF;

$plugin->formAddFieldText('Latitude', "", true, 'number', 'width:120px', $btn);
$plugin->formAddFieldText('Longitude', "", true, 'number', 'width:120px');

$plugin->formAddFieldsetEnd();


// poi
$plugin->formAddFieldsetStart('POI');
$plugin->formAddFieldText('Icon', $lang_msg[6], false, "", "", "", "", $lang_msg[10]);
$plugin->formAddFieldSelect('Color', $lang_msg[7], false, $colors_opts);
$plugin->formAddFieldSelect('Size', $lang_msg[8], false, array('', 'medium', 'small', 'large'));
$plugin->formAddFieldTextarea('InfoWindow', $lang_msg[9], false, '', '', '', $lang_msg[11]);
$plugin->formAddFieldsetEnd();


$plugin->formAddFieldHidden('NutsGMapsID', 'NutsGMapsID', true);
// if($plugin->formModeIsAdding())
// {
	$plugin->formActionAddParameter("NutsGMapsID={$_GET['NutsGMapsID']}");
	$plugin->formAddEndText("<script>$('#NutsGMapsID').val({$_GET['NutsGMapsID']});</script>");
// }


if($_POST)
{
	$_POST['City'] = ucfirst($_POST['City']);
	$_POST['Country'] = ucfirst($_POST['Country']);

	// color + size required
	if(empty($_POST['Icon']))
	{
		$nuts->notEmpty('Color');
		$nuts->notEmpty('Size');
	}
	else
	{
		if(!empty($_POST['Color']))$nuts->addError('Color', $lang_msg[12]);
		if(!empty($_POST['Size']))$nuts->addError('Size', $lang_msg[13]);
	}
}


?>