<?php

// page locked
if(pageIsLocked($_GET['ID']))die($lang_msg[108]);

// right verification
if(!nutsPageManagerUserHasRight(0, 'edit', 0, $_GET['ID']))
{
	$error_message = "You can not rename this page (right `edit` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas renommer cette page (droit `éditer` requis)";
	die($error_message);
}



nutsTrigger('page-manager::rename_page', true, "action on rename page");
$nuts->dbUpdate('NutsPage', array('MenuName' => $_GET['XName']), "ID = ".(int)$_GET['ID']);
$plugin->trace('rename_page', (int)$_GET['ID']);

die('ok');






?>