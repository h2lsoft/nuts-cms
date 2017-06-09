<?php
/**
 * Get file specs
 */

$cur_path = urldecode($_GET["path"]);
$type = (in_array($_GET["type"], array('folder', 'file', 'image'))) ? $_GET["type"] : "folder";


if($type == 'folder')
{
    $folder_name = WEBSITE_PATH.$cur_path;
    $folder_modified = date($datetimeFormat, filemtime($folder_name));
    $folder_name = @array_pop((@explode("/", trim($folder_name,"/"))));

    // no display at root
    if(WEBSITE_PATH.$cur_path == $upload_path)
    {
        $folder_name = $root_name;
        $folder_modified = '';
    }

    $html = '<div class="icon folder"></div>';
    $html .= '<div class="filename">'.$folder_name.'</div>';
    $html .= '<div class="filetype">'.translate('Folder').'</div>';
    $html .= '<div class="filemodified"><span>'.translate('Modified on').' :&nbsp;</span>'.$folder_modified.'</div>';
}
elseif($type == 'file')
{
    $filename = WEBSITE_PATH.$cur_path;
    $file_modified = date($datetimeFormat, filemtime($filename));
    $file_type = mime_content_type($filename);
    $file_size = filesize($filename);
    $file_size = $file_size < 1024  ? $file_size. ' '.translate('bytes') : $file_size < 1048576 ? number_format($file_size / 1024, 2, $dec_seperator, $thousands_separator) . ' '.translate('kB') : number_format($file_size / 1048576, 2, $dec_seperator, $thousands_separator) . ' '.translate('MB');
    $filename = @array_pop((@explode("/", $filename)));
    $fileext = @strtolower(@array_pop((@explode(".", $filename))));

	// get image preview from google viewer
	// $file_uri = urlencode(WEBSITE_URL.'/'.$cur_path);
	// $uri_preview = "https://docs.google.com/viewer?a=bi&pagenumber=1&w=140&url=".$file_uri;


    // $html = '<div class="icon '.$fileext.'"></div>';
    $html = '<div class="icon '.$fileext.'"></div>';
    $html .= '<div class="filename">'.$filename.'</div>';
    $html .= '<div class="filetype">'.$file_type.'</div>';
    $html .= '<div class="filemodified"><span>'.translate('Modified on').':&nbsp;</span>'.$file_modified.'</div>';
    $html .= '<div class="filesize"><span>'.translate('Size').':&nbsp;</span>'.$file_size.'</div>';

}
elseif($type == 'image')
{
    $filename = WEBSITE_PATH.$cur_path;
    $image_info = getimagesize($filename);
    $file_modified = date($datetimeFormat, filemtime($filename));
    $file_size = filesize($filename);
    $file_size = $file_size < 1024  ? $file_size. ' '.translate('bytes') : $file_size < 1048576 ? number_format($file_size / 1024, 2, $dec_seperator, $thousands_separator) . ' '.translate('kB') : number_format($file_size / 1048576, 2, $dec_seperator, $thousands_separator) . ' '.translate('MB');
    $filename = strtolower(@array_pop((@explode("/", $filename))));

    $html = '<div class="icon image"><img src="phpthumb/phpThumb.php?h=140&amp;w=140&amp;far=1&amp;src='.urlencode($cur_path).'&bg=0000FF" /></div>';
    $html .= '<div class="filename">'.$filename.'</div>';
    $html .= '<div class="filetype">'.$image_info['mime'].'</div>';
    $html .= '<div class="filemodified"><span>'.translate('Modified on').':&nbsp;</span>'.$file_modified.'</div></div>';
    $html .= '<div class="filesize"><span>'.translate('Size').':&nbsp;</span>'.$file_size.'</div></div>';
    $html .= '<div class="filedim"><span>'.translate('Dimensions').':&nbsp;</span>'.$image_info[0].' x '.$image_info[1].'</div></div>';

}


$resp['html'] = $html;


