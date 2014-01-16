<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->listSetDbTable('NutsNewsletterMailingList', "(SELECT COUNT(*) FROM NutsNewsletterMailingListSuscriber WHERE Deleted = 'NO' AND UnsuscribeNewletterID = 0 AND NutsNewsletterMailingListID = NutsNewsletterMailingList.ID) AS Total");


// create fields
$plugin->listAddCol('ID', '', 'center; width:10px; white-space:nowrap;', true);
$plugin->listAddCol('Name', $lang_msg[1], '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Description', $lang_msg[2], 'left;', true);
$plugin->listAddCol('Total', $lang_msg[3], 'center; width:30px; white-space:nowrap;', true);

$plugin->listSetFirstOrderBySort('asc');


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{

	$row['Total'] = int_formatX($row['Total']);

	$uri = 'index.php?mod=_newsletter-mailing-list-suscriber&do=list&ID_operator=_equal_&ID=&NutsNewsletterMailingListID_operator=_equal_&NutsNewsletterMailingListID='.$row['ID'];
	$tmp = '<a class="counter" href="javascript:;" onclick="system_goto(\''.$uri.'\', \'content\');"> <i class="icon-mail"></i>'.$row['Total'].'</a>';
	$row['Total'] = $tmp;

	return $row;
}



