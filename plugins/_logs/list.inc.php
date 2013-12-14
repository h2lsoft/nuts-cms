<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');

// reajuts DateGMT
if($plugin->listUserIsSearching())
{
	if(!@empty($_GET['DateGMT']))$_GET['DateGMT'] = nutsConvertUserDateToGMT($_GET['DateGMT']);
	if(!@empty($_GET['DateGMT2']))$_GET['DateGMT2'] = nutsConvertUserDateToGMT($_GET['DateGMT2']);
}


// assign table to db
$plugin->listSetDbTable('NutsLog',
									"DATE_ADD(DateGMT, INTERVAL {$_SESSION['Timezone']} HOUR) AS DateGMT,
									 DATE_ADD(DateGMT, INTERVAL {$_SESSION['Timezone']} HOUR) AS DateGMT2,
									(SELECT Name FROM NutsGroup WHERE ID = NutsLog.NutsGroupID) AS GroupName,
									(SELECT Login FROM NutsUser WHERE ID = NutsLog.NutsUserID) AS NutsUserName");

// search engine
$plugin->listSearchAddFieldDatetime('DateGMT', $lang_msg[2], '', '', '>=');
$plugin->listSearchAddFieldDatetime('DateGMT2', $lang_msg[2], 'DateGMT', '', '<=');
$plugin->listSearchAddFieldSelectSql('NutsGroupID', $lang_msg[1]);
// $plugin->listSearchAddFieldSelectSql('NutsUserID', 'User', "CONCAT(FirstName,' ',LastName)");
$plugin->listSearchAddFieldTextAjaxAutoComplete('NutsUserID', 'Login', 'begins', "Login" , 'ID', 'NutsUser');
$plugin->listSearchAddFieldSelectSql('Application');
$plugin->listSearchAddFieldSelectSql('Action');
$plugin->listSearchAddFieldText('Resume', 'Message');
$plugin->listSearchAddFieldText('RecordID');


// add purge button
$plugin->listAddButton('Purge', $lang_msg[3], "if(c=confirm('{$lang_msg[4]}'))system_goto('index.php?mod=_logs&do=list&_action=purge', 'content');");
if(isset($_GET['_action']) && $_GET['_action'] == 'purge')
{
    $nuts->doQuery("TRUNCATE TABLE NutsLog");
    nutsTrace('_logs', 'purge', '');
    die("<script>system_goto('index.php?mod=_logs&do=list','content');</script>");
}


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true); // with order by
$plugin->listAddCol('DateGMT', $lang_msg[2], 'center; width:120px', true); // with order by
$plugin->listAddCol('GroupName', $lang_msg[1], 'center', true); // with order by
$plugin->listAddCol('NutsUserName', 'Login', 'center', true);
$plugin->listAddCol('Application', '', 'center; white-space:nowrap;', true);
$plugin->listAddCol('Action', '', 'center', true);
$plugin->listAddCol('Resume', '', '');
$plugin->listAddCol('IP', '', 'center;', true);
$plugin->listAddCol('RecordID', '', 'center;', true);


// render list
$plugin->listRender(100, 'hookData');

function hookData($row)
{
	global $nuts;

	$row['IP'] = long2ip($row['IP']);
	$row['IP'] = '<a href="http://www.geoiptool.com/en/?IP='.$row['IP'].'" target="_blank">'.$row['IP'].'</a>';
	$row['Resume'] = '<span class="mini">'.$row['Resume'].'</span>';

	return $row;
}


