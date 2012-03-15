<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

$plugin->viewDbTable(array('NutsIM'));

$plugin->viewAddSQLField("(SELECT CONCAT(Lastname,' ', Firstname) FROM NutsUser WHERE ID = NutsIM.NutsUserIDFrom) AS NutsUserFrom");
$plugin->viewAddSQLField("CONCAT('<br/><div style=\"padding:20px; margin-bottom:20px; border:1px solid #ccc; border-radius:4px; -moz-border-radius:4px; -webkit-border-radius:4px;\">',Message,'</div>') AS Message");


$plugin->viewAddVar('Date', $lang_msg[5]);
$plugin->viewAddVar('NutsUserFrom', $lang_msg[3]);
$plugin->viewAddVar('Subject', $lang_msg[1]);
$plugin->viewAddVar('Message', '&nbsp;');
$plugin->viewRender();

// update information *****************************************************
$_GET['ID'] = (int)@$_GET['ID'];
$nuts->dbUpdate('NutsIM', array('Viewed' => 'YES', 'DateViewed' => 'NOW()'), "ID = {$_GET['ID']}");
echo "<script>system_refresh();</script>"






?>