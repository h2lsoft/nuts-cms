<?php

/* @var $plugin Plugin */

$plugin->formDBTable(array('NutsRteTemplate'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], true);
$plugin->formAddFieldText('Description', '', false);
$plugin->formAddFieldTextArea('Content', $lang_msg[2], true, 'tabby', "height:400px;");

