<?php

/* @var $plugin Plugin */

include('../plugins/_gallery/config.inc.php');

// erase with current gallery config
if(@$_GET['ID'] != 0)
{
    $_GET['NutsGalleryID'] = Query::factory()->select('NutsGalleryID')->from('NutsGalleryImage')->whereEqualTo('ID', $_GET['ID'])->executeAndGetOne();
}

$gallery_config = Query::factory()->select('*')->from('NutsGalleryImage')->whereEqualTo('NutsGalleryID', $_GET['NutsGalleryID'])->executeAndFetch();
if(!empty($gallery_config['ImageMaxWidth']))$gallery_images_allowed_max_width = $gallery_config['ImageMaxWidth'];
if(!empty($gallery_config['ImageMaxHeight']))$gallery_images_allowed_max_height = $gallery_config['ImageMaxHeight'];
if(!empty($gallery_config['ThumbnailWidth']))$gallery_images_allowed_thumbnail_width = $gallery_config['ThumbnailWidth'];
if(!empty($gallery_config['ThumbnailHeight']))$gallery_images_allowed_thumbnail_height = $gallery_config['ThumbnailHeight'];
if(!empty($gallery_config['ThumbnailConstraint']))$gallery_images_allowed_thumbnail_constraint = ($gallery_config['ThumbnailConstraint'] == 'YES') ? true : false;
if(!empty($gallery_config['ThumbnailBackgroundColor']))$gallery_images_allowed_thumbnail_background_color = explode(',', $gallery_config['ThumbnailBackgroundColor']);



// sql table
$plugin->formDBTable(array('NutsGalleryImage'));




/*$plugin->formAddField('Main', $lang_msg[7], 'image', false, array('path' => NUTS_IMAGES_PATH.'/gallery_images',
																  'url' => NUTS_IMAGES_URL.'/gallery_images', 
																  'size' => $gallery_images_allowed_max_size, 
																  'exts' => $gallery_images_allowed_exts, 
																  'mimes' => $gallery_images_allowed_mimes,

																  'parent_resize' => true,
																  'parent_width' => $gallery_images_allowed_max_width,
																  'parent_height' => $gallery_images_allowed_max_height,

																  'thumbnail_width' => $gallery_images_allowed_thumbnail_width,
																  'thumbnail_height' => $gallery_images_allowed_thumbnail_height,
																  'thumbnail_constraint' => $gallery_images_allowed_thumbnail_constraint,
																  'thumbnail_background_color' => $gallery_images_allowed_thumbnail_background_color,
																  'thumbnail_new' => true));*/

$plugin->formAddFieldImage('Main', $lang_msg[7], true,
														NUTS_IMAGES_PATH.'/gallery_images',
														NUTS_IMAGES_URL.'/gallery_images',
														$gallery_images_allowed_max_size, 
														$gallery_images_allowed_exts,
														$gallery_images_allowed_mimes,
														'',
														'', 
														true,
														$gallery_images_allowed_max_width,
														$gallery_images_allowed_max_height,
														'',
														'',
														true,
														$gallery_images_allowed_thumbnail_width,
														$gallery_images_allowed_thumbnail_height,
														$gallery_images_allowed_thumbnail_constraint,
														$gallery_images_allowed_thumbnail_background_color
							);



$plugin->formAddFieldText('Legend', $lang_msg[2], true, 'ucfirst');
$plugin->formAddFieldHtmlArea('Description', $lang_msg[3], false);
// $plugin->formAddField('Position', $lang_msg[5], 'text', 'notEmpty|onlyDigit', array('style' => 'width:3em; text-align:center'));
$plugin->formAddFieldBoolean('Active', $lang_msg[4], true);

/*
$plugin->formAddField('HD', $lang_msg[6], 'image', false, array('path' => NUTS_IMAGES_PATH.'/gallery_images_hd', 
																'url' => NUTS_IMAGES_URL.'/gallery_images_hd', 
																'size' => $gallery_images_hd_allowed_max_size, 
																'exts' => $gallery_images_hd_allowed_exts, 
																'mimes' => $gallery_images_hd_allowed_mimes,
																'thumbnail_width' => $gallery_images_hd_allowed_thumbnail_width,
																'thumbnail_height' => $gallery_images_hd_allowed_thumbnail_height,
																'thumbnail_constraint' => $gallery_images_hd_allowed_thumbnail_constraint,
																'thumbnail_background_color' => $gallery_images_hd_allowed_thumbnail_background_color,
																'thumbnail_new' => true));
 */
/*
$plugin->formAddFieldImage('HD', $lang_msg[6], false, 
														NUTS_IMAGES_PATH.'/gallery_images',
														NUTS_IMAGES_URL.'/gallery_images',
														$gallery_images_hd_allowed_max_size, 
														$gallery_images_hd_allowed_exts,
														$gallery_images_hd_allowed_mimes,
														'',
														'', 
														'',
														'',
														'',
														'',
														'',
														true,
														$gallery_images_hd_allowed_thumbnail_width,
														$gallery_images_hd_allowed_thumbnail_height,
														$gallery_images_hd_allowed_thumbnail_constraint,
														$gallery_images_hd_allowed_thumbnail_background_color
							);

*/



$plugin->formAddFieldHidden('NutsGalleryID', $lang_msg[1], true);
if($plugin->formModeIsAdding())
{
    $plugin->formActionAddParameter("NutsGalleryID={$_GET['NutsGalleryID']}");
    $plugin->formAddEndText("<script>$('#NutsGalleryID').val({$_GET['NutsGalleryID']});</script>");
}


if($_POST)
{
	// form assignation + get max position
	if($_GET['ID'] == 0)
	{
		$_POST['Position'] = $plugin->formGetMaxPosition('Position', 'NutsGalleryID', (int)$_POST['NutsGalleryID']);
		$_POST['Position'] += 1;
	}
}




?>