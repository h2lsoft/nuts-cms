<?php
/**
 * Ajax treatment
 */

if(@$_GET['ajax'] && @in_array($_GET['_action'], array('user_init')))
{
    $_GET['ID'] = (int)@$_GET['ID'];


    // user_init *******************************************************************************************************
    if($_GET['_action'] == 'user_init')
    {

        Query::factory()->select('NutsUserID')
                        ->from('NutsEDMGroupUser')
                        ->where("NutsEDMGroupID = {$_GET['ID']}")
                        ->order_by('ID')
                        ->execute();

        $data = array();
        while($r = $nuts->dbFetch())
        {
            $data[] = $r['NutsUserID'];
        }

        $data = array_unique($data);
        die(json_encode($data));
    }

}







?>