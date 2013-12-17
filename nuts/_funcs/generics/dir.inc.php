<?php
/**
 * Dir
 * @package Functions
 * @version 1.0
 */


/**
 * Recursive delete whole directory and files and directory itself
 *
 * @param $path without slashes at end
 */
function rm_r($path)
{
	foreach(glob($path . '/*') as $file) {
		if(is_dir($file))
			rm_r($file);
		else
			unlink($file);
	}

	rmdir($path);
}
