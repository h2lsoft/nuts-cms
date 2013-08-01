<?php

// right verification
if(!nutsPageManagerUserHasRight(0, 'comments', 0, $_GET['ID']))
{
	$error_message = "You can not update comments on this page (right `comments` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas mettre à jour le statut des commentaires de cette page (droit `commentaires` requis)";
	die($error_message);
}


// get current visible comment status
$_GET['CommentID'] = (int)@$_GET['CommentID'];
$nuts->doQuery("SELECT Visible FROM NutsPageComment WHERE ID = {$_GET['CommentID']} AND NutsPageID = {$_GET['ID']}");
$visible = ($nuts->dbGetOne() == 'YES') ? 'NO' : 'YES';

$_GET['CommentID'] = (int)@$_GET['CommentID'];
$nuts->dbUpdate('NutsPageComment', array('Visible' => $visible), "ID={$_GET['CommentID']} AND NutsPageID = {$_GET['ID']}");


$plugin->trace('comment_visible', (int)$_GET['CommentID'], $visible);

nutsTrigger('page-manager::comment_visible', true, "action visible on comment");

// launch exec for all suscribers
echo $visible;
exit(1);













?>