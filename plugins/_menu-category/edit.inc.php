<?php
/**
 * Plugin menu-category - action Edit
 * 
 * @version 1.0
 * @date 19/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/form.inc.php');

$rec = $plugin->formInit();
if($plugin->formValid())
{
    // switch ?
    $old_position = Query::factory()->select('Position')->from('NutsMenuCategory')->where('ID', '=', $_GET['ID'])->executeAndGetOne();
    $new_position = $_POST['Position'];

	$CUR_ID = $plugin->formUpdate();

    if($old_position != $new_position)
    {
        // update category from NutsMenu
        $sql = "SELECT ID, Category FROM NutsMenu WHERE Category IN($old_position, $new_position)";
        $nuts->doQuery($sql);
        $arr = $nuts->dbGetData();
        foreach($arr as $cur)
        {
            $pos = ($cur['Category'] == $old_position) ? $new_position : $old_position;
            $nuts->dbUpdate('NutsMenu', array('Category' => $pos), "ID={$cur['ID']}");
        }

        // repositon current category
        $nuts->dbUpdate('NutsMenuCategory', array('Position' => $old_position), "Position = $new_position AND ID != $CUR_ID");

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

}


?>