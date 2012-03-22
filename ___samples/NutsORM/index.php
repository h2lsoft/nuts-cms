<?php
/**
 * NutsORM Component Sample - you must execute test.sql before
 */

// headers *************************************************************************
include("../../nuts/config.inc.php");
include("../../nuts/headers.inc.php");

// execution *************************************************************************
$job = new NutsCore();
$job->dbConnect();

$job->doQuery("SHOW TABLES LIKE 'Author'");
if(!$job->dbNumRows())
{
	die("Please execute first `test.sql`");
}

// initialize
$job->doQuery("TRUNCATE TABLE Author");

// create an author
$author = NutsORM::factory('Author');
$author->Name = "Walter Isaacson";
$author->save();
echo "Author `{$author->Name}` created with ID # {$author->ID}<br />";

// update
$author->Name = "Walter Isaacson UPDATED";
$author->save();
echo "Author ID #{$author->ID} updated name: {$author->Name} <br />";

// deleted
$author->delete();
echo "Author  deleted<br />";

// recreation
$author->Name = 'Agatha cristies';
$author->save(); # force creation
echo "New Author `{$author->Name}` creation with ID {$author->ID}<br>";

// new author
$author->Name = 'Suzanne Collins';
$author->save(true); # force creation
echo "New Author `{$author->Name}` creation with ID {$author->ID}<br>";



$job->dbClose();



?>