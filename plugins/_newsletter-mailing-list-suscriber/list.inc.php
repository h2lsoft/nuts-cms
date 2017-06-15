<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


if(ajaxerRequested())
{
	if(ajaxerAction('deleted'))
	{
		$IDS = ajaxerGetIDS();
		$nuts->dbUpdate('NutsNewsletterMailingListSuscriber', array('Deleted' => 'YES'), "ID IN($IDS)");
		die('ok');
	}
}

// assign table to db
$plugin->listSetDbTable('NutsNewsletterMailingListSuscriber', "(SELECT Name FROM NutsNewsletterMailingList WHERE ID = NutsNewsletterMailingListSuscriber.NutsNewsletterMailingListID) AS MailingList");

// search engine
$plugin->listSearchAddFieldSelectSql("NutsNewsletterMailingListID", 'Mailing-list');
$plugin->listSearchAddFieldTextAjaxAutoComplete("Email", '', 'countains');
$plugin->listSearchAddFieldTextAjaxAutoComplete("LastName", $lang_msg[4], 'countains');
$plugin->listSearchAddFieldTextAjaxAutoComplete("FirstName", $lang_msg[5], 'countains');
$plugin->listSearchAddFieldSelectSql("Language", $lang_msg[1]);
$plugin->listSearchAddFieldDate("Date");
$plugin->listSearchAddFieldDate("Date2", 'Date', "Date");

// create fields
$plugin->listAddCol('ID', '', 'center; width:10px; white-space:nowrap;', true);
$plugin->listAddCol('MailingList', 'Mailing-list', '; width:30px; white-space:nowrap;', true);
$plugin->listAddColImg('Language', $lang_msg[1], '', true, NUTS_IMAGES_URL.'/flag/{Language}.gif');
$plugin->listAddCol('Date', '', '; width:10px; white-space:nowrap;', true);
$plugin->listAddCol('Email', '', '; white-space:nowrap;', true);
$plugin->listAddCol('LastName', $lang_msg[4], '', true);
$plugin->listAddCol('FirstName', $lang_msg[5], '', true);

// render list
$plugin->listAllowBatchActions = true;
$plugin->listAddBatchAction($lang_msg[6], ajaxerUrlConstruct('deleted'));

$plugin->listRender(20, 'hookData');


function hookData($row)
{
    global $nuts;

    if($_SESSION['Language'] == 'fr')$row['Date'] = $nuts->db2Date($row['Date']);

	return $row;
}



