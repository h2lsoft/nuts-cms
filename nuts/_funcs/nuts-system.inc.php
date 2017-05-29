<?php
/**
 * System
 * @package Functions
 * @version 1.0
 */


/**
 * Return current date in gtml mode
 *
 * @return sql-datetime sql date => 'Y-m-d H:i:s'
 */
function nutsGetGMTDate()
{
	return gmdate('Y-m-d H:i:s');
}


/**
 * Allow to start/auto registered trigger
 *
 * @param $name
 * @param bool $auto_register (false)
 * @param string $description
 */
function nutsTrigger($name, $auto_register=false, $description="")
{
	global $nuts;

	$sql = "SELECT * FROM NutsTrigger WHERE Deleted = 'NO' AND Name = '".sqlX($name)."' LIMIT 1";
	$nuts->doQuery($sql);
	if(!$nuts->dbNumRows())
	{
		if($auto_register)
		{
			$f = array();
			$f['Name'] = $name;
			$f['Description'] = ucfirst($description);
			$nuts->dbInsert('NutsTrigger', $f);
		}
	}
	else
	{
		$rec = $nuts->dbFetch();
		$rec['PhpCode'] = trim($rec['PhpCode']);
		if(!empty($rec['PhpCode']))
		{
			eval($rec['PhpCode']);
		}
	}
}

/**
 * Add a new version of record
 *
 * @param $application
 * @param $recordID
 * @param $data (use _table our _file and use _linked array to associate data)
 * @param $NutsUserID
 */
function nutsVersioningAdd($application, $recordID, $data=[], $exceptions=[], $NutsUserID=0)
{
	global $nuts;
	
	$appX = sqlX($application);
	$recordID = (int)$recordID;
	
	$data2 = [];
	foreach($data as $key => $val)
	{
		$joker_found = false;
		
		foreach($exceptions as $exception)
		{
			$tmp = explode('*', $exception);
			if(count($tmp) == 2)
			{
				$str = $tmp[0];
				if(stripos($key, $str) !== false && stripos($key, $str) == 0)
				{
					$joker_found = true;
					break;
				}
			}
		}
		
		if(!in_array($key, $exceptions) && !$joker_found)
			$data2[$key] = $data[$key];
	}
	
	$f = [];
	$f['Date'] = 'NOW()';
	$f['Application'] = $application;
	$f['RecordID'] = $recordID;
	$f['DataSerialized'] = serialize($data2);
	$f['NutsUserID'] = $NutsUserID;
	
	$nuts->dbInsert('NutsVersion', $f);
	
	// clean table alst entries
	include NUTS_PLUGINS_PATH.'/_versioning/config.inc.php';
	
	$count = Query::factory()->select('COUNT(*)')->from('NutsVersion')->whereEqualTo('Application', $application)->whereEqualTo('RecordID', $recordID)->executeAndGetOne();
	if($count > $VERSIONING_MAX)
	{
		$diff = $count - $VERSIONING_MAX;
		
		$sql = "DELETE FROM NutsVersion WHERE Application = '$appX' AND RecordID=$recordID ORDER BY ID ASC LIMIT {$diff}";
		$nuts->doQuery($sql);
	}
	
	
	
}

