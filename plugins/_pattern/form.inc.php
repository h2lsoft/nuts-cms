<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsPattern'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[2], true);
$plugin->formAddFieldSelect('Type', $lang_msg[1], true, array('HTML','PHP','REGEX'));
$plugin->formAddFieldTextArea('Description', $lang_msg[3], false);
$plugin->formAddFieldTextArea('Pattern', '', true);
$plugin->formAddFieldTextArea('Code', '', true, 'tabby', '', '', "Php> use `\$rep` variable to set content");

$plugin->formAddFieldText('BlocStart', 'Bloc start', false, '', '', '', '', 'pattern inside bloc');
$plugin->formAddFieldText('BlocEnd', 'Bloc end', false, '', '', '', '', 'pattern inside bloc');


?>