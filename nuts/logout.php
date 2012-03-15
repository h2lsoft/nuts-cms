<?php

// configuration *********************************************************************
include_once("config.inc.php");
include_once("headers.inc.php");

$nuts = new NutsCore();
$nuts->dbConnect();
include_once("_inc/session.inc.php");

$NutsUserLang = strtolower($_SESSION['Language']);
if(!file_exists('lang/'.$NutsUserLang.'.inc.php'))
	include('lang/en.inc.php');
else
	include('lang/'.$NutsUserLang.'.inc.php');

// execution *************************************************************************

$nuts->open("_templates/logout.html");
$nuts->write();







?>