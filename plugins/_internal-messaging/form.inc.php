<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// sql table PLUGIN_PATH
// include(PLUGIN_PATH."/config.inc.php");

// mark read
$subject = '';
if(isset($_GET['replyID']))
{
	$_GET['replyID'] = (int)$_GET['replyID'];
	$nuts->dbUpdate('NutsIM', array('Viewed' => 'YES'), "ID={$_GET['replyID']}");
}

$plugin->formDBTable(array('NutsIM')); // put table here

// fields
$plugin->formAddFieldsetStart('To', $lang_msg[11]);

$nuts->doQuery("SELECT ID AS value, CONCAT(Name,' (',(SELECT COUNT(*) FROM NutsUser WHERE Deleted = 'NO' AND NutsGroupID = NutsGroup.ID),')' ) AS label FROM NutsGroup WHERE Deleted = 'NO' AND BackofficeAccess = 'YES' ORDER BY Priority, Name");
$nuts_groups = $nuts->dbGetData();

// $plugin->formAddField('NutsGroup[]', $lang_msg[8], 'select', false, array( "attributes" => 'multiple="" size="5"', 'options' => $nuts_groups, 'help' => $lang_msg[9], 'class' => 'checkbox-list'));
$plugin->formAddFieldSelectMultiple('NutsGroup[]', $lang_msg[8], false, $nuts_groups, '', '', 'multiple="" size="5"', true, $lang_msg[9]);

$replyX = (isset($_GET['replyTo'])) ? urldecode($_GET['replyTo']) : '';
// $plugin->formAddField('NutsUserIDFrom', $lang_msg[10], 'textarea', false, array('style' => "height:60px", 'value' => $replyX));
$plugin->formAddFieldTextArea('NutsUserIDFrom', $lang_msg[10], false, '', "height:60px", '', '', $replyX);
$plugin->formAddFieldsetEnd();

$reply = (isset($_GET['reply'])) ? 'Re : '.$_GET['reply'] : '';
$plugin->formAddFieldText('Subject', $lang_msg[1], true, 'ucfirst', '', '', '', '', $reply);
$plugin->formAddFieldTextArea('Message', $lang_msg[2], true, 'ucfirst', "height:380px", "", $lang_msg[20]);
$plugin->formAddFieldBoolean('EmailAlert', $lang_msg[17], true, $lang_msg[18]);


// rules exception
if($_POST)
{
	if(!isset($_POST['NutsGroup']))$_POST['NutsGroup'] = array();

	// verify group or user
	$user_tmp = explode('; ', $_POST['NutsUserIDFrom']);
	$users = array();
	foreach($user_tmp as $u)
	{
		$u = trim($u);
		$uID = explode(' (#', $u);
		$uID = (int)@$uID[1];
		if($uID > 0)
			$users[] = $uID;
	}

	if(count($_POST['NutsGroup']) == 0 && count($users) == 0)
	{
		$nuts->addError('NutsUserIDFrom', $lang_msg[12]);
	}

}

