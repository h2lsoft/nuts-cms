<?php

/* @var $plugin Plugin */

$plugin->formDBTable(array('NutsRteTemplate'));

// fields
$plugin->formAddFieldText('Name', '', true);
$plugin->formAddFieldText('Description', '', false);
$plugin->formAddFieldTextArea('Content', '', true, '', "height:400px;");


?>