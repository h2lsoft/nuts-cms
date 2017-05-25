<?php

// right verification
if(!nutsPageManagerUserHasRight(0, 'comments', 0, $_GET['ID']))
{
	$error_message = "You can not delete comments on this page (right `comments` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas supprimer les commentaires de cette page (droit `commentaires` requis)";
	die($error_message);
}



$_GET['CommentID'] = (int)@$_GET['CommentID'];
$nuts->dbUpdate('NutsPageComment', array('Deleted' => 'YES'), "ID={$_GET['CommentID']} AND NutsPageID = {$_GET['ID']}");

$plugin->trace('comment_delete', (int)$_GET['ID']);

nutsTrigger('page-manager::comment_delete', true, "action delete on comment");

// launch exec for all suscribers
die('ok');

