<?php

/* @var $plugin Plugin */

// sql table
$plugin->formDBTable(array('NutsRss'));

$plugin->formAddFieldText('RssTitle', $lang_msg[1], true);
$plugin->formAddFieldText('RssDescription', $lang_msg[2], true);
$plugin->formAddFieldText('RssLink', $lang_msg[5], true, '', '', '', '', '', WEBSITE_URL);
$plugin->formAddFieldImageBrowser('RssImage', $lang_msg[6], false, 'nuts_rss');
$plugin->formAddFieldText('RssCopyright', $lang_msg[7], false, '', '', '', '', '', WEBSITE_NAME.' - '.WEBSITE_URL);
$plugin->formAddFieldText('RssLimit', $lang_msg[11], 'notEmpty|onlyDigit', '', 'width:50px; text-align:center;');

// code
$plugin->formAddFieldsetStart('Code');
$plugin->formAddFieldTextArea('PhpCode', 'Php code', false, 'php', '', '', $lang_msg[8]);
$plugin->formAddFieldTextArea('Query', '', true, 'sql', '', '', $lang_msg[9]);
$plugin->formAddFieldTextArea('HookFunction', 'Hook function', false, 'php', '', '', $lang_msg[10]);
$plugin->formAddFieldsetEnd();

if($_POST)
{
	// link force absolute url
	if($_POST['RssLink'][0] == '/')
	{
		$_POST['RssLink'] = WEBSITE_URL.'/'.$_POST['RssLink'];
	}
	
}







?>