<?php
/* @var $plugin Plugin */

$plugin->viewDbTable(array('NutsVersion'));

$dataX = Query::factory()->select('DataSerialized')->from('NutsVersion')->whereID($_GET['ID'])->executeAndGetOne();
$data = unserialize($dataX);


if(isset($data['_file']) && !empty($data['_file']))
{
	$plugin->viewAddRow('File', "", $data['_file']);
	
	$plugin->viewAddFieldsetStart('Content', "Content");
	$plugin->viewAddRow('Content', ' ', nl2br(trim($data['Content'])));
	$plugin->viewAddFieldsetEnd();
}
else
{
	foreach($data as $key => $val)
	{
		if($key[0] != '_')
			$plugin->viewAddRow($key, $key, nl2br($nuts->xssProtect($val)));
	}
}




$plugin->viewRender();