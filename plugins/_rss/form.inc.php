<?php

/* @var $plugin Plugin */

// sql table
$plugin->formDBTable(array('NutsRss'));

$plugin->formAddFieldText('RssTitle', $lang_msg[1], true, '', '', '', '', '', WEBSITE_NAME.' - RSS');
$plugin->formAddFieldText('RssDescription', $lang_msg[2], true, '', '', '', '', '', 'RSS - Really Simple Syndication');
$plugin->formAddFieldText('RssLink', $lang_msg[5], true, '', '', '', '', '', WEBSITE_URL);
$plugin->formAddFieldImageBrowser('RssImage', $lang_msg[6], false, 'nuts_rss');
$plugin->formAddFieldText('RssCopyright', $lang_msg[7], false, '', '', '', '', '', WEBSITE_NAME.' - '.WEBSITE_URL);
$plugin->formAddFieldText('RssLimit', $lang_msg[11], 'notEmpty|onlyDigit', '', 'width:50px; text-align:center;', '', '', '', 20);

// code
$plugin->formAddFieldsetStart('Code');
$plugin->formAddFieldTextArea('PhpCode', 'Php code', false, 'php', '', '', $lang_msg[8]);

$val = "SELECT
\tTitle AS title,
\tResume AS description,
\tDATE_FORMAT(DateGMT, '%m-%d-%Y %h:%i') AS pubDate,
\tVirtualPagename AS link
FROM
\tNutsNews
WHERE
\tDeleted = 'NO' AND
\tActive = 'YES' AND
\tDateGMT <= NOW()
ORDER BY
\tDateGMT DESC";

$plugin->formAddFieldTextArea('Query', '', true, 'sql', 'height:220px', '', $lang_msg[9], $val);
$plugin->formAddFieldTextArea('HookFunction', 'Hook function', false, 'php', '', '', $lang_msg[10]);
$plugin->formAddFieldsetEnd();

if($_POST)
{

}







?>