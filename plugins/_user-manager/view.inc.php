<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

$plugin->viewDbTable(array('NutsUser'));
$plugin->viewAddSQLField("

							(SELECT Name FROM NutsGroup WHERE ID = NutsGroupID) AS NutsGroup
							, CONCAT(
									'<img src=\"'
									'http://www.gravatar.com/avatar/',
									MD5(Email),
									'?s=60&d=".urlencode(WEBSITE_URL.'/nuts/img/gravatar.jpg')."',
									'\" />'
							   ) AS Avatar");

$plugin->viewAddVar('Avatar', '&nbsp;');

$plugin->viewAddVar('NutsGroup', $lang_msg[1]);
$plugin->viewAddVar('Gender', $lang_msg[24]);
$plugin->viewAddVar('LastName', $lang_msg[2]);
$plugin->viewAddVar('FirstName', $lang_msg[3]);
$plugin->viewAddVar('Email', $lang_msg[4]);
$plugin->viewAddVar('Login', $lang_msg[5]);
//$plugin->viewAddVar('Password', $lang_msg[6]);
$plugin->viewAddVar('Language', $lang_msg[7]);
$plugin->viewAddVar('Timezone', $lang_msg[9]);
$plugin->viewAddVar('Active', $lang_msg[8]);

// info
$plugin->viewAddVar('Company', $lang_msg[15]);
$plugin->viewAddVar('NTVA', $lang_msg[25]);
$plugin->viewAddVar('Address', $lang_msg[16]);
$plugin->viewAddVar('Address2', $lang_msg[17]);
$plugin->viewAddVar('Address3', $lang_msg[26]);

$plugin->viewAddVar('ZipCode', $lang_msg[18]);
$plugin->viewAddVar('City', $lang_msg[19]);
$plugin->viewAddVar('Country', $lang_msg[20]);
$plugin->viewAddVar('Phone', $lang_msg[21]);
$plugin->viewAddVar('Gsm', $lang_msg[22]);
$plugin->viewAddVar('Fax', $lang_msg[23]);
$plugin->viewAddVar('Job', '');
$plugin->viewAddVar('NoteField', ' ');
// end of info

$plugin->viewRender("hookData");


function hookData($row){

	$row['Gender'] = ucfirst(strtolower($row['Gender']));
	$row['Note'] = nl2br($row['Note']);
	$row['NoteField'] = '<fieldset style="display:block;"><legend>Note</legend>'.$row['Note'].'</fieldset>';

	return $row;
}




?>