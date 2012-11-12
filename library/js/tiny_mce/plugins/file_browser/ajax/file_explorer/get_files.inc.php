<?php
/**
 * Retrieve files list
 */

if(@!in_array($_GET['view'], array('large_images', 'small_images', 'list', 'tiles', 'details')))$_GET['view'] = 'small_images';
$selected_path = (!isset($_GET['path'])) ?  $upload_path : WEBSITE_PATH.urldecode($_GET["path"]);

if(!($selected_path = checkpath($selected_path, $upload_path)))
{
    $resp['result'] = 'ko';
    $resp['message'] = translate("Folder path is not correct");
}
else
{
    $dirs = getDirTree($selected_path, true, false);

    $htmlFolders = '';
    $htmlFiles = '';
    $all_lines = array();
    foreach($dirs as $key => $value)
    {
        $pathX = $selected_path.$key;
        $pathX = str_replace(WEBSITE_PATH, '', $pathX);
        $value = strtolower($value);


        if($value == "folder")
        {
            if(!in_array($selected_path.$key, $tree_hidden_folders))
            {
                // large_images + small images + list ********************************************************************************************
                if($_GET['view'] == 'large_images' || $_GET['view'] == 'small_images' || $_GET['view'] == 'list')
                {
                    $htmlFolders .= sprintf('<li>
                                                <a href="%1$s" title="%2$s" class="folder">
                                                    <span class="begin"></span>
                                                    <span class="filename">%2$s</span>
                                                    <span class="icon folder"></span>
                                                </a>
                                         </li>'.CR,
                        $pathX."/",
                        $key);
                }
                elseif($_GET['view'] == 'tiles')
                {
                    $htmlFolders .= sprintf('<li>
                                                    <a href="%1$s" title="%2$s" class="folder">
                                                        <span class="begin"></span>
                                                        <span class="filename">%2$s</span>
                                                        <span class="filetype">%3$s</span>
                                                        <span class="icon folder"></span>
                                                    </a>
                                            </li>'.CR,
                                            $pathX."/",
                                            $key,
                                            translate("Directory"));
                }
                elseif($_GET['view'] == 'tiles')
                {
                    $htmlFolders .= sprintf('<li>
                                                    <a href="%1$s" title="%2$s" class="folder">
                                                        <span class="begin"></span>
                                                        <span class="filename">%2$s</span>
                                                        <span class="filetype">%3$s</span>
                                                        <span class="icon folder"></span>
                                                    </a>
                                            </li>'.CR,
                                            $pathX."/",
                                            $key,
                                            translate("Directory"));
                }
                elseif($_GET['view'] == 'details')
                {
                    $foldername = $selected_path.$key;
                    $folder_modified = date($datetimeFormat, filemtime($foldername));

                    $TR = sprintf('<tr href="%1$s" class="folder">
                                        <td class="begin"></td>
                                        <td class="icon"><span class="folder"></span></td>
                                        <td class="filename">%2$s</td>
                                        <td class="filemodified">%4$s</td>
                                        <td class="filetype">%3$s</td>
                                        <td class="filesize">&nbsp;</td>
                                        <td class="filedim">&nbsp;</td>
                                        <td class="end">&nbsp;</td>
                                     </tr>',
                        $pathX."/",
                        $key,
                        translate("Directory"),
                        $folder_modified
                    );

                    $htmlFolders .= $TR;
                    $all_lines['folders'][] = array('name' => $key, 'date' =>  filemtime($foldername), 'size' => 0, 'dimensions' => 0, 'tr' => $TR);
                }
            }

        }
        else
        {

            // large_images ********************************************************************************************
            if($_GET['view'] == 'large_images')
            {
                if(in_array(strtolower($value), array('png','jpg','jpeg','gif','bmp')))
                {

                    $htmlFiles .= sprintf('<li>
                                            <a href="%1$s" title="%2$s" class="image">
                                                <span class="begin"></span>
                                                <span class="filename">%2$s</span>
                                                <span class="icon image"><img src="phpthumb/phpThumb.php?h=97&w=97&src=%4$s&far=1&bg=0000FF" /></span>
                                            </a>
                                        </li>'.CR,
                        $pathX,
                        $key,
                        $value,
                        urlencode($pathX));
                }
                else
                {
                    $htmlFiles .= sprintf('<li>
                                            <a href="%1$s" title="%2$s" class="file">
                                                <span class="begin"></span>
                                                <span class="filename">%2$s</span>
                                                <span class="icon %3$s"></span>
                                            </a>
                                        </li>'.CR,
                        $pathX,
                        $key,
                        $value);
                }
            }
            // small_images ********************************************************************************************
            elseif($_GET['view'] == 'small_images')
            {
                if(in_array(strtolower($value), array('png','jpg','jpeg','gif','bmp')))
                {
                    $htmlFiles .= sprintf('<li>
                                            <a href="%1$s" title="%2$s" class="image">
                                                <span class="begin"></span>
                                                <span class="filename">%2$s</span>
                                                <span class="icon image"><img src="phpthumb/phpThumb.php?h=48&w=48&src=%4$s&far=1&bg=0000FF" /></span>
                                            </a>
                                        </li>'.CR,
                        $pathX,
                        $key,
                        $value,
                        urlencode($pathX));
                }
                else
                {
                    $htmlFiles .= sprintf('<li>
                                            <a href="%1$s" title="%2$s" class="file">
                                                <span class="begin"></span>
                                                <span class="filename">%2$s</span>
                                                <span class="icon %3$s"></span>
                                            </a>
                                        </li>'.CR,
                        $pathX,
                        $key,
                        $value);
                }
            }
            // list ********************************************************************************************
            elseif($_GET['view'] == 'list')
            {
                if(in_array(strtolower($value), array('png','jpg','jpeg','gif','bmp')))
                {
                    $htmlFiles .= sprintf('<li>
                                            <a href="%1$s" title="%2$s" class="image">
                                                <span class="begin"></span>
                                                <span class="filename">%2$s</span>
                                                <span class="icon %3$s"></span>
                                            </a>
                                        </li>'.CR,
                        $pathX,
                        $key,
                        strtolower($value),
                        urlencode($pathX));
                }
                else
                {
                    $htmlFiles .= sprintf('<li>
                                            <a href="%1$s" title="%2$s" class="file">
                                                <span class="begin"></span>
                                                <span class="filename">%2$s</span>
                                                <span class="icon %3$s"></span>
                                            </a>
                                        </li>'.CR,
                        $pathX,
                        $key,
                        $value);
                }
            }
            // details ********************************************************************************************
            elseif($_GET['view'] == 'details')
            {
                if(in_array(strtolower($value), array('png','jpg','jpeg','gif','bmp')))
                {
                    $filename = $selected_path.$key;
                    $image_info = getimagesize($filename);
                    $file_modified = date($datetimeFormat, filemtime($filename));
                    $file_size = filesize($filename);
                    $file_size = $file_size < 1024  ? $file_size. ' '.translate('bytes') : $file_size < 1048576 ? number_format($file_size / 1024, 2, $dec_seperator, $thousands_separator) . ' '.translate('kB') : number_format($file_size / 1048576, 2, $dec_seperator, $thousands_separator) . ' '.translate('MB');

                    $TR = sprintf('<tr href="%1$s" class="image">
                                        <td class="begin"></td>
                                        <td class="icon"><span class="%8$s"></span></td>
                                        <td class="filename">%2$s</td>
                                        <td class="filemodified">%7$s</td>
                                        <td class="filetype">%3$s</td>
                                        <td class="filesize">%4$s</td>
                                        <td class="filedim">%5$s x %6$s</td>
                                        <td class="end">&nbsp;</td>
								   </tr>',

                                    $pathX,
                                    $key,
                                    $image_info['mime'],
                                    $file_size,
                                    $image_info[0],
                                    $image_info[1],
                                    $file_modified,
                                    strtolower($value)
                                );

                    $htmlFiles .= $TR;
                    $all_lines['files'][] = array('name' => $key, 'type' => $image_info['mime'], 'date' =>  filemtime($filename), 'size' => filesize($filename), 'dimensions' => "{$image_info[0]} x {$image_info[1]}", 'tr' => $TR);
                }
                else
                {
                    $filename = $selected_path.$key;
                    $file_modified = date($datetimeFormat, filemtime($filename));
                    $file_size = filesize($filename);
                    $file_type = mime_content_type($filename);
                    $file_size = $file_size < 1024  ? $file_size. ' '.translate('bytes') : $file_size < 1048576 ? number_format($file_size / 1024, 2, $dec_seperator, $thousands_separator) . ' '.translate('kB') : number_format($file_size / 1048576, 2, $dec_seperator, $thousands_separator) . ' '.translate('MB');

                    $TR = sprintf('<tr href="%1$s" class="file">
									<td class="begin"></td>
									<td class="icon"><span class="%6$s"></span></td>
									<td class="filename">%2$s</td>
									<td class="filemodified">%5$s</td>
									<td class="filetype">%3$s</td>
									<td class="filesize">%4$s</td>
									<td class="filedim">&nbsp;</td>
									<td class="end">&nbsp;</td>
								   </tr>',
                        $pathX,
                        $key,
                        $file_type,
                        $file_size,
                        $file_modified,
                        $value
                    );
                    $htmlFiles .= $TR;

                    $all_lines['files'][] = array('name' => $key, 'type' => mime_content_type($filename), 'date' =>  filemtime($filename), 'size' => filesize($filename), 'dimensions' => "", 'tr' => $TR);

                }
            }
            // tiles ********************************************************************************************
            elseif($_GET['view'] == 'tiles')
            {
                if(in_array(strtolower($value), array('png','jpg','jpeg','gif','bmp')))
                {
                    $filename = $selected_path.$key;
                    $image_info = getimagesize($filename);
                    $file_modified = date($datetimeFormat, filemtime($filename));

                    $htmlFiles .= sprintf('<li>
                                            <a href="%1$s" title="%2$s" class="image">
                                                <span class="begin"></span>
                                                <span class="filename">%2$s</span>
                                                <span class="filetype">%8$s</span>
                                                <span class="filedim">%6$s x %7$s</span>
                                                <span class="icon image"><img src="phpthumb/phpThumb.php?h=48&w=48&src=%4$s&far=1&bg=0000FF" /></span>
                                            </a>
                                        </li>'.CR,
                                        $pathX,
                                        $key,
                                        $value,
                                        urlencode($pathX),
                                        translate("Dimensions"),
                                        $image_info[0],
                                        $image_info[1],
                                        $image_info['mime']);
                }
                else
                {
                    $filename = $selected_path.$key;
                    //$file_modified = date($datetimeFormat, filemtime($filename));
                    $file_size = filesize($filename);
                    $file_type = mime_content_type($filename);
                    $file_size = $file_size < 1024  ? $file_size. ' '.translate('bytes') : $file_size < 1048576 ? number_format($file_size / 1024, 2, $dec_seperator, $thousands_separator) . ' '.translate('kB') : number_format($file_size / 1048576, 2, $dec_seperator, $thousands_separator) . ' '.translate('MB');

                    $htmlFiles .= sprintf('<li>
                                            <a href="%1$s" title="%2$s" class="file">
                                                <span class="begin"></span>
                                                <span class="filename">%2$s</span>
                                                <span class="filetype">%4$s</span>
                                                <span class="filesize">%5$s</span>
                                                <span class="icon %3$s"></span>
                                            </a>
                                        </li>'.CR,
                        $pathX,
                        $key,
                        $value,
                        $file_type,
                        $file_size);
                }
            }
        }
    }



    // format list
    if($_GET['view'] == 'details')
    {
        $encoded = json_encode($all_lines);

        $htmlFiles = '<table id="details" class="files tablesorter">
		<thead>
			<tr>
				<th type="name" colspan="3" class="filename">'.translate("Filename").'</th>
				<th type="date">'.translate("Modified on").'</th>
				<th type="type">'.translate("Filetype").'</th>
				<th type="size">'.translate("Size").'</th>
				<th type="dimensions">'.translate("Dimensions").'</th>
				<th class="end">&nbsp;</th>
			</tr>
		</thead>
		<tbody>'.$htmlFolders.$htmlFiles.'</tbody></table>';

        $htmlFiles .= <<<EOF
        <script>var all_lines = $encoded;</script>
EOF;

    }
    else
    {
        $htmlFiles = '<ul id="'.$_GET['view'].'" class="files clear">'.$htmlFolders.$htmlFiles.'</ul>';
    }



    $resp['result'] = 'ok';
    $resp['html'] = $htmlFiles;


}











?>