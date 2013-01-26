<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsGroup',
                                    "(SELECT COUNT(*) FROM NutsUser WHERE NutsGroupID = NutsGroup.ID AND Deleted = 'NO') AS Total",
                                    "Priority >= (SELECT Priority FROM NutsGroup WHERE ID = {$_SESSION['NutsGroupID']})"
);

// create search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Name', $lang_msg[1]);


// create fields
$plugin->listAddCol('Priority', $lang_msg[5], 'center; width:50px', true);
$plugin->listAddCol('Name', $lang_msg[1], '; white-space:nowrap;', true); // with order by
$plugin->listAddCol('Description', $lang_msg[2]);

$plugin->listAddColImg('FrontofficeAccess', $lang_msg[21]);
$plugin->listAddColImg('BackofficeAccess', $lang_msg[20]);

$plugin->listAddColImg('AllowUpload', $lang_msg[16]);
$plugin->listAddColImg('AllowEdit', $lang_msg[17]);
$plugin->listAddColImg('AllowDelete', $lang_msg[18]);
$plugin->listAddColImg('AllowFolders', $lang_msg[19]);



$plugin->listAddCol('Total', $lang_msg[3], 'center; width:50px', true);


# force order order by to use at first asc
$plugin->listFirstOrderBy =  'Priority'; 
$plugin->listSetFirstOrderBySort('asc');



// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	$uri = 'index.php?mod=_user-manager&do=list&ID_operator=_equal_&ID=&NutsGroupID_operator=_equal_&NutsGroupID='.$row['ID'];
	$tmp = '<a class="counter" href="javascript:;" onclick="system_goto(\''.$uri.'\', \'content\');"><img src="img/icon-user.gif" align="absbottom" /> '.$row['Total'].'</a>';
	$row['Total'] = $tmp;

	return $row;
}




?>