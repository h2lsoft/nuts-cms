<?php

// get page right + autojs
$data = array();
$data['js'] = "";
$data['error_right_edit'] = false;
$data['error_right_edit_message'] = "";
$data['rights'] = array();

// get author
$authorID = (int)Query::factory()->select('NutsUserID')->from('NutsPage')->whereID($_GET['ID'])->executeAndGetOne();

// apply js if not author ot nor super admin
if(!nutsUserGroupIs(1) && $_SESSION['NutsUserID'] != $authorID)
	$data['js'] = "disabled";

// get rights
$tmp = Query::factory()->select('NutsGroupID, Action')->from('NutsPageRights')->whereEqualTo('NutsPageID', $_GET['ID'])->executeAndGetAll();
$data['rights'] = $tmp;

// check if user has right
if(!nutsPageManagerUserHasRight(0, 'edit', 0, $_GET['ID']))
{
	$error_message = "You can not edit this page (right `edit` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas éditer cette page (droit `modification` requis)";

	$data['error_right_edit'] = true;
	$data['error_right_edit_message'] = $error_message;

}


die(json_encode($data));

