<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsRegion'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], 'unique|notEmpty', '', '', '', 'maxlength="50"');
$plugin->formAddFieldTextAjaxAutoComplete('Category', $lang_msg[26], false);
$plugin->formAddFieldTextArea('Description', $lang_msg[2], false);

$plugin->formAddFieldTextArea('Query', $lang_msg[6], true, 'sql', 'height:250px;', '', $lang_msg[27]);
$plugin->formAddFieldTextArea('PhpCode', $lang_msg[4], false, 'php', 'height:150px;', '', $lang_msg[5]);
$plugin->formAddFieldTextArea('HtmlBefore', $lang_msg[15], false, 'html', '', '', $lang_msg[16]);
$plugin->formAddFieldTextArea('Html', $lang_msg[17], true, 'html');
$plugin->formAddFieldTextArea('HtmlAfter', $lang_msg[18], false, 'html', '', '', $lang_msg[19]);
$plugin->formAddFieldTextArea('HtmlNoRecord', $lang_msg[8], false, 'html');

$plugin->formAddFieldTextArea('HookData', '', false, 'php');

$plugin->formAddFieldText('Result', $lang_msg[9], true, 'number', 'width:35px', '', '', $lang_msg[10]);

$plugin->formAddFieldBooleanX('Pager', $lang_msg[13], true);
$plugin->formAddFieldText('SetUrl', $lang_msg[20], false, '', "", "", "", "Force url");
$plugin->formAddFieldTextArea('PagerPreviousText', $lang_msg[21], false);
$plugin->formAddFieldTextArea('PagerNextText', $lang_msg[22], false);

$plugin->formAddFieldBooleanX('PreviousStartEndVisible', $lang_msg[23], true);
$plugin->formAddFieldTextArea('PagerStartText', $lang_msg[24], false);
$plugin->formAddFieldTextArea('PagerEndText', $lang_msg[25], false);


if($_POST)
{
	$_POST['Name'] = ucfirst($_POST['Name']);
	$_POST['Description'] = ucfirst($_POST['Description']);
	$_POST['Category'] = ucfirst($_POST['Category']);
}


?>