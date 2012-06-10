<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->listSetDbTable('NutsNewsletter',
											"
												(SELECT COUNT(*) FROM NutsNewsletterData WHERE NutsNewsletterID = NutsNewsletter.ID) AS TotalViews,
												(SELECT COUNT(*) FROM NutsNewsletterMailingListSuscriber WHERE UnsuscribeNewletterID = NutsNewsletter.ID) AS TotalUnsuscribe
											");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldText('Subject', $lang_msg[1]);


// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Date', '', '; width:30px; white-space: nowrap;', false);
$plugin->listAddCol('uFrom', $lang_msg[2], '; width:30px; white-space: nowrap;', true);
$plugin->listAddCol('Subject', $lang_msg[1], '', true);
$plugin->listAddCol('TotalSend', $lang_msg[3], 'center; width:60px; white-space: nowrap;', true);
$plugin->listAddCol('TotalViews', $lang_msg[4], 'center; width:10px; white-space: nowrap;', true);
$plugin->listAddCol('TotalUnsuscribe', $lang_msg[5], 'center; width:10px; white-space: nowrap;', true);


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
    global $nuts;

	$row['Date'] = nutsGetGMTDateUser($row['LogActionCreateDateGMT']);
    if($_SESSION['Language'] == 'fr')$row['Date'] = $nuts->db2Date($row['Date']);
	
	$total_views = (@($row['TotalViews']/$row['TotalSend']) * 100);
	$total_views = round($total_views, 2);
	$total_unsuscribe = (@($row['TotalUnsuscribe']/$row['TotalSend']) * 100);
	$total_unsuscribe = round($total_unsuscribe, 2);
	
	// pourcentage
	$row['TotalViews'] .= " (".$total_views." %)";
	$row['TotalUnsuscribe'] .= " (".$total_unsuscribe." %)";
	
	return $row;
}



?>