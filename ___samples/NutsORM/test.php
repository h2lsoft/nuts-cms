<?php
/**
 * This examples show you a select example
 * of CRUD with has many
 * 
 * Author has many Book
 * 
 */

// headers *************************************************************************
include("../../nuts/config.inc.php");
include("../../nuts/headers.inc.php");

// execution *************************************************************************

// include class
include("Author.class.php");
include("Book.class.php");

$job = new NutsCore();
$job->dbConnect();

$author = NutsORM::factory('Author');

// create ann author




$job->dbClose();




?>