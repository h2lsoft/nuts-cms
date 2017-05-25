<?php

$resp = array();
$table_name = $_POST['table'];

$sql = "SHOW FIELDS FROM `$table_name`";
$nuts->doQuery($sql);


$resp = $nuts->dbGetData();
$resp_joker[] = array('Field' => '*', 'Type' => '', 'Null' => '', 'Key' => 'JOKER', 'Default' => '', 'Extra' => '');
$resp = array_merge($resp_joker, $resp);


die(json_encode($resp));


