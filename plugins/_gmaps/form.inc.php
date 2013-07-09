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


// sql table
$plugin->formDBTable(array('NutsGMaps'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[2], true, 'ucfirst');
$plugin->formAddFieldText('Description', $lang_msg[5], false, 'ucfirst');

$types = $lang_msg[1];
$plugin->formAddFieldSelect('Type', "", true, $types);

$plugin->formAddFieldText('Width', $lang_msg[3], true, 'number', '', 'px');
$plugin->formAddFieldText('Height', $lang_msg[4], true, 'number', '', 'px');
$plugin->formAddFieldText('Zoom', "", true, 'number', '', '', '', '', 12);

$btn = <<<EOF
<input type="button" value="Geocoder" onclick="geocoder('Latitude', 'Longitude', '');" />
EOF;

$plugin->formAddFieldText('Latitude', "", true, 'number', 'width:120px', $btn);
$plugin->formAddFieldText('Longitude', "", true, 'number', 'width:120px');



?>