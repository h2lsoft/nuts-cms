<?php

/* @var $nuts NutsCore */
/* @var $plugin Plugin */

include(PLUGIN_PATH.'/config.inc.php');

// sql table
$plugin->formDBTable(array('NutsGallery'));

/*
$plugin->formAddField('Logo', '', 'image', false, array('path' => NUTS_IMAGES_PATH.'/gallery',
														'url' => NUTS_IMAGES_URL.'/gallery',
														'size' => $gallery_allowed_max_size,
														'exts' => $gallery_allowed_exts,
														'mimes' => $gallery_allowed_mimes,

														'thumbnail_width' => $gallery_allowed_thumbnail_width,
														'thumbnail_height' => $gallery_allowed_thumbnail_height,
														'thumbnail_constraint' => $gallery_allowed_thumbnail_constraint,
														'thumbnail_background_color' => $gallery_allowed_thumbnail_background_color,

														'thumbnail_new' => true));
*/

$plugin->formAddFieldImage('Logo', '', false,
							NUTS_IMAGES_PATH.'/gallery',
							NUTS_IMAGES_URL.'/gallery',
							$gallery_allowed_max_size,
							$gallery_allowed_exts,
							$gallery_allowed_mimes,
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							true,
							$gallery_allowed_thumbnail_width,
							$gallery_allowed_thumbnail_height,
							$gallery_allowed_thumbnail_constraint,
							$gallery_allowed_thumbnail_background_color);


$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldHtmlArea('Description', $lang_msg[2], false);
// $plugin->formAddField('Position', $lang_msg[5], 'text', 'notEmpty|onlyDigit', array('style' => 'width:3em; text-align:center'));
$plugin->formAddFieldBoolean('GenerateJS', $lang_msg[8], true, $lang_msg[9]);
$plugin->formAddFieldBoolean('Active', $lang_msg[4], true);

// option for gallery
$plugin->formAddFieldsetStart("Options");
$plugin->formAddFieldText("ImageMaxWidth", $lang_msg[10], true, 'number', '', 'px', '', $lang_msg[11], $gallery_images_allowed_max_width);
$plugin->formAddFieldText("ImageMaxHeight", $lang_msg[12], true, 'number', '', 'px', '', $lang_msg[13], $gallery_images_allowed_max_height);
$plugin->formAddFieldText("ThumbnailWidth", $lang_msg[14], true, 'number', '', 'px', '', $lang_msg[15], $gallery_images_allowed_thumbnail_width);
$plugin->formAddFieldText("ThumbnailHeight", $lang_msg[16], true, 'number', '', 'px', '', $lang_msg[17], $gallery_images_allowed_thumbnail_height);
$plugin->formAddFieldBoolean("ThumbnailConstraint", $lang_msg[18], true, $lang_msg[19]);
$plugin->formAddFieldText("ThumbnailBackgroundColor", $lang_msg[20], false, '', 'width:120px; text-align:center', '', '', $lang_msg[21], join(', ', $gallery_images_allowed_thumbnail_background_color));
$plugin->formAddFieldsetEnd();



// add an upload multiple gallery
//if($_GET['ID'] == 0)
//{
	$plugin->formAddFieldsetStart("Uploader", "Multiple Uploader");
	$plugin->formAddFieldsetEnd();
//}

$plugin->formAddException('file');



if($_POST)
{
	// form assignation
	if($plugin->formModeIsAdding())
	{
		// get max position
		$_POST['Position'] = $plugin->formGetMaxPosition('Position');
		$_POST['Position'] += 1;
	}

	$nuts->alphaNumeric('Name', ' ');
}







?>