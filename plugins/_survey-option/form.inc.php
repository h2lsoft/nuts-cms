<?php

/* @var $plugin Plugin */
$multiLanguage = isWebsiteMultiLang();

$plugin->formDBTable(array('NutsSurveyOption'));

if($_GET['ID'] == 0)
	$plugin->formActionAddParameter("NutsSurveyID={$_GET['NutsSurveyID']}");

$plugin->formAddFieldText('Title', $lang_msg[1], true);

if($multiLanguage)
	$plugin->formAddFieldBooleanX('I18N', '', false);


if($_GET['ID'] != 0)
{
	$plugin->formAddFieldHidden('NutsSurveyID', "", true);
}


if($_POST)
{
	// form assignation
	if($_GET['ID'] == 0)
	{
		// get max position
		$_POST['NutsSurveyID'] = $_GET['NutsSurveyID'];
		$_POST['Position'] = $plugin->formGetMaxPosition('Position', 'NutsSurveyID', $_POST['NutsSurveyID']);
		$_POST['Position'] += 1;
	}
}



