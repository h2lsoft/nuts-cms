<?php
/**
 * Query Component Sample
 *
 * @package Nuts-Component
 * @version 1.0
 * @date 30/11/2014
 */

// headers *************************************************************************
include("../../nuts/config.inc.php");
include("../../nuts/headers.inc.php");

// execution *************************************************************************
$job = new NutsCore();
$job->dbConnect();

$debug_sql = true;
$logs = Query::factory()->select("FirstName, Action, Resume")
						->from('NutsLog, NutsUser')
						->join()
						->order_by("NutsLog.ID DESC")
						->limit(5)
						->executeAndGetAll($debug_sql);

x($logs);

$job->dbClose();





?>