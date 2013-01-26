<?php

/* @var $plugin Page */
/* @var $nuts Page */


// replace current option value for gallery
$gallery_images_allowed_max_width = $_POST['ImageMaxWidth'];
$gallery_images_allowed_max_height = $_POST['ImageMaxHeight'];
$gallery_images_allowed_thumbnail_width = $_POST['ThumbnailWidth'];
$gallery_images_allowed_thumbnail_height = $_POST['ThumbnailHeight'];
$gallery_images_allowed_thumbnail_constraint = $_POST['ThumbnailConstraint'];
$gallery_images_allowed_thumbnail_background_color = @explode(',', $_POST['ThumbnailBackgroundColor']);


// check multiple user file and create thumb image
$tmp_files = (array)glob(PLUGIN_PATH.'/_tmp/'.$_SESSION['ID'].'_*');
$allowed_ext = explode(',', str_replace(' ', '', $gallery_images_allowed_exts));

$max_position = 1;
foreach($tmp_files as $tmp_file)
{
	$tmp_file_info = pathinfo($tmp_file);
	$tmp_file_info['extension'] = trim(strtolower($tmp_file_info['extension']));

	// prevent error
	if(!in_array($tmp_file_info['extension'], $allowed_ext))
	{
		@unlink($tmp_file);
	}
	else
	{
		// copy file, unlink file, create thumbnail, insert record
		$new_file = str_replace("/{$_SESSION['ID']}_", '/', $tmp_file);
		$new_file = basename($new_file);

		if(@rename($tmp_file, NUTS_GALLERY_IMAGES_PATH.'/'.$new_file))
		{
			@unlink($tmp_file);
			@copy(NUTS_GALLERY_IMAGES_PATH.'/'.$new_file, NUTS_GALLERY_IMAGES_PATH.'/thumb_'.$new_file);
			sleep(1);

			// resize parent
			$nuts->imgThumbnailSetOriginal(NUTS_GALLERY_IMAGES_PATH.'/'.$new_file);
			$nuts->imgThumbnail($gallery_images_allowed_max_width,
								$gallery_images_allowed_max_height);


			// create thumb
			$nuts->imgThumbnailSetOriginal(NUTS_GALLERY_IMAGES_PATH.'/thumb_'.$new_file);

			$nuts->imgThumbnail($gallery_images_allowed_thumbnail_width,
								$gallery_images_allowed_thumbnail_height,
								$gallery_images_allowed_thumbnail_constraint,
								$gallery_images_allowed_thumbnail_background_color);


			$legend = str_replace(array('-','_'), ' ', strtolower($new_file));
			$legend = str_replace('.'.$tmp_file_info['extension'], '', $legend);
			$legend = ucfirst(strtolower(trim($legend)));

			$fields = array(
								'NutsGalleryID' => $CID,
								'Legend' => $legend,
								'Active' => 'YES',
								'Position' => $max_position
							);
			$recID = $nuts->dbInsert('NutsGalleryImage', $fields, array(), true);

			// rename thumb and original
			@rename(NUTS_GALLERY_IMAGES_PATH.'/'.$new_file, NUTS_GALLERY_IMAGES_PATH.'/'.$recID.'.'.$tmp_file_info['extension']);
			@rename(NUTS_GALLERY_IMAGES_PATH.'/thumb_'.$new_file, NUTS_GALLERY_IMAGES_PATH.'/thumb_'.$recID.'.'.$tmp_file_info['extension']);
			$nuts->dbUpdate('NutsGalleryImage', array('MainImage' => $recID.'.'.$tmp_file_info['extension']), "ID = $recID");

			$max_position++;
		}
	}
}






?>