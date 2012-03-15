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
	if($_GET['ID'] == 0)
	{
		// get max position
		$_POST['Position'] = $plugin->formGetMaxPosition('Position');
		$_POST['Position'] += 1;
	}

	$nuts->alphaNumeric('Name', ' ');
}







?>