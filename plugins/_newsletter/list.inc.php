<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// ajax **************************************************************************************************
if(ajaxerRequested())
{
	if(ajaxerAction('get-error'))
	{
		$ID = @(int)$_GET['ID'];
		$errors = Query::factory()->select('TotalErrorEmail')->from('NutsNewsletter')->whereID($ID)->executeAndGetOne();
		if(!$errors || empty($errors))
		{
			die("No errors found");
		}
		
		// header txt
		$filecontent = str_replace('\n', "\n", $errors);
		$downloadfile = "newsletter $ID - error report.txt";
		
		header("Content-Type: plain/text");
		header("Content-disposition: attachment; filename='$downloadfile'");
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");
		die($filecontent);
		
	}
	
	
	die();
}


// assign table to db
$plugin->listSetDbTable('NutsNewsletter',
											"
												(SELECT COUNT(*) FROM NutsNewsletterData WHERE NutsNewsletterID = NutsNewsletter.ID) AS TotalViews,
												(SELECT COUNT(*) FROM NutsNewsletterMailingListSuscriber WHERE UnsuscribeNewletterID = NutsNewsletter.ID) AS TotalUnsuscribe
											");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldDate('DateCreate', 'Date', '', '', '>=');
$plugin->listSearchAddFieldDate('DateCreate2', 'Date', 'DateCreate', '', '<=');
$plugin->listSearchAddFieldSelectSql('Category', $lang_msg[19]);
$plugin->listSearchAddFieldText('Subject', $lang_msg[1]);
$plugin->listSearchAddFieldBoolean('Draft', $lang_msg[14]);




// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Category', $lang_msg[19], '; width:10px; white-space: nowrap;', true);
$plugin->listAddCol('DateCreate', 'Date', '; width:30px; white-space: nowrap;', true);
$plugin->listAddCol('uFrom', $lang_msg[2], '; width:30px; white-space: nowrap;', true);
$plugin->listAddCol('Subject', $lang_msg[1], '', true);


$plugin->listAddCol('Status', $lang_msg[22], 'center; width:60px; white-space: nowrap;', false);


$plugin->listAddCol('TotalSend', $lang_msg[3], 'center; width:60px; white-space: nowrap;', true);
$plugin->listAddCol('TotalError', $lang_msg[27], 'center; width:60px; white-space: nowrap;', true);
$plugin->listAddCol('TotalViews', $lang_msg[4], 'center; width:10px; white-space: nowrap;', true);
$plugin->listAddCol('TotalUnsuscribe', $lang_msg[5], 'center; width:10px; white-space: nowrap;', true);
$plugin->listAddCol('SchedulerDateStart', $lang_msg[25], 'center; width:10px; white-space: nowrap;', true);
$plugin->listAddCol('SchedulerDateEnd', $lang_msg[26], 'center; width:10px; white-space: nowrap;', false);

// render list
$plugin->listCopyButton = false;
$plugin->listAddSumRow($lang_msg[3], 'TotalSend');



$plugin->listExportExcelModeApplyHookData = true;
$plugin->listRender(20, 'hookData');


function hookData($row)
{
    global $nuts, $plugin, $lang_msg;
	
    if(($_SESSION['Language'] == 'fr'))
		$row['DateCreate'] = $nuts->db2Date($row['DateCreate']);
    
	
    if($row['Draft'] == 'YES')
	{
		$row['TotalSend'] = $row['TotalError'] = $row['TotalViews'] = $row['TotalUnsuscribe'] = $row['TotalViews'] = $row['TotalUnsuscribe'] = '-';
		$row = $plugin->listRowSetBackgroundColor('warning', $row);
		
		$row['Status'] = $lang_msg[14];
	}
	elseif($row['SchedulerStart'] == 'NO')
	{
		$row['Status'] = $nuts->db2Date($row['SchedulerDate']);
		$row['TotalSend'] = $row['TotalError'] = $row['TotalViews'] = $row['TotalUnsuscribe'] = $row['TotalViews'] = $row['TotalUnsuscribe'] = '-';
	}
	else
	{
		$row['Status'] = ($row['SchedulerFinished'] == 'NO') ? $lang_msg[15] : $lang_msg[16];
		
		
		$row = $plugin->listRowSetEditButtonHidden($row);
		
		$total_views = (@($row['TotalViews']/$row['TotalSend']) * 100);
		$total_views = round($total_views, 2);
		
		$total_unsuscribe = (@($row['TotalUnsuscribe']/$row['TotalSend']) * 100);
		$total_unsuscribe = round($total_unsuscribe, 2);
		
		$total_error = (@($row['TotalError']/$row['TotalSend']) * 100);
		$total_error = round($total_error, 2);
		
	
		// format
		$row['TotalSend'] = int_formatX($row['TotalSend']);
		$row['TotalViews'] = int_formatX($row['TotalViews']);
		$row['TotalUnsuscribe'] = int_formatX($row['TotalUnsuscribe']);
		$row['TotalError'] = int_formatX($row['TotalError']);
	
		// pourcentage
		$row['TotalViews'] .= " (".$total_views." %)";
		$row['TotalUnsuscribe'] .= " (".$total_unsuscribe." %)";
		$row['TotalError'] .= " (".$total_error." %)";
	}
	
	
	
	
	$row['SchedulerDateStart'] = $nuts->db2Date($row['SchedulerDateStart']);
	$row['SchedulerDateEnd'] = $nuts->db2Date($row['SchedulerDateEnd']);
 
	$row['Status'] = strtoupper(str_replace_latin_accents($row['Status']));
	
	
	if(!empty($row['TotalErrorEmail']))
	{
		$row['TotalError'] .= " <a href='?mod=_newsletter&do=list&ajaxer=1&_action=get-error&ID={$row['ID']}'><img align='middle' style='height:18px' src='/nuts/img/icon_extension/txt.png'></a>";
	}
	
	
	
	return $row;
}




