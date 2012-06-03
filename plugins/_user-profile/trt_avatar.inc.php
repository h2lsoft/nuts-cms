<?php
/**
 * Avatar treatment
 */

$AvatarTmp = Query::factory()->select('AvatarTmpImage')
                       ->from('NutsUser')
                       ->where("ID = {$_SESSION['NutsUserID']}")
                       ->executeAndGetOne();


if(!empty($AvatarTmp))
{
    $avatar_image = '/library/media/images/avatar/'.$AvatarTmp;

    $f = array();
    $f['AvatarTmpImage'] = "";
    $f['Avatar'] = $avatar_image;
    $nuts->dbUpdate('NutsUser', $f, "ID = {$_SESSION['NutsUserID']}");
}








?>