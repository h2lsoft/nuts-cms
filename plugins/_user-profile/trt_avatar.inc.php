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

    $avatar = NutsORM::factory('NutsUser');
    $avatar->ID = $_SESSION['NutsUserID'];
    $avatar->AvatarTmpImage = '';
    $avatar->Avatar = $avatar_image;
    $avatar->update();
}





