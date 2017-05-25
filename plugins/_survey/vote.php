<?php
/**
 * Script use for newsletter
 * action = affiate | unsuscribe | suscribe
 */

include_once("../../nuts/config.inc.php");
include(WEBSITE_PATH."/nuts/headers.inc.php");

// controller *************************************************************************
if(!$_POST)die('Error: no parameter found');
$_POST['ID'] = @(int)$_POST['ID'];
if($_POST['ID'] == 0)die('Error: parameter `ID` not found');

$_POST['OptionID'] = @(int)$_POST['OptionID'];
if($_POST['OptionID'] == 0)die('Error: parameter `OptionID` not found');

// execution *************************************************************************
$nuts = new NutsCore();
$nuts->dbConnect();
$IP = $nuts->getIP();
$IP_long = (float)ip2long($IP);

// verify user already votes
if($_POST['OptionID'] != -1)
{
	$sql = "SELECT IP FROM NutsSurveyData WHERE NutsSurveyID = {$_POST['ID']} AND IP = $IP_long LIMIT 1";
	$nuts->doQuery($sql);
	if($nuts->dbNumRows() == 0)
	{
		$nuts->dbInsert('NutsSurveyData', array(
												 'Date' => 'NOW()',
												 'NutsSurveyID' => $_POST['ID'],
												 'NutsSurveyOptionID' => $_POST['OptionID'],
												 'IP' => $IP_long));
	}
}

// generation of vote
$content = '';

$nuts->doQuery("SELECT COUNT(*) FROM NutsSurveyData WHERE NutsSurveyID = {$_POST['ID']}");
$total_count = (int)$nuts->dbGetOne();


$nuts->doQuery("SELECT
						ID,
						Title,
						I18N,
						(SELECT COUNT(*) FROM NutsSurveyData WHERE NutsSurveyID = {$_POST['ID']} AND NutsSurveyOptionID = NutsSurveyOption.ID) AS Votes
				FROM
						NutsSurveyOption
				WHERE
						Deleted = 'NO' AND
						NutsSurveyID = {$_POST['ID']}
				ORDER BY
						Position");

$content = '';
while($row = $nuts->dbFetch())
{
	$title = $row['Title'];
	if($row['I18N'] == 'YES')
		$title = "<i18n>$title</i18n>";

	$percent = @($row['Votes'] / $total_count) * 100;
	$percent = (int)$percent;
	$width = ($percent == 0) ? "0px" : "$percent%";
	
	$content .= '<label>'.$title.'<br /><span class="nuts_survey_bar_bkg"><span class="nuts_survey_bar" style="width:'.$width.';">'.$percent.'% ('.$row['Votes'].')</span></span></label>'."\n";
}

$content .= '<div class="nuts_survey_bottom">Total: '.$total_count.'</div>';
$content = '<div class="nuts_survey_results">'.$content.'</div>';


$nuts->dbClose();

die("ok@@@$content");

