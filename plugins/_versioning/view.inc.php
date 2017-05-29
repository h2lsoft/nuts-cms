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
		{
			// content
			if(preg_match('/^(Content|Text)/', $key))
			{
				$plugin->viewAddFieldsetStart($key, fromCamelCase($key));
				$plugin->viewAddRow($key, ' ', nl2br(trim($data[$key])));
				$plugin->viewAddFieldsetEnd();
			}
			else
				$plugin->viewAddRow($key, fromCamelCase($key, false), nl2br($nuts->xssProtect($val)));
		}
		
	}
	
	
}




$plugin->viewRender();