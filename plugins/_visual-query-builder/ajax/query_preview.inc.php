<?php

// security first word must be SELECT
$query = $_POST['query'];
$limit = (int)$_POST['limit'];
if(substr($query, 0, strlen("SELECT\n")) != "SELECT\n")
{
	die("Error: query must begins by SELECT clause");
}

// check LIMIT clause and replace it
if(!$limit || $limit > 500)$limit = 100;

$conn_str = "mysql:host=".$vqb_db_host.";dbname=".$vqb_db_schema.";port=".$vqb_db_port;
$conn = new PDO($conn_str, $vqb_db_user, $vqb_db_password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try
{
	$results = $conn->prepare($query);
	$results->setFetchMode(PDO::FETCH_ASSOC);
	$results->execute();

	$rowCount = $results->rowCount();

	$total = 0;
	$rows = array();
	while($row = $results->fetch())
	{
		$rows[] = $row;
		$total++;
		if($total == $limit)
			break;
	}
	$results->closeCursor();


	if(!$total)
	{
		$html = $lang_msg[14];
	}
	else
	{
		$html = "<h3>{$lang_msg[15]}: $rowCount ({$lang_msg[16]}: $limit)</h3>";
		$html .= array2table($rows, "", "", " class='datagrid'");
	}

	die($html);

}
catch (PDOException $e)
{
	$err_code = $e->getCode();
	$err_msg = $e->getMessage();
	$html = "<b>Error $err_code :</b><pre style='border:1px solid #ccc; padding:5px;'>".$err_msg.'</pre>';
	die($html);
}

