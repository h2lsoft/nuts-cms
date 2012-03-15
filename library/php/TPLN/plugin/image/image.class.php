<?php

/**
 * TPLN Image Plugin
 * @package Template Engine 
 */
class Image
{
	public $new_name;
	public $path;
	protected $obj;

	/**
	 * Upload file to directory, if name is not empty rename file (no extension needed)
	 *
	 * @param string $obj
	 * @param string $path
	 * @param string $new_name
	 * @return boolean
	 */
	public function fileUpload($obj, $path, $new_name='')
	{
		$this->path = $path;
		$this->obj = $obj;
		//if there is not new name we keep the same origin name
		if(empty($new_name))
		{
			$name = $_FILES[$obj]['name'];
			$path .= "/$name";
			$this->new_name = $name;
		}
		else
		{
			$extent = pathinfo($_FILES[$obj]['name']);
			$extent = $extent['extension'];
			$name = $new_name.".$extent";
			$this->new_name = $name;
			$path .= "/$name";
		}

		if(!move_uploaded_file($_FILES[$obj]['tmp_name'], $path))
			return false;

		return true;
	}


	/**
	 * Assign image to thumbnail file
	 *
	 * @param string path full path with image name
	 */
	public function imgThumbnailSetOriginal($path)
	{
		$this->new_name = basename($path);
		$this->path = str_replace('/'.$this->new_name, '', $path);
	}



	/**
	 * Transform image to thumbnail file
	 *
	 * @param int $width image width
	 * @param int $height image height
	 * @param boolean $contraint
	 * @param array $background_color 3 colors RGB
	 * @param string $suffix add a suffix in image name
	 * @param string $force_type convert original image: jpg, gif, png
	 */
	public function imgThumbnail($width, $height, $constraint=false, $background_color=array(0,0,0), $suffix='', $force_type='')
	{

		//recover the extension
		$exts = explode('.', $this->new_name);

		$file_ext = strtolower($exts[count($exts)-1]);

		if(empty($force_type))
			$force_type = $file_ext;

		$file_name_output = $this->new_name;
		$file_name_output = str_replace(".$file_ext", "$suffix.$force_type", $file_name_output);
		$p = $this->path;
		$this->path .= "/$this->new_name";

		// apply size ?
		if($file_ext == 'jpg')$srcImg = imagecreatefromjpeg($this->path);
		elseif($file_ext == 'gif')$srcImg = imagecreatefromgif($this->path);
		elseif($file_ext == 'png')$srcImg = imagecreatefrompng($this->path);

		$old_x = imagesx($srcImg);
		$old_y = imagesy($srcImg);

		// width is larger than height
		if($old_x > $old_y)
		{
			$thumb_w = $width;
			$thumb_h = $old_y * ($width / $old_x);
		}
		else
		{
			$thumb_w = $old_x * ($height / $old_y);
			$thumb_h = $height;
		}

		$thumb_w = (int)$thumb_w;
		$thumb_h = (int)$thumb_h;

		// verify constraint
		#if($constraint)
		#{
			if($thumb_w > $width)
			{
				$thumb_h = $thumb_h * ($width / $thumb_w);
				$thumb_w = $width;
			}

			if($thumb_h > $height)
			{
				$thumb_w = $thumb_w * ($height / $thumb_h);
				$thumb_h = $height;
			}

			$thumb_w = (int)$thumb_w;
			$thumb_h = (int)$thumb_h;
		#}


		$img_resampled = @imagecreatetruecolor($thumb_w, $thumb_h);
		imagecopyresampled($img_resampled, $srcImg, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);


		if($constraint)
		{
			$posX = (int)(($width - $thumb_w)/2);
			$posY = (int)(($height - $thumb_h)/2);
			$img_resampled = imagecreatetruecolor($width, $height);
			$red = imagecolorallocate($img_resampled, $background_color[0], $background_color[1], $background_color[2]);
			imagefill($img_resampled, 0, 0, $red);

			//$background = imagecolorallocate($img_resampled, $background_color[0], $background_color[1], $background_color[2]);
			imagecopyresampled($img_resampled, $srcImg, $posX, $posY, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
		}

		// output with new prefix ?
		if($force_type == 'jpg')imagejpeg($img_resampled, $p."/".$file_name_output, 100);
		elseif($force_type == 'png')imagepng($img_resampled, $p."/".$file_name_output);
		elseif($force_type == 'gif')imagegif($img_resampled, $p."/".$file_name_output);
	}

}

?>