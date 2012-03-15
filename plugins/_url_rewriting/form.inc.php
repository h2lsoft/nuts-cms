<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table PLUGIN_PATH
// include(PLUGIN_PATH."/config.inc.php");

$plugin->formDBTable(array('NutsUrlRewriting')); // put table here

// fields
$plugin->formAddFieldSelect('Type', '', true, array('SIMPLE', 'REGEX'));
$plugin->formAddFieldText('Pattern', '', true, '', '', '', '', 'Regex ex: `#/fr/thanks#i`');
$plugin->formAddFieldText('Replacement', '', true, '', '', '', '', 'Regex replace $ by §');

if($_POST)
{
	// form assignation
	if(!$_GET['ID'])
	{
		// get max position
		$_POST['Position'] = $plugin->formGetMaxPosition('Position');
		$_POST['Position'] += 1;
	}
}


?>