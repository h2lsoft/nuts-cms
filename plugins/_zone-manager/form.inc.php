<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table
$plugin->formDBTable(array('NutsZone'));

// fields
// $plugin->formAddField('Type', $lang_msg[1], 'select', true, array('options' => $nuts_zone_options));
$plugin->formAddFieldText('Name', $lang_msg[2], true, '', '', '', 'maxlength="50"');
$plugin->formAddFieldText('CssName', $lang_msg[3], 'notEmpty|alphaNumeric,_', '', '', '', 'maxlength="50"');
$plugin->formAddFieldTextArea('Description', $lang_msg[4], false);
$plugin->formAddFieldText('Url', '', false, '', '', '', '', $lang_msg[6]);
$plugin->formAddFieldBoolean('Navbar', $lang_msg[7], true, $lang_msg[8]);

// groups allowed in page manager
$groups = Query::factory()->select('ID as value, Name AS label')
						  ->from('NutsGroup')
						  ->whereEqualTo('BackofficeAccess', 'YES')
						  ->where('ID', 'IN', "(SELECT NutsGroupID FROM NutsMenuRight WHERE NutsGroupID = NutsGroup.ID AND NutsMenuID = (SELECT ID FROM NutsMenu WHERE Name = '_page-manager' AND Deleted = 'NO'))")
					      ->order_by('Priority ASC')
						  ->executeAndGetAll();

// rights
$plugin->formAddFieldsetStart('Rights', $lang_msg[9]);
$plugin->formAddFieldSelectMultiple('Rights', $lang_msg[10], false, $groups, '', '', '', true);
$plugin->formAddFieldsetEnd();

if($_POST)
{

	// convert rights
	if(!isset($_POST['Rights']))$_POST['Rights'] = array();

	$_POST['Rights'][] = 1; # force super admin
	$_POST['Rights'] = array_unique($_POST['Rights']);
	$_POST['Rights'] = serialize($_POST['Rights']);
}


