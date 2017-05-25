<?php

/* @var $plugin Plugin */

// sql table PLUGIN_PATH

$plugin->formDBTable(array('NutsNewsletterMailingList')); // put table here

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldText('Description', $lang_msg[2], false, 'ucfirst');


