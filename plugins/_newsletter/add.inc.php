<?php

/*@var $plugin Plugin */
/*@var $nuts NutsCore */


include(PLUGIN_PATH.'/form.inc.php');

// hack percent treatment
if($plugin->formPercentRenderExecute())
{
	$_POST = $plugin->formPercentRenderGetPost();
	include_once(PLUGIN_PATH."/trt_treatment.inc.php");
}

if($plugin->formValid())
{
	// update form value end
	$mailinglistIDs = join(',', $_POST['MailingList']);
	for($i=0; $i < count($mailinglistIDs); $i++)
		$mailinglistIDs[$i] = (int)$mailinglistIDs[$i];

	// get total value
	$sql = "SELECT ID, Email FROM NutsNewsletterMailingListSuscriber WHERE NutsNewsletterMailingListID IN($mailinglistIDs) AND Deleted = 'NO' GROUP BY Email";
	$nuts->doQuery($sql);
	$plugin->formPercentRenderEndValue = $nuts->dbNumRows();


	$CUR_ID = $plugin->formInsert();

}


?>