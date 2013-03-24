<?php
/**
 * Plugin edm-share - action List
 *
 * @version 1.0
 * @date 25/02/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsEDMShare', "
                                            `To` AS ClientEmail,
                                            (SELECT Login FROM NutsUser WHERE ID = NutsUserID) AS Login,
                                            (SELECT Date FROM NutsEDMShareLog WHERE NutsEDMShareID = NutsEDMShare.ID AND Deleted = 'NO' ORDER BY Date DESC LIMIT 1) AS LastDownload
                                         ");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldText('ClientEmail', 'Email');
$plugin->listSearchAddFieldDatetime('Date');
$plugin->listSearchAddFieldDatetime('Date2', 'Date', 'Date');
$plugin->listSearchAddFieldTextAjaxAutoComplete('NutsUserID', 'Login', 'begins', 'Login', 'ID', 'NutsUser');
$plugin->listSearchAddFieldText('ZipName', 'Zip name');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px;', true);
$plugin->listAddCol('DateCreate', 'Date', 'center; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Login', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('ClientEmail', 'Email', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('Message', '', '', false);
$plugin->listAddCol('ZipName', 'Zip name', '', false);
$plugin->listAddCol('Files', '', '', false);
$plugin->listAddCol('Expiration', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('LastDownload', 'Last download', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddColImg('AR');

// render list
$plugin->listAllowExcelExport = false;
$plugin->listRender(20, 'hookData');


function hookData($row)
{
    global $nuts, $plugin;


    if(empty($row['AR']))$row['AR'] = 'NO';

    $row['ZipName'] .= ".zip";
    $row['Files'] = "<span class='mini'>".nl2br($row['Files'])."</span>";

    $row['Message'] = nl2br($row['Message']);
    $row['Message'] = "<b>{$row['Subject']}</b><br><span class='mini'>{$row['Message']}</span>";

    return $row;
}



?>