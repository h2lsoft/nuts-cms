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