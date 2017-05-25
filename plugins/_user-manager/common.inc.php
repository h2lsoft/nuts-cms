<?php

/* @var $plugin Page */
/* @var $nuts Page */

$nuts->doQuery("SELECT Priority FROM NutsGroup WHERE ID = {$_SESSION['NutsGroupID']}");
$user_group_priority = (int)$nuts->dbGetOne();
$nuts->doQuery("SELECT ID FROM NutsGroup WHERE Priority >= {$user_group_priority} AND Deleted = 'NO'");
$allowed_groups = array(0); # prevent error
while($r = $nuts->dbFetch())
{
	if(!in_array($r['ID'], $nutsGroupIDHidddens))
	$allowed_groups[] = $r['ID'];
}


