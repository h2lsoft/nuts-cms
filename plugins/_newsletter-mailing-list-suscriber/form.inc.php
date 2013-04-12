<?php

/* @var $plugin Plugin */

// sql table PLUGIN_PATH

$plugin->formDBTable(array('NutsNewsletterMailingListSuscriber')); // put table here

// fields
$plugin->formAddFieldSelectSql('NutsNewsletterMailingListID', 'Mailing-list', true);
$plugin->formAddFieldSelect('Language', $lang_msg[1], true, $nuts_lang_options);
$plugin->formAddFieldDateTime('Date', $lang_msg[2], true);
$plugin->formAddFieldText('Email', '', 'notEmpty|Email');
$plugin->formAddFieldText('LastName', $lang_msg[4], false, 'ucfirst');
$plugin->formAddFieldText('FirstName', $lang_msg[5], false, 'ucfirst');



if($_POST)
{
    if(!empty($_POST['NutsNewsletterMailingListID']))
    {
        Query::factory()->select('Email')
                        ->from('NutsNewsletterMailingListSuscriber')
                        ->whereNotEqualTo('ID', $_GET['ID'])
                        ->whereEqualTo('NutsNewsletterMailingListID', $_POST['NutsNewsletterMailingListID'])
                        ->whereEqualTo('Email', $_POST['Email'])
                        ->limit(1)
                        ->execute();

        if($nuts->dbNumRows() > 0)
        {
            $nuts->addError('Email', $lang_msg[3]);
        }
    }
}



?>