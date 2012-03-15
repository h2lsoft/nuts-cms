<?php

/* @var $plugin Plugin */

// sql table PLUGIN_PATH

$plugin->formDBTable(array('NutsNewsletterMailingListSuscriber')); // put table here

// fields
$plugin->formAddFieldSelectSql('NutsNewsletterMailingListID', 'Mailing-list', true);
$plugin->formAddFieldSelect('Language', $lang_msg[1], true, $nuts_lang_options);
$plugin->formAddFieldDateTime('Date', $lang_msg[2], true);
$plugin->formAddFieldText('Email', '', 'notEmpty|Email');




?>