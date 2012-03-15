<?php

/* @var $nuts NutsCore */
/* @var $plugin Plugin */
$_GET['ID'] = (int)@$_GET['ID'];


$colors = "FF0000|00FF00|0000FF|CC0000|00CC00|0000CC|EE0000|00EE00|0000EE";

$sql = "SELECT Title FROM NutsSurvey WHERE ID = {$_GET['ID']} AND Deleted = 'NO'";
$nuts->doQuery($sql);
if($nuts->dbNumRows() == 0)
{
	$output = "Error: chart not found";
}
else
{
	$chart_title = $nuts->dbGetOne();

	// get all options + results
	$nuts->doQuery("SELECT
							Title,
							(SELECT COUNT(*) FROM NutsSurveyData WHERE NutsSurveyID = {$_GET['ID']} AND NutsSurveyOptionID = NutsSurveyOption.ID) AS Votes
					FROM
							NutsSurveyOption
					WHERE
							Deleted = 'NO' AND
							NutsSurveyID = {$_GET['ID']}
					ORDER BY
							Position");

	$titles1 = array();
	$titles = array();
	$votes = array();
	$total = 0;
	while($row = $nuts->dbFetch())
	{
		$titles1[] = $row['Title'].' ('.$row['Votes'].')';
		$titles[] = $row['Title'];
		$votes[] = $row['Votes'];
		$total += $row['Votes'];
	}
	$chart_title .= " (Total: $total)";


   	$output = '<center><img src="http://chart.apis.google.com/chart?chtt='.urlencode($chart_title).'&chts=000000,18&chco='.$colors.'&cht=p3&chd=t:'.join(',',$votes).'&chs=850x350&chl='.join('|',$titles1).'&chdl='.join('|',$titles).'" /></center>';
}

die($output);




?>