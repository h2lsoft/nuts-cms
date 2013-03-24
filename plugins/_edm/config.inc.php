<?php
/**
 * Plugin edm - configuratation File
 *
 * @version 1.0
 * @date 03/07/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

$max_file_size = ini_get('upload_max_filesize'); // 2M by default

$valid_chars_regex = '.A-Za-z0-9_ !@%()+=\[\],~`\-àâäéèêïîùûüôöç'; // characters allowed in the file name (in a Regular Expression format)

$customFilters = array(
    "MS Office" => ".doc|.docx|.dot|.dotm|.xsl|.xlsx|.ppt|.pptx",
    "Open Office" => ".odt|.ods|.odg|.odf|.odc|.ods|.ots|.ott",
    "PDF" => ".pdf",
    "ZIP" => ".zip|.rar"
);

$share_file_expiration = 15; // default days for expiration
$share_file_max_expiration = 45; // max expiration
$share_file_max_zip_size = 500*1024*1024; // 500M
$share_file_memory_limit = '100M'; // max memory limit usage

$author = (isset($_SESSION['LastName'])) ? $_SESSION['FirstName'].' '.$_SESSION['LastName'] : '';


$share_file_mail_add_webapp_subject = true;
$share_file_mail_add_signature = true;

$share_file_msg = array();
$share_file_msg['en'] = array(
                                'subject' => "{$author} want to share files with you",
                                'message' => "Hi,\\n\\nTo download the file, thank you click on the link below :\\n[FILE_URL]\\n\\n$author",// [FILE_URL] required
                                'AR_subject' => "Notification download file",
                                'AR_message' => "{EMAIL} has downloaded file {FILE_ZIP}", // {EMAIl} {FILE_ZIP} required
                                'zip_name' => "files"
                            );


$share_file_msg['fr'] = array(
                                'subject' => "{$author} souhaite partager un fichier avec vous",
                                'message' => "Bonjour,\\n\\nPour télécharger le fichier, merci de cliquer sur le lien ci-dessous :\\n[FILE_URL]\\n\\n$author",// [FILE_URL] required
                                'AR_subject' => "Notification de téléchargement fichier",
                                'AR_message' => "Le client {EMAIL} a bien téléchargé le fichier {FILE_ZIP}", // {EMAIl} {FILE_ZIP} required
                                'zip_name' => "fichiers"
                            );




?>