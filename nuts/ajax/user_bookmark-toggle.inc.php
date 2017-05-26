<?php

$response = ['error' => false, 'error_msg' => "", 'recordID' => 0];
$plugin = @$_POST['plugin'];

if(empty($plugin))
{
	$response = ['error' => true, 'error_msg' => "Error: plugin parameter not found"];
}
else
{
	$sql = "SELECT
					ID
			FROM
					NutsMenuRight
			WHERE
					NutsGroupID = {$_SESSION['NutsGroupID']} AND
					NutsMenuID IN(SELECT ID FROM NutsMenu WHERE Name = '$plugin')
			LIMIT
					1";
	$nuts->doQuery($sql);
	
	if(!$nuts->dbFetch())
	{
		$response = ['error' => true, 'error_msg' => "Error: plugin nout found or not allowed"];
	}
	else
	{
		// toggle favorite plugin
		$foundID = Query::factory()->select('ID')
		                           ->from('NutsUserShortcut')
			                       ->whereEqualTo('Plugin', $plugin)
			                       ->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])
			                       ->executeAndGetOne();
		if(!$foundID)
		{
			$max_position = (int)Query::factory()->select('Position')->from('NutsUserShortcut')->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])->order_by('Position DESC')->limit(1)->executeAndGetOne();
			$max_position += 1;
			
			$f = [];
			$f['NutsUserID'] = $_SESSION['NutsUserID'];
			$f['Plugin'] = $plugin;
			$f['Position'] = $max_position;
			
			$nuts->dbInsert('NutsUserShortcut', $f);
			$response['action'] = 'create';
		}
		else
		{
			$response['action'] = 'delete';
			$nuts->dbDelete('NutsUserShortcut', "ID=$foundID");
		}
		
		
	}
	
}





die(json_encode($response));