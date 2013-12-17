<?php
/**
 * File
 * @package Functions
 * @version 1.0
 */


/**
 * Return filesize in Mo
 *
 * @param string $file_path
 * @param string $suffix (` Mo` by default)
 * @return string
 */
function getFileSize($file_path, $suffix=' Mo')
{
	$size = @filesize($file_path);
	$size = bcdiv($size, 1048576, 2);
	if($size == 0)$size = 0.1;
	$size = number_formatX($size);
	$size_label = $size.' '.$suffix;
	$size_label = trim($size_label);

	return $size_label;

}

/**
 * Change contents for a special line for configuration file by example
 *
 * @param string $file
 * @param string $line_start
 * @param string $replacement
 * @return boolean
 */
function fileChangeLineContents($file, $line_start, $replacement)
{
	$file_contents = file_get_contents($file);
	if(!$file_contents)return false;

	$found = false;
	$lines = explode("\n", $file_contents);
	$i = 0;
	foreach($lines as $line)
	{
		$tmp_line = trim($line);
		if(strpos($tmp_line, $line_start) !== false && strpos($tmp_line, $line_start) == 0)
		{
			$lines[$i] = $replacement;
			$found = true;
			break;
		}
		$i++;
	}

	$new_file = join("\n", $lines);
	$new_file = trim($new_file);

	// save file
	if(!file_put_contents($file, $new_file))
		return false;

	return $found;
}

/**
 * Log event in file
 *
 * @param string $msg
 * @param int $level
 * @param string $file if empty use trace.log
 */
function xLog($msg, $level=0, $file="")
{
	if(empty($file))
		$file = 'trace.log';

	$contents = @file_get_contents($file);
	if(!empty($contents))
		$contents .= "\n";

	$spaces = str_repeat("\t", $level);

	$contents .= "[".date('Y-m-d H:i:s')."]\t$spaces".ucfirst(trim($msg));
	file_put_contents($file, $contents);
}
