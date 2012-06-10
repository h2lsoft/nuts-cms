<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->listSetDbTable('NutsNewsletterMailingListSuscriber', "(SELECT Name FROM NutsNewsletterMailingList WHERE ID = NutsNewsletterMailingListSuscriber.NutsNewsletterMailingListID) AS MailingList");

// search engine
$plugin->listSearchAddFieldSelectSql("NutsNewsletterMailingListID", 'Mailing-list');
$plugin->listSearchAddFieldTextAjaxAutoComplete("Email", '', 'countains');
$plugin->listSearchAddFieldSelectSql("Language", $lang_msg[1]);
$plugin->listSearchAddFieldDate("Date");
$plugin->listSearchAddFieldDate("Date2", 'Date', "Date");

// create fields
$plugin->listAddCol('ID', '', 'center; width:10px; white-space:nowrap;', true);
$plugin->listAddCol('MailingList', 'Mailing-list', '; width:30px; white-space:nowrap;', true);
$plugin->listAddColImg('Language', $lang_msg[1], '', true, NUTS_IMAGES_URL.'/flag/{Language}.gif');
$plugin->listAddCol('Date', '', '; width:10px; white-space:nowrap;', true);
$plugin->listAddCol('Email', '', '; white-space:nowrap;', true);

// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
    global $nuts;

    if($_SESSION['Language'] == 'fr')$row['Date'] = $nuts->db2Date($row['Date']);

	return $row;
}




?>