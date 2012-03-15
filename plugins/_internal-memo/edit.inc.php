<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// ajax action
if($_POST)
{
	$nuts->dbUpdate('NutsIMemo', array('Text' => $_POST['text']), "NutsUserID={$_SESSION['ID']}");
}


$nuts->open(PLUGIN_PATH.'/form.html');


$nuts->doQuery("SELECT Text FROM NutsIMemo WHERE NutsUserID = {$_SESSION['ID']}");
if(!$nuts->dbNumRows())
{
	$notes = "My notes";
	$nuts->dbInsert('NutsIMemo', array('NutsUserID' => $_SESSION['ID'], 'Text' => $notes));
}
else
{
	$notes = $nuts->dbGetOne();
}

$nuts->parse('notes', $notes);
$plugin->render = $nuts->output();



?>