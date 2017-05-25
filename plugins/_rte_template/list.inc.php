<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsRteTemplate', "");

// create search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelectSql('Name', $lang_msg[1]);

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Name', $lang_msg[1], '', true); // with order by
$plugin->listAddCol('Description', '', '', false);

// popup
if(@$_GET['popup'] == 1)
{
	$plugin->listAddCol('AddCode', '&nbsp;', 'center; width:35px');
}


// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $nuts, $lang_msg;

	// add code
	if(@$_GET['popup'] == 1)
	{
		$code = $row['Content'];
		$code = str_replace("'", "\\'", $code);
		$code = str_replace('"', '``', $code);
		$code = str_replace(CR, '\\n', $code);
		$code = str_replace(TAB, '\\t', $code);
		$code = str_replace("\r", '\\r', $code);

		$row['AddCode'] = '<a href="javascript:;" onclick="window.opener.WYSIWYGAddText(\''.$_GET['parentID'].'\', \''.$code.'\'); window.close();" class="tt" title="'.$lang_msg[3].'"><i class="icon-arrow-down-3" style="font-size:18px; margin:0; padding:0;"></i></a>';
	}
	
	return $row;
}


