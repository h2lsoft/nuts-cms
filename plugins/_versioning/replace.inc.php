<?php
/* @var $plugin Plugin */
$record = Query::factory()->from('NutsVersion')->whereID($_GET['ID'])->executeAndFetch();

if(!$_GET['ID'])
	die("Error: ID `{$_GET['ID']}` not found");

$data = unserialize($record['DataSerialized']);

echo "<pre>";

// type file
if(isset($data['_file']))
{
	echo "Type => FILE<br>";
	echo "Target => {$data['_file']}<br>";
	
	$content = trim($data['Content']);
	
	if(empty($content))
		die("Error: file content is empty");
	
	
	// save old file content
	echo "Save old content from `{$data['_file']}`<br>";
	$data_old = $data;
	$data_old['Content'] =  file_get_contents($data['_file']);
	nutsVersioningAdd($record['Application'], 0, $data_old);
	
	
	// save file
	echo "Save new content to `{$data['_file']}`<br>";
	file_put_contents($data['_file'], $content);
	nutsVersioningAdd($record['Application'], 0, $data);
}

// type table
if(isset($data['_table']))
{
	echo "Type => TABLE<br>";
	echo "Target => {$data['_table']}<br>";
	echo "Record ID => #{$record['RecordID']}<br>";
	
	if(!$record['RecordID'])
		die("Error: record ID is empty");
	
	// check linked table
	$has_link = false;
	if(isset($data['_linked']))
	{
		$has_link = true;
		foreach($data['_linked'] as $link)
		{
			echo "Target LINK => {$link['_table']}<br>";
		}
	}
	
	// update record
	echo "Update record #{$record['RecordID']}<br>";
	$nuts->dbUpdate($data['_table'], $data, "ID={$record['RecordID']}", ["_*"]);
	
	// linked data
	if($has_link)
	{
		foreach($data['_linked'] as $link)
		{
			echo "Update linked record table `{$link['_table']}` => `ID` = #{$link['ID']}<br>";
			$nuts->dbUpdate($link['_table'], $link, "ID={$link['ID']}", ["_*"]);
		}
	}
	
	
	// add versioning
	echo "Save new versioning `{$record['Application']}` #{$record['RecordID']}<br>";
	nutsVersioningAdd($record['Application'], $record['RecordID'], $data);
	
}


echo "<br><br>+++++++++++++++++ FININSH +++++++++++++++++";

echo "</pre>";
die();






