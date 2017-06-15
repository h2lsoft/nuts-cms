<?php

foreach($BATCH_EMAILS as $BATCH_EMAIL)
{
	$f = [];
	$f['NutsNewsletterMailingListID'] = $_POST['NutsNewsletterMailingListID'];
	$f['Language'] = $_POST['Language'];
	$f['Date'] = $_POST['Date'];
	
	$f['Email'] = $BATCH_EMAIL['Email'];
	$f['LastName'] = $BATCH_EMAIL['LastName'];
	$f['FirstName'] = $BATCH_EMAIL['FirstName'];
	
	$nuts->dbInsert('NutsNewsletterMailingListSuscriber', $f);
	
}


