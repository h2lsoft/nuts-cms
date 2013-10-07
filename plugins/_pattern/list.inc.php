<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsPattern');


// search engine
$plugin->listSearchAddFieldSelectSql('Type', $lang_msg[1]);
$plugin->listSearchAddFieldText('Name', $lang_msg[2]);
$plugin->listSearchAddFieldTextAjaxAutoComplete('Pattern');


// create fields
$plugin->listAddCol('ID', '', 'center; width:5px', true); // with order by
$plugin->listAddCol('Name', $lang_msg[2], ';', true);
// $plugin->listAddCol('Description', $lang_msg[3], 'white-space:nowrap;', false);
$plugin->listAddCol('Pattern', '', 'left; white-space:nowrap; width:5px', false);
$plugin->listAddCol('Code', '', '', false);
$plugin->listAddCol('BlocInside', 'Inside', 'center; white-space:nowrap; width:5px', false);


// render list
$plugin->listRender(20, 'hookData');
function hookData($row)
{
	$row['Name'] = "<b>{$row['Name']}</b><br />{$row['Description']}";


	$row['Code'] = htmlentities($row['Code'], ENT_QUOTES, "UTF-8");

    $row['BlocInside'] = '*';
    if(!empty($row['BlocStart']) && !empty($row['BlocEnd']))
    {
        $row['BlocStart'] = htmlentities($row['BlocStart']);
        $row['BlocEnd'] = htmlentities($row['BlocEnd']);
        $row['BlocInside'] = "{$row['BlocStart']}(*){$row['BlocEnd']}";
    }


	return $row;
}



?>