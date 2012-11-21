<?php
/**
 * Plugin menu-category - action Delete
 * 
 * @version 1.0
 * @date 19/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


$plugin->deleteDbTable(array('NutsMenuCategory'));
$plugin->deleteRender();


// verify nb count
if($plugin->deleteUserHasConfirmed())
{
    // reposition category
    $IDs = Query::factory()->select('ID')
                            ->from('NutsMenuCategory')
                            ->order_by('Position')
                            ->executeAndGetAllOne();

    $pos = 0;
    foreach($IDs as $currentID)
    {
        $nuts->dbUpdate('NutsMenuCategory', array('Position' => ++$pos), "ID=$currentID");
    }

}




?>