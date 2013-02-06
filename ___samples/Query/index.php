<?php
/**
 * Query Component Sample
 */


// headers *************************************************************************
include("../../nuts/config.inc.php");
include("../../nuts/headers.inc.php");

// execution *************************************************************************
$job = new NutsCore();
$job->dbConnect();

$logs = Query::factory()->select("ID, Action, Resume")
						->from('NutsLog')
						->order_by("ID DESC")
						->limit(10)
						->executeAndGetAll();

foreach($logs as $log)
{
	echo "ID: {$log['ID']}<br>";
	echo "Action: {$log['Action']}<br>";
	echo "Resume: {$log['Resume']}<br>";
	echo "<hr>";
}

$job->dbClose();





?>