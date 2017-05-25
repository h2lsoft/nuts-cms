<?php
/**
 * Exchange
 *
 * @date 25/02/13
 * @author 25/02/13
 *
 */

// controller **********************************************************************************************************
if(!isset($_GET['token']))die("Error: parameter `token` not found");
if(!isset($_GET['email']))die("Error: parameter `email` not found");


// includes ************************************************************************************************************
include_once("../../nuts/config.inc.php");
include_once("../../nuts/headers.inc.php");
include_once("config.inc.php"); # file configuration _edm
$nuts = new NutsCore();
$nuts->dbConnect();


// execution ***********************************************************************************************************
Query::factory()->select('
                            *,
                            (SELECT Language FROM NutsUser WHERE ID = NutsUserID) AS NutsUserLang,
                            (SELECT Email FROM NutsUser WHERE ID = NutsUserID) AS NutsUserEmail
                       ')
                       ->from('NutsEDMShare')
                       ->whereEqualTo('`To`', $_GET['email'])
                       ->whereEqualTo('Token', $_GET['token'])
                       ->limit(1)
                       ->execute();

if(!$nuts->dbNumRows())
{
    die("Error: file not found");
}

$rec = $nuts->dbFetch();

// expiration ?
Query::factory()->select('ID')
                ->from('NutsEDMShare')
                ->whereEqualTo('ID', $rec['ID'])
                ->where("DATE_ADD(DateCreate, INTERVAL {$rec['Expiration']} DAY) >= NOW()")
                ->execute();
if(!$nuts->dbNumRows())
{
    die("Error: file has expired");
}

// file exists ?
if(!file_exists("exchange/{$rec['ID']}.zip"))
{
    die("Error: file not exist (please contact administrator)");
}


// Log
$f = array();
$f['NutsEDMShareID'] = $rec['ID'];
$f['Date'] = 'NOW()';
$f['IP'] = $nuts->getIP();
$nuts->dbInsert('NutsEDMShareLog', $f);

// dl
$filepath = 'exchange/';
$filename = "{$rec['ID']}.zip";
$virtual_name = "{$rec['ZipName']}.zip";



header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"".$virtual_name."\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($filepath.$filename));
ob_end_flush();
@readfile($filepath.$filename);


// AR ?
if($rec['AR'] == 'YES')
{
    $nuts_user_lang = ($rec['NutsUserLang'] == 'fr') ? 'fr' : 'en';
    $data = array();
    $data['EMAIL'] = $rec['To'];
    $data['FILE_ZIP'] = $rec['ZipName'].'.zip';

    $mail = array();
    $mail['subject'] = $share_file_msg[$nuts_user_lang]['AR_subject'];
    $mail['body'] = $share_file_msg[$nuts_user_lang]['AR_message'];

    nutsSendEmail($mail, $data, $rec['NutsUserEmail'], $share_file_mail_add_signature, '', $share_file_mail_add_webapp_subject);
}



$nuts->dbClose();


