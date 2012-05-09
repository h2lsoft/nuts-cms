<?php

require_once('functions.php');

if(isset($_REQUEST["ajax"])){
	$selectedpath = urldecode($_REQUEST["path"]);
	if($selectedpath = checkpath($selectedpath, $uploadpath)){
		$dirs = getDirTree(DOCUMENTROOT.$selectedpath, true, false);
	} else {
		die('0||'.translate("The folder path was tampered with!"));
	}
} else {
	$selectedpath = $uploadpath;	
}

print '<table id="details" class="files tablesorter">
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
		<tbody>';
		
$htmlFiles = '';
$htmlFolders = '';

$all_lines = array();

foreach($dirs as $key => $value){
	if($value != "folder"){
		if(strtolower($value) == "png" || strtolower($value) == "jpg" || strtolower($value) == "jpeg" || strtolower($value) == "gif" || strtolower($value) == "bmp"){
							
			$filename = DOCUMENTROOT.$selectedpath.$key;
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
									$selectedpath.$key, 
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


		} else {
									
			$filename = DOCUMENTROOT.$selectedpath.$key;
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
									$selectedpath.$key, 
									$key, 
									$file_type,
									$file_size,
									$file_modified,
									$value
								 );
            $htmlFiles .= $TR;

            $all_lines['files'][] = array('name' => $key, 'type' => mime_content_type($filename), 'date' =>  filemtime($filename), 'size' => filesize($filename), 'dimensions' => "", 'tr' => $TR);

		}
	} else {
		
		$foldername = DOCUMENTROOT.$selectedpath.$key;
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
								 	$selectedpath.$key."/", 
									$key, 
									translate("Directory"),
									$folder_modified
								 );
        $htmlFolders .= $TR;

        $all_lines['folders'][] = array('name' => $key, 'date' =>  filemtime($foldername), 'size' => 0, 'dimensions' => 0, 'tr' => $TR);

	}
}

print $htmlFolders;
print $htmlFiles;
print '</tbody>
	</table>';


$encoded = json_encode($all_lines);


echo <<<EOF
<script>

var all_lines = $encoded;
$('#details th').click(function(e){

    direction = 'up';
    if(!$(this).hasClass('selected'))
    {
        $('#details th').removeClass('selected');
        $('#details th').removeClass('selected_up');
        $('#details th').removeClass('selected_down');
        $(this).addClass('selected');
        $(this).addClass('selected_up');
    }
    else
    {
        if(!$(this).hasClass('selected_up'))
        {
            $('#details th').removeClass('selected_down');
            $(this).addClass('selected_up');
        }
        else
        {
            $('#details th').removeClass('selected_up');
            $(this).addClass('selected_down');
            direction = 'down';
        }
    }

    tableDetailsSort($(this).attr('type'), direction);

});
</script>
EOF;




?>