<?php

include(PLUGIN_PATH.'/form.inc.php');

$rec = $plugin->formInit();
$rec['MailingList'] = unserialize($rec['MailinglistIDs']);
$rec['ModeTest'] = 'NO';
$plugin->formInitSetRow($rec);



if($plugin->formValid())
{
	$CUR_ID = $plugin->formUpdate();
	
}