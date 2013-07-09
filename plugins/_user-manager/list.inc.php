<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');
include(PLUGIN_PATH.'/common.inc.php');


// assign table to db
$plugin->listSetDbTable('NutsUser',  "(SELECT Name FROM NutsGroup WHERE ID = NutsUser.NutsGroupID) AS GroupName", "NutsGroupID IN(".join(',',$allowed_groups).")");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('NutsGroupID', $lang_msg[1], '', '', "ID IN(".join(',',$allowed_groups).")");
$plugin->listSearchAddFieldTextAjaxAutoComplete('Login');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Email');
$plugin->listSearchAddFieldTextAjaxAutoComplete('LastName', $lang_msg[2]);
$plugin->listSearchAddFieldBoolean('Active');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Company', $lang_msg[15]);


// create fields
$plugin->listAddCol('Avatar', ' ', 'center; width:30px', false); # avatar
$plugin->listAddCol('ID', '', 'center; width:30px', true); // with order by
$plugin->listAddCol('GroupName', $lang_msg[1], '', true); // with order by
$plugin->listAddCol('LastName', $lang_msg[2], '', true);
// $plugin->listAddCol('FirstName', $lang_msg[3], '', true);
$plugin->listAddCol('Login', $lang_msg[5], 'center', true);
$plugin->listAddColImg('Password', $lang_msg[6], '', false, 'logon_password.png');
$plugin->listAddCol('Timezone', $lang_msg[9], 'center; width:10px', false);
$plugin->listAddColImg('Email', $lang_msg[4], '', false, 'email.png', 'mailto:{Email}');
$plugin->listAddColImg('Language', $lang_msg[7], '', true, NUTS_IMAGES_URL.'/flag/{Language}.gif');
$plugin->listAddColImg('Active', $lang_msg[8], '', true);
$plugin->listAddCol('LastConnection', $lang_msg[28], 'center; width:10px; white-space:nowrap;', true);

include(PLUGIN_PATH."/custom.inc.php");

// render list
$plugin->listExportExcelModeApplyHookData = true;
$plugin->listRender(20, 'hookData');

function hookData($row)
{
	global $nuts, $plugin;
	
	$row['Gender'] = trim(ucwords(strtolower($row['Gender'])));
	$row['LastName'] = trim(ucwords(strtolower($row['LastName'])));
	$row['LastName'] = "{$row['Gender']} {$row['LastName']} {$row['FirstName']}";
	
	// $row['Password'] = nutsCrypt($row['Password'], false);
	$qID = $nuts->dbGetQueryId();
	$row['Password'] = nutsUserGetPassword($row['ID']);	
	$nuts->dbSetQueryId($qID);

    // avatar
    if(!$plugin->listExportExcelMode)
    {
        if(empty($row['Avatar']))$row['Avatar'] = '/nuts/img/gravatar.jpg';
        $row['Avatar'] = "<img src='{$row['Avatar']}' style='max-width:35px; max-height:35px;'>";
    }


	$format = ($_SESSION['Language'] == 'fr') ? 'd/m/y H:i' : 'Y-m-d H:i';
	if(empty($row['LastConnection']) || $row['LastConnection'] == '0000-00-00 00:00:00')
	{
		$row['LastConnection'] = '-';
	}
	else
	{
		$row['LastConnection'] = date($format, strtotime($row['LastConnection']));
	}
	

	return $row;
}



?>