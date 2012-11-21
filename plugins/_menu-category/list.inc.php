<?php
/**
 * Plugin menu-category - action List
 * 
 * @version 1.0
 * @date 19/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// initialization if count = 0 *****************************************************************************************
$num = Query::factory()->select('COUNT(*)')
                ->from('NutsMenuCategory')
                ->executeAndGetOne();

if(!$num)
{
    // include main view
    include(NUTS_PATH.'/lang/en.inc.php');
    include(NUTS_PATH.'/_custom_menu.inc.php');
    $en_mods = $mods_group;

    include(NUTS_PATH.'/lang/fr.inc.php');
    include(NUTS_PATH.'/_custom_menu.inc.php');
    $fr_mods = $mods_group;

    $position = 0;
    $i = 0;
    foreach($en_mods as $current_menu)
    {
        $f = array();
        $f['Name'] = $current_menu['name'];
        $f['NameFr'] = @$fr_mods[$i]['name'];
        $f['Color'] = $current_menu['color'];
        $f['Position'] = ++$position;
        $nuts->dbInsert('NutsMenuCategory', $f);
        $i++;
    }

}



// assign table to db
$plugin->listSetDbTable('NutsMenuCategory');

// search engine
// $plugin->listSearchAddFieldText('ID');

// create fields
// $plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', '', '; width:30px', false);
$plugin->listAddCol('Plugins', '', '', false);
$plugin->listAddCol('Color', '', 'center; width:30px', false);
$plugin->listAddCol('Position', '', 'center; width:30px', true);


// render list
$plugin->listCopyButton = false;
$plugin->listSetFirstOrderBy('Position');
$plugin->listSetFirstOrderBySort('ASC');
$plugin->listRender(0, 'hookData');


$qID = $nuts->dbGetQueryID();
function hookData($row)
{
	global $nuts, $plugin, $qID;

    $data = Query::factory()->select('Name')
                            ->from('NutsMenu')
                            ->where('Category', '=', $row['Position'])
                            ->order_by('Position ASC')
                            ->executeAndGetAll();

    $row['Plugins'] = '';
    foreach($data as $pl)
    {
        if(!empty($row['Plugins']))$row['Plugins'] .= ', ';
        $row['Plugins'] .= $pl['Name'];
    }

    // color
    $row['Color'] = "<div style='margin-left:10px; width:16px; height:16px; background:{$row['Color']}'></div>";

    // count
    if(!empty($row['Plugins']))
    {
        $row['Plugins'] .= "<script>$('#ls_tr_{$row['ID']} .list_btn_delete').hide();</script>";
    }


    $nuts->dbSetQueryID($qID);

	return $row;
}



?>