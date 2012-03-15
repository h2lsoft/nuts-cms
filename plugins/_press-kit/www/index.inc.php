<?php

/* @var $plugin Page */
/* @var $nuts Page */

// configuration ******************************************
include_once($plugin->plugin_path.'/config.inc.php');

$cur_cf = (isset($plugins_lng[$plugin->vars['Language']])) ? $plugins_lng[$plugin->vars['Language']] : $plugins_lng['en'];

// execution ******************************************
$plugin->openPluginTemplate();

$sql = "SELECT
				*,
				DATE_FORMAT(Date, '{$cur_cf['date_format']}') AS DateX
		FROM
				NutsPressKit
		WHERE
				Deleted = 'NO'
		ORDER BY
				Date DESC";
$plugin->doQuery($sql);
if(!$plugin->dbNumRows())
{
	$plugin->eraseBloc('press_kit');
}
else
{
	$plugin->eraseBloc('norecord');

	while($row = $plugin->dbFetch())
	{
		$plugin->parse('files.Source', $row['Source']);
		$plugin->parse('files.DateX', $row['DateX']);
		$plugin->parse('files.Title', $row['Title']);

		$file = getImageExtension($row['File']);
		$file = '<a href="'.$row['File'].'" target="_blank">'.$file.'</a>';
		$plugin->parse('files.File', $file);
	}

}


$output = $this->output();

// parsing language
$output = str_replace("{no_record_msg}", $cur_cf['no_record_msg'], $output);
$output = str_replace("[TH::Source]", $cur_cf['source'], $output);
$output = str_replace("[TH::Title]", $cur_cf['title'], $output);
$output = str_replace("[TH::Date]", $cur_cf['date'], $output);
$output = str_replace("[TH::File]", $cur_cf['file'], $output);



$plugin->setNutsContent($output);




?>