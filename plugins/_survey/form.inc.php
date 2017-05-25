<?php

$multiLanguage = isWebsiteMultiLang();

/* @var $plugin Plugin */
$plugin->formDBTable(array('NutsSurvey'));


$plugin->formAddFieldTextArea('Title', $lang_msg[1], true);

if($multiLanguage)
	$plugin->formAddFieldBooleanX('I18N', '', 'booleanX', false);

$plugin->formAddFieldBooleanX('ViewResult', $lang_msg[5], true, $lang_msg[6]);


