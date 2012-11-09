<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsRegion'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], 'unique|notEmpty', '', '', '', 'maxlength="50"');
$plugin->formAddFieldTextArea('Description', $lang_msg[2], false);

$plugin->formAddFieldTextArea('Query', $lang_msg[6], true, 'sql', '', '', 'Add {@PAGE_ID} to replace with current page ID');
$plugin->formAddFieldTextArea('PhpCode', $lang_msg[4], false, 'php', '', '', $lang_msg[5]);
$plugin->formAddFieldTextArea('HtmlBefore', $lang_msg[15], false, 'html', '', '', $lang_msg[16]);
$plugin->formAddFieldTextArea('Html', $lang_msg[17], true, 'html');
$plugin->formAddFieldTextArea('HtmlAfter', $lang_msg[18], false, 'html', '', '', $lang_msg[19]);
$plugin->formAddFieldTextArea('HtmlNoRecord', $lang_msg[8], false, 'html');

$plugin->formAddFieldTextArea('HookData', '', false, 'php');

$plugin->formAddFieldText('Result', $lang_msg[9], true, 'number', 'width:35px', '', '', $lang_msg[10]);

$plugin->formAddFieldBooleanX('Pager', $lang_msg[13], true);
$plugin->formAddFieldText('SetUrl', 'Set url', false, '', "", "", "", "Force url");
$plugin->formAddFieldTextArea('PagerPreviousText', 'Pager Previous Text', false);
$plugin->formAddFieldTextArea('PagerNextText', 'Pager Next Text', false);

$plugin->formAddFieldBooleanX('PreviousStartEndVisible', "Add start/end pager", true);
$plugin->formAddFieldTextArea('PagerStartText', 'Pager Start Text', false);
$plugin->formAddFieldTextArea('PagerEndText', 'Pager End Text', false);



?>