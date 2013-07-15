<?php
/**
 * Plugin comments - action List
 * 
 * @version 1.0
 * @date 31/12/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// ajaxer **************************************************************************************************************
if(ajaxerRequested())
{
	$IDS = ajaxerGetIDS();

	if(ajaxerAction('visible') || ajaxerAction('hidden'))
	{
		$bool = (ajaxerAction('visible')) ? 'YES' : 'NO';
		$nuts->dbUpdate('NutsPageComment', array('Visible' => $bool), "ID IN($IDS)");
	}
	elseif(ajaxerAction('deleted'))
	{
		$nuts->dbUpdate('NutsPageComment', array('Deleted' => 'YES'), "ID IN($IDS)");
	}

	die('ok');
}



// assign table to db
$plugin->listSetDbTable('NutsPageComment');

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldText('NutsPageID', 'Page ID');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Url');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Name', $lang_msg[1]);
$plugin->listSearchAddFieldTextAjaxAutoComplete('Email');
$plugin->listSearchAddFieldBoolean('Visible');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('NutsPageID', 'Page ID', 'center; width:30px', true);
$plugin->listAddCol('Url', 'Page url', 'center; width:30px', true);
$plugin->listAddCol('Avatar', '', 'center; width:30px', true);
$plugin->listAddCol('Date', '', '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Name', $lang_msg[1], '; width:30px; white-space:nowrap;', true);
$plugin->listAddCol('Message', '', '', false);
$plugin->listAddColImg('Visible');

// batch actions
$plugin->listAllowBatchActions = true;
$plugin->listAddBatchAction($lang_msg[2], ajaxerUrlConstruct('visible'));
$plugin->listAddBatchAction($lang_msg[3], ajaxerUrlConstruct('hidden'));
$plugin->listAddBatchAction($lang_msg[4], ajaxerUrlConstruct('deleted'));


// render list
$plugin->listCopyButton = false;
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;

    if(!empty($row['Url']))
    {
        $row['Url'] = "<a href=\"{$row['Url']}#comments\" target='_blank'><img src=\"/nuts/img/icon-web.gif\" /></a>";
    }

    if($_SESSION['Language'] == 'fr')
    {
        $row['Date'] = $nuts->db2date($row['Date']);
    }

    // avatar
    if(!$plugin->listExportExcelMode)
    {
        $default = '/nuts/img/gravatar.jpg';
        $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($row['Email'])))."?d=".urlencode($default)."&s=60";
        if(empty($row['Avatar']))$row['Avatar'] = $grav_url;
        $row['Avatar'] = "<img src='{$row['Avatar']}' style='max-width:60px; max-height:60px;'>";
    }

	// error
	if($row['Visible'] == 'NO')
	{
		$row['td_class'] = 'error';
	}


	return $row;
}



?>