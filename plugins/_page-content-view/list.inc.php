<?php
/**
 * Plugin page-content-view - action List
 * 
 * @version 1.0
 * @date 20/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsPageContentView', "
                                                    (SELECT COUNT(*) FROM NutsPageContentViewField WHERE NutsPageContentViewID = NutsPageContentView.ID AND Deleted = 'NO') AS Fields
                                                ");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Name');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', '', '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Fields', '', 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('Description', '', '', false);



// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

    	$row['Fields'] = <<<EOF
<img src="img/widget.png" align="absbottom" style="width:16px;" />
<a href="javascript:popupModal('/nuts/?mod=_page-content-view-fields&do=list&popup=1&NutsPageContentViewID={$row['ID']}&NutsPageContentViewID_operator=_equal_&user_se=1');">{$row['Fields']}</a>
EOF;

	
	return $row;
}



?>