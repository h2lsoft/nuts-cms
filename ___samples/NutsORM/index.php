<?php
/**
 * NutsORM Component Sample - you must execute test.sql before
 */

// headers *************************************************************************
include("../../nuts/config.inc.php");
include(WEBSITE_PATH."/nuts/headers.inc.php");

// execution ***********************************************************************
$job = new NutsCore();
$job->dbConnect();

// verify Author table
$job->doQuery("SHOW TABLES LIKE 'Author'");
if(!$job->dbNumRows())die("Please execute first `test.sql`");

$job->doQuery("SHOW TABLES LIKE 'Book'");
if(!$job->dbNumRows())die("Please execute first `test.sql`");

NutsOrm::createClass('Author', true);
NutsOrm::createClass('Book', true);

$job->doQuery("TRUNCATE TABLE Author");
$job->doQuery("TRUNCATE TABLE Book");

// create Author & Book
$Author = new Author();
$Book = new Book();

$Author->Name = "Agatha Christies";
$ID = $Author->insert();
echo "Author #$ID created<hr>";

$Books = array();
$Book->AuthorID = $ID;
$Book->Name = "The Murder of Roger Ackroyd";
$Books[] = $Book->insert();

$Book->AuthorID = $ID;
$Book->Name = "Peril at End House";
$Books[] = $Book->insert();

$Book->AuthorID = $ID;
$Book->Name = "Murder on the Orient Express";
$Books[] = $Book->insert();
echo "Books created<hr>";

// get all author information
echo "Author information<br>";
$author_books = $Author->with('Book')->order_by('ID DESC')->get($ID);
x($author_books);

// update
$Author->Name = "Christies Agatha";
$Author->update($ID);
echo "Author #$ID updated<hr>";

// delete Author and Books
// $Author->delete($ID, array('Book'));
// echo "Author #$ID deleted<hr>";


$job->dbClose();



