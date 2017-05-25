<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// ajaxer **************************************************************************************************************
if(ajaxerRequested())
{
	// reload
	if(ajaxerAction('reload'))
	{
		Query::factory()->select('*')
						->from('NutsIMemo')
						->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])
						->order_by('Title')
						->execute();

		$opt = '';
		while($row = $nuts->dbFetch())
		{
			$note_title = ucfirst($row['Title']);
			if(empty($note_title))$note_title = 'Note';
			$opt .= "<option value='{$row['ID']}'>$note_title</option>\n";
		}

		die($opt);
	}

	// get
	if(ajaxerAction('get'))
	{
		ajaxerParameterRequired('ID', 'int');
		$text = Query::factory()->select('Text')
								->from('NutsIMemo')
								->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])
								->whereEqualTo('ID', $_GET['ID'])
								->executeAndGetOne();
		die($text);
	}

	// save
	if(ajaxerAction('save'))
	{
		ajaxerParameterRequired('ID', '', 'POST');
		$nuts->dbUpdate('NutsIMemo', array('Text' => $_POST['text']), "NutsUserID={$_SESSION['NutsUserID']} AND ID = {$_POST['ID']}");
	}

	// rename
	if(ajaxerAction('rename'))
	{
		ajaxerParameterRequired('ID', 'int');
		ajaxerParameterRequired('title');
		$nuts->dbUpdate('NutsIMemo', array('Title' => $_GET['title']), "NutsUserID={$_SESSION['NutsUserID']} AND ID = {$_GET['ID']}");
	}

	// add
	if(ajaxerAction('add'))
	{
		ajaxerParameterRequired('title');
		$CUR_ID = $nuts->dbInsert('NutsIMemo', array('NutsUserID' => $_SESSION['NutsUserID'], 'Title' => @$_GET['title'], 'Text' => ""), array(), true);
		die("$CUR_ID");
	}

	// delete
	if(ajaxerAction('delete'))
	{
		ajaxerParameterRequired('ID', 'int');
		$nuts->dbUpdate('NutsIMemo', array('Deleted' => 'YES'), "NutsUserID = {$_SESSION['NutsUserID']} AND ID = {$_GET['ID']}");
	}



	die();
}



// execution ***********************************************************************************************************
$nuts->open(PLUGIN_PATH.'/form.html');


$nuts->doQuery("SELECT Text FROM NutsIMemo WHERE NutsUserID = {$_SESSION['ID']}");
if(!$nuts->dbNumRows())
{
	$notes = "My notes";
	$nuts->dbInsert('NutsIMemo', array('NutsUserID' => $_SESSION['ID'], 'Text' => $notes));
}
else
{
	// $notes = $nuts->dbGetOne();
    $notes = '';
}

$nuts->parse('notes', $notes);
$plugin->render = $nuts->output();



