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





?>