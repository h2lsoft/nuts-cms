<?php

/* @var $plugin Plugin */
$_GET['ID'] = $_SESSION['NutsUserID'];

include(PLUGIN_PATH.'/config.inc.php');
include(PLUGIN_PATH.'/form.inc.php');


$plugin->formInit();
$r = $plugin->formInitGetRow();

$r['Password'] = nutsUserGetPassword($r['ID']);

$plugin->formInitSetRow($r);

if($plugin->formValid())
{
	$CUR_ID = $plugin->formUpdate();

    if($profile_enable_password_change)
	    nutsUserSetPassword($CUR_ID, $_POST['Password']);

    // avatar treatment
    include(PLUGIN_PATH.'/trt_avatar.inc.php');

    nutsTrigger('_user-profile', true, "User change its profile");
}


?>