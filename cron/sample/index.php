<?php
/**
 * Sample Jobs - this is a sample job
 *
 * @version 1.0
 * @author XXX
 * @date xx/xx/xx
 *
 */

// headers *************************************************************************
set_time_limit(0);
header("Content-Type: text/plain");

// includes *************************************************************************
include("../../nuts/config.inc.php");
include("../../nuts/headers.inc.php");

// configuration *************************************************************************
$job = new NutsCore();
$nuts = &$job;

// execution *************************************************************************
$job->dbConnect();



$job->dbClose();

