<?php

if($_POST['IdentificationEmail'] == 'YES')
{
    // check group access
    $sql = "SELECT BackofficeAccess FROM NutsGroup WHERE ID = (SELECT NutsGroupID FROM NutsUser WHERE ID = {$_GET['ID']})";
    $nuts->doQuery($sql);
    $r = $nuts->dbFetch();

    $_POST['ConnectUrl'] = '/nuts/';
    if($r['BackofficeAccess'] == 'NO')
        $_POST['ConnectUrl'] = ($_POST['Language'] == 'fr' && LOGIN_PAGE_URL_FR != '') ? LOGIN_PAGE_URL_FR : LOGIN_PAGE_URL_EN;

	nutsSendEmail($lang_msg[13], $_POST, $_POST['Email']);

}



