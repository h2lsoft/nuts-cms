<?php
/**
 * Image
 * @package Functions
 * @version 1.0
 */

/**
 * Return image extension for a file
 *
 * @param string $file
 * @return string image
 */
function getImageExtension($file)
{
	$file_name = basename($file);
	$exts = explode('.', $file_name);
	$ext = strtolower(end($exts));
	$ext = trim($ext);
	$ext2 = $ext;

	if(strlen($ext) >= 4)$ext2 = substr($ext, 0 , 3);

	$img = '<img src="/nuts/img/icon_extension/file.png" align="absmiddle" />';

	if(file_exists(WEBSITE_PATH."/nuts/img/icon_extension/{$ext}.png"))
		$img = "<img src=\"/nuts/img/icon_extension/{$ext}.png\" align=\"absmiddle\" />";
	elseif(file_exists(WEBSITE_PATH."/nuts/img/icon_extension/{$ext2}.png"))
		$img = "<img src=\"/nuts/img/icon_extension/{$ext2}.png\" align=\"absmiddle\" />";

	/*
	// doc
	if(in_array($ext, array('doc', 'docx', 'odt')))
	{
		$img = '<img src="/nuts/img/icon_extension/doc.png" align="absmiddle" />';
	}
	// excel
	elseif(in_array($ext, array('xls', 'xlsx', 'csv')))
	{
		$img = '<img src="/nuts/img/icon_extension/excel.png" align="absmiddle" />';
	}
	// pdf
	elseif(in_array($ext, array('pdf')))
	{
		$img = '<img src="/nuts/img/icon_extension/pdf.png" align="absmiddle" />';
	}
	// zip
	elseif(in_array($ext, array('zip')))
	{
		$img = '<img src="/nuts/img/icon_extension/zip.png" align="absmiddle" />';
	}
	// jpg, jpeg, gif, bmp, tiff, png, psd
	elseif(in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'tiff', 'psd')))
	{
		$img = '<img src="/nuts/img/icon_extension/image.png" align="absmiddle" />';
	}*/

	return $img;
}

/**
 * Generate thumbnail image dynamically from width and height attributes
 *
 * @param string $content
 * @return string content reformatted src image
 */
function smartImageResizer($content)
{
	$pattern = '/<img[^>]+>/i';
	preg_match_all($pattern, $content, $matches);

	$matches[0] = array_unique($matches[0]);

	foreach($matches[0] as $match)
	{
		if(strstr($match, 'src="/library/media/') !== false && strstr($match, 'height="') !== false && strstr($match, 'width="') !== false)
		{
			$tab = array();

			$tmp =  explode('src="', $match);
			$tmp =  explode('"', $tmp[1]);
			$tab['src'] = $tmp[0];

			$tmp =  explode('width="', $match);
			$tab['width'] = (int)$tmp[1];

			$tmp =  explode('height="', $match);
			$tab['height'] = (int)$tmp[1];

			// see original width and height
			list($original_width, $original_height, )  = @getimagesize(WEBSITE_PATH.$tab['src']);

			// see alreday defined
			$file_infos = @pathinfo($tab['src']);

			$tab['path'] = $file_infos['dirname'];
			$tab['file_name'] = $file_infos['basename'];
			$tab['extension'] = $file_infos['extension'];
			$tab['file_noext'] = $file_infos['filename'];
			$tab['file_thumbnail'] = $tab['file_noext']."-{$tab['width']}x{$tab['height']}.{$file_infos['extension']}";
			$thumb_full = WEBSITE_PATH.$tab['path'].'/'.$tab['file_thumbnail'];

			if($tab['width'] && $tab['width'] < $original_width && $tab['height'] && $tab['height'] < $original_height && isset($file_infos['filename']) && isset($file_infos['extension']) && in_array(strtolower($file_infos['extension']), array('jpg', 'png', 'gif')))
			{
				if(!file_exists($thumb_full))
				{
					createThumb(WEBSITE_PATH.$tab['src'], $tab['width'], true, $tab['height'], "", "-{$tab['width']}x{$tab['height']}");
				}
			}

			// at end force change content if user do not close window
			if($tab['width'] && $tab['height'] && isset($file_infos['filename']) && isset($file_infos['extension']) && in_array(strtolower($file_infos['extension']), array('jpg', 'png', 'gif')))
			{
				if(file_exists($thumb_full))
				{
					$imgX = $match;
					$imgX = str_replace("src=\"{$tab['src']}\"", "src=\"{$tab['path']}/{$tab['file_thumbnail']}\"", $imgX);
					$imgX = str_replace("width=\"{$tab['width']}\"", " ", $imgX);
					$imgX = str_replace("height=\"{$tab['height']}\"", " ", $imgX);
					$content = str_replace($match, $imgX, $content);
				}
			}
		}
	}

	return $content;
}

/**
 * Create thumbnail image
 *
 * @param string $fname
 * @param int $thumbWidth max width to resize
 * @param bool $create_new create new one with $create_new_prefix and $create_new_suffix
 * @param int $create_new_height force height (by default 0 = generated)
 * @param string $create_new_prefix prefix for image (thumb_ by default)
 * @param string $create_new_suffix suffix for image (empty by default)
 * @return boolean
 */
function createThumb($fname, $thumbWidth, $create_new = false, $create_new_height = 0, $create_new_prefix = "thumb_", $create_new_suffix = "")
{
	$tmp = explode('/', $fname);
	$file = $tmp[count($tmp)-1];

	$info = pathinfo($fname);
	$ext = strtolower($info['extension']);

	if($ext == 'jpg' || $ext == 'jpeg')$img = imagecreatefromjpeg($fname);
	elseif($ext == 'png')$img = imagecreatefrompng($fname);
	elseif($ext == 'gif')$img = imagecreatefromgif($fname);

	$width = imagesx($img);
	$height = imagesy($img);

	// calculate thumbnail size
	$new_width = $thumbWidth;

	if(!$create_new_height)
		$new_height = floor($height * ($thumbWidth / $width));
	else
		$new_height = $create_new_height;

	$tmp_img = @imagecreatetruecolor($new_width, $new_height);

	// preserve transparency
	if($ext == 'png' || $ext == 'gif')
	{
		@imagealphablending($tmp_img, false);
		@imagesavealpha($tmp_img, true);
	}

	//imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	// create new thumb
	if($create_new)
	{
		$fname = str_replace(basename($fname), $create_new_prefix.str_replace(".$ext", "", $file).$create_new_suffix.'.'.$ext, $fname);
	}

	if($ext == 'jpg' || $ext == 'jpeg')return imagejpeg($tmp_img, $fname, 100);
	elseif($ext == 'png')return imagepng($tmp_img, $fname);
	elseif($ext == 'gif')return imagegif($tmp_img, $fname);

	return false;
}



/**
 * Check base64 image string (useful for js canvas dataUri)
 *
 * @param string $base64Img
 * @param array $mime_allowed (default 'image/png', 'image/jpg', 'image/jpeg', 'image/gif')
 *
 * @return bool
 */
function isBase64Image($base64Img, $mime_allowed=array('image/png', 'image/jpg', 'image/jpeg', 'image/gif'))
{
	// check image
	$img_info = @getimagesize($base64Img);
	if(!$img_info || !isset($img_info['mime']))return false;

	// check mime
	if(!in_array($img_info['mime'], $mime_allowed))
		return false;

	return true;
}
