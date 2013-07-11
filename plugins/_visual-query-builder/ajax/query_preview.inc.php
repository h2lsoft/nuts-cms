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
$conn_str = "mysql:host=".NUTS_DB_HOST.";dbname=".NUTS_DB_BASE.";port=".NUTS_DB_PORT;
$conn = new PDO($conn_str, NUTS_DB_USER, NUTS_DB_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try
{
	$results = $conn->query($query);

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
	$html = "<b>Errror $err_code :</b><pre style='border:1px solid #ccc; padding:5px;'>".$err_msg.'</pre>';
	die($html);
}





?>