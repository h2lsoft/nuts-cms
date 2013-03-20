<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

if(@$_GET['ajaxer'] == 1)
{
    // reload
    if(@$_GET['action'] == 'reload')
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
    if(@$_GET['action'] == 'get')
    {
        $text = Query::factory()->select('Text')
                                ->from('NutsIMemo')
                                ->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])
                                ->whereEqualTo('ID', @$_GET['ID'])
                                ->executeAndGetOne();
        die($text);

    }

    // save
    if(@$_GET['action'] == 'save')
    {
        $nuts->dbUpdate('NutsIMemo', array('Text' => $_POST['text']), "NutsUserID={$_SESSION['ID']} AND ID = {$_POST['ID']}");
    }

    // rename
    if(@$_GET['action'] == 'rename')
    {
        $nuts->dbUpdate('NutsIMemo', array('Title' => $_GET['title']), "NutsUserID={$_SESSION['ID']} AND ID = {$_GET['ID']}");
    }

    // add
    if(@$_GET['action'] == 'add')
    {
        $CUR_ID = $nuts->dbInsert('NutsIMemo', array('NutsUserID' => $_SESSION['ID'], 'Title' => @$_GET['title'], 'Text' => ""), array(), true);
        die("$CUR_ID");
    }

    // delete
    if(@$_GET['action'] == 'delete')
    {
        $nuts->dbUpdate('NutsIMemo', array('Deleted' => 'YES'), "NutsUserID = {$_SESSION['ID']} AND ID = {$_GET['ID']}");
    }



    die();
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
	// $notes = $nuts->dbGetOne();
    $notes = '';
}

$nuts->parse('notes', $notes);
$plugin->render = $nuts->output();



?>