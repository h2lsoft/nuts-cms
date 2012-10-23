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
$plugin->formAddFieldText('Position', '', true, 'number', 'width:50px', '', '', 'put -1 to add at end');


if($_POST)
{
	// form assignation
	if($_GET['Position'] == -1)
	{
		// get max position
		$_POST['Position'] = $plugin->formGetMaxPosition('Position');
		$_POST['Position'] += 1;
	}
}


?>