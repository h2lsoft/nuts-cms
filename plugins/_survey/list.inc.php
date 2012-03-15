<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsSurvey', "
										(SELECT COUNT(*) FROM NutsSurveyData WHERE NutsSurveyID = NutsSurvey.ID) AS Votes,
										(SELECT COUNT(*) FROM NutsSurveyOption WHERE NutsSurveyID = NutsSurvey.ID AND Deleted = 'NO') AS Choices
										
									");

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldText('Title', $lang_msg[1]);

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Title', $lang_msg[1], '', true);
$plugin->listAddCol('Choices', $lang_msg[3], 'center; width:30px; white-space:nowrap;', false);
$plugin->listAddCol('Votes', $lang_msg[2], 'center; width:30px; white-space:nowrap;', true);

// render list
$plugin->listRender(20, 'hookData');


function hookData($row)
{
	global $lang_msg;

	$row['Choices'] = <<<EOF
		<img src="img/widget.png" align="absbottom" style="width:16px;" />
		<a href="javascript:popupModal('/nuts/?mod=_survey-option&do=list&popup=1&NutsSurveyID={$row['ID']}&NutsSurveyID_operator=_equal_&user_se=1');"> {$row['Choices']}</a>
EOF;

	$title = str_replace("'", "\'", $row['Title']);
	$uri = "formIt('$title', '?mod=_survey&do=reporting&ID={$row['ID']}');";


	$row['Votes'] = '<img src="img/icon-user.gif" align="absbottom" /> <a href="javascript:'.$uri.'" title="'.$lang_msg[4].'" class="tt">'.$row['Votes']."</a>";
	
	return $row;
}



?>