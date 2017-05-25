<?php

/* @var $plugin Page */
/* @var $nuts Page */

// configuration ******************************************
include_once($plugin->plugin_path.'/config.inc.php');

$cur_cf = (isset($plugins_lng[$plugin->vars['Language']])) ? $plugins_lng[$plugin->vars['Language']] : $plugins_lng['en'];
$sql_added = '';
if($plugin->pluginHasParameter())
{
	$category = $plugin->getPluginParameter(0);
	if(!empty($category))
		$sql_added = " AND Category = '".sqlX($category)."'";
}

// execution ***********************************************************************************************************
$plugin->openPluginTemplate();

$sql = "SELECT
				*,
				DATE_FORMAT(Date, '{$cur_cf['date_format']}') AS DateX
		FROM
				NutsPressKit
		WHERE
				Deleted = 'NO'
				$sql_added
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

        $size = getFileSize(WEBSITE_PATH.$row['File']);
        $plugin->parse('files.Size', $size);

		$file = getImageExtension($row['File']);
		$file = '<a href="'.$row['File'].'" target="_blank">'.$file.'</a>';
		$plugin->parse('files.File', $file);

        $plugin->loop('files');
    }

}


$output = $this->output();

// parsing language
$output = str_replace("{no_record_msg}", $cur_cf['no_record_msg'], $output);
$output = str_replace("[TH::Source]", $cur_cf['source'], $output);
$output = str_replace("[TH::Title]", $cur_cf['title'], $output);
$output = str_replace("[TH::Date]", $cur_cf['date'], $output);
$output = str_replace("[TH::Size]", $cur_cf['size'], $output);
$output = str_replace("[TH::File]", $cur_cf['file'], $output);



if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_press-kit/style.css');
$plugin->setNutsContent($output);


