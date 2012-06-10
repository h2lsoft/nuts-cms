<?php

/*@var $plugin Plugin */
/*@var $nuts NutsCore */

include(PLUGIN_PATH.'/form.inc.php');

if($plugin->formValid())
{
	$curGroupIDs = $_POST['NutsGroup'];
	$curUserIDs = array_unique($users);

	$_POST['NutsGroup'] = 0;
	$_POST['NutsUserID'] = 0;
	$_POST['Message'] = strip_tags($_POST['Message']);
    $_POST['Message'] = $nuts->clickable($_POST['Message']);


    // $CUR_ID = $plugin->formInsert();
	// $nuts->doQuery("DELETE FROM NutsIM WHERE ID = $CUR_ID");

	$curUserIDdone = array();

	// update by Users
	foreach($curUserIDs as $uID)
	{
		if(!in_array($uID, $curUserIDdone) && $uID != $_SESSION['ID'])
		{
			$fields = array(
								'NutsGroupID' => 0,
								'NutsUserID' => $uID,
								'Date' => 'NOW()',
								'NutsUserIDFrom' => $_SESSION['ID'],
								'Subject' => $_POST['Subject'],
								'Message' => $nuts->clickable(nl2br($_POST['Message'])),
								'Viewed' => 'NO');

			$nuts->dbInsert('NutsIM', $fields);
			$curUserIDdone[] = $uID;
		}
	}

	// update by Group
	if(count($curGroupIDs) > 0)
	{
		$sql = "SELECT ID, Email FROM NutsUser WHERE NutsGroupID IN(".join(',', $curGroupIDs).") AND Deleted = 'NO' AND ID != {$_SESSION['ID']}";
		$nuts->doQuery($sql);

		$cur_id = array();
		while($row = $nuts->dbFetch())
		{
			if(!in_array($row['ID'], $curUserIDdone))
			{
				$cur_id[] = $row['ID'];
				$curUserIDdone[] = $row['ID'];
			}
		}

		// insert
		foreach($cur_id as $cid)
		{
			$fields = array(
								'NutsGroupID' => 0,
								'NutsUserID' => $cid,
								'Date' => 'NOW()',
								'NutsUserIDFrom' => $_SESSION['ID'],
								'Subject' => $_POST['Subject'],
								'Message' => $nuts->clickable(nl2br($_POST['Message'])),
								'Viewed' => 'NO');

			$nuts->dbInsert('NutsIM', $fields);
		}
	}


	// send email for all
	if($_POST['EmailAlert'] == 'YES')
	{
		$nuts->mailCharset('UTF8');
		$nuts->mailFrom(NUTS_EMAIL_NO_REPLY);
		$nuts->mailSubject("[Private box] {$_POST['Subject']}");

		$body = nl2br($_POST['Message']);

		$nuts->doQuery("SELECT * FROM NutsUser WHERE ID = {$_SESSION['ID']}");
		$row = $nuts->dbFetch();
		$body = "<b>{$row['FirstName']} {$row['LastName']} ({$row['Login']}) {$lang_msg[16]}</b><br/><br />".$body;

		include_once(WEBSITE_PATH."/plugins/_email/config.inc.php");
		$body = str_replace('[BODY]', $body, $HTML_TEMPLATE);

		$nuts->mailBody($body, 'HTML');

		// send to all
		foreach($curUserIDdone as $cur)
		{
			$sql = "SELECT Email FROM NutsUser WHERE ID = $cur";
			$nuts->doQuery($sql);
			$to = $nuts->dbGetOne();
			$nuts->mailTo($to);
			$nuts->mailSend();
		}
	}

	// add copy message for author
	$fields = array(
								'NutsGroupID' => 0,
								'NutsUserID' => $_SESSION['ID'],
								'Date' => 'NOW()',
								'NutsUserIDFrom' => $_SESSION['ID'],
								'Subject' => $_POST['Subject'],
								'Message' => $nuts->clickable(nl2br($_POST['Message'])),
								'Viewed' => 'YES',
								'DateViewed' => 'NOW()');

	$nuts->dbInsert('NutsIM', $fields);



}


?>