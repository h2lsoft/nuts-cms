<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// create files for url rewriting rules
$sql = "SELECT * FROM NutsUrlRewriting WHERE Type= 'SIMPLE' AND Deleted = 'NO' ORDER BY Position";
$nuts->doQuery($sql);

$str_file = '<?php'.CR.CR;

$str_file .= '// simples replacements *************************'.CR;
$str_file .= '$uri_str_patterns = array();'.CR;
$str_file .= '$uri_str_replaces = array();'.CR.CR;
while($row = $nuts->dbfetch())
{
	$str_file .= '$uri_str_patterns[] = "'.$row['Pattern'].'";'.CR;
	$str_file .= '$uri_str_replaces[] = "'.$row['Replacement'].'";'.CR.CR;
}


// regex
$sql = "SELECT * FROM NutsUrlRewriting WHERE Type= 'REGEX' AND Deleted = 'NO' ORDER BY Position";
$nuts->doQuery($sql);

$str_file .= CR.CR;
$str_file .= '// regex replacements *************************'.CR;
$str_file .= '$uri_patterns = array();'.CR;
$str_file .= '$uri_replaces = array();'.CR.CR;
while($row = $nuts->dbfetch())
{
	$str_file .= '$uri_patterns[] = "'.str_replace('§', '$', $row['Pattern']).'";'.CR;
	$str_file .= '$uri_replaces[] = "'.str_replace('§', '$', $row['Replacement']).'";'.CR.CR;
}


file_put_contents(NUTS_PATH.'/url_rewriting_rules.inc.php', $str_file);

