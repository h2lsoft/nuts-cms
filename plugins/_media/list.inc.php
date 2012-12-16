<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// assign table to db
$plugin->listSetDbTable('NutsMedia');

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelect('Type', $lang_msg[1], array('YOUTUBE VIDEO', 'AUDIO', 'VIDEO', 'EMBED CODE'));
$plugin->listSearchAddFieldText('Name', $lang_msg[2]);


// create fields
$plugin->listAddCol('Type', $lang_msg[1], 'center;width:10px;  white-space:nowrap;', true);
$plugin->listAddCol('Name', $lang_msg[2], '; width:10px; white-space:nowrap;', true);
$plugin->listAddCol('Description', $lang_msg[3], '', false);
$plugin->listAddCol('Preview', '', 'center;width:10px;', false);

// popup
if(@$_GET['popup'] == 1)
{
	$plugin->listAddCol('AddCode', '&nbsp;', 'center; width:35px');
}


// render list
$plugin->listRender(20, 'hookData');

function hookData($row)
{
	global $lang_msg;


	$row['Preview'] = '';

    if($row['Type'] == 'YOUTUBE VIDEO')
    {
        $params = explode('@@', $row['Parameters']);
        $paramsX = array();
        foreach($params as $param)
        {
            if(!empty($param))
            {
                list($p,$v) = explode('=>', $param);
                $paramsX[$p] = $v;
            }
        }

        $row['Preview'] = youtubeGetPlayer($row['ID'], $paramsX['url'], 240, 160);
    }
	elseif($row['Type'] == 'AUDIO')
	{
		$row['Preview'] = '<object type="application/x-shockwave-flash" data="../library/js/dewplayer/dewplayer.swf?mp3='.$row['Url'].'&amp;showtime=1" width="200" height="20">

		                    <param name="wmode" value="transparent" />
							<param name="movie" value="../library/js/dewplayer/dewplayer.swf?mp3='.$row['Url'].'&amp;showtime=1" />
						 </object>';
	}
	elseif($row['Type'] == 'VIDEO')
	{
		$params = explode('@@', $row['Parameters']);
		$paramsX = array();
		foreach($params as $param)
		{
			if(!empty($param))
			{
				list($p,$v) = explode('=>', $param);
				$paramsX[$p] = $v;
			}
		}
		$startimage = "";
		if(!empty($paramsX['startimage']))
		{
			$startimage = "&amp;startimage={$paramsX['startimage']}";
		}

		$row['Preview'] = '<object type="application/x-shockwave-flash" data="/nuts/player_flv_maxi.swf" width="200" height="160">
								<param name="movie" value="/nuts/player_flv_maxi.swf" />
								<param name="allowFullScreen" value="true" />
								<param name="wmode" value="transparent" />

								<param name="FlashVars" value="flv='.$row['Url'].'&amp;width=200&amp;height=160&amp;showstop=1&amp;showvolume=1&amp;showplayer=always&amp;showloading=always&amp;showfullscreen=1&amp;showiconplay=1&amp;ondoubleclick=fullscreen&amp;autoload=0&amp;srt=1&amp;iconplaybgalpha=50'.$startimage.'" />
						</object>';
	}
	elseif($row['Type'] == 'EMBED CODE')
	{
		$row['Preview'] = "<a href=\"javascript:;\" onclick=\"popupModal('{$row['EmbedCodePreviewUrl']}', 'video preview', 1024, 768);\">{$lang_msg[11]}</a>";
	}

	// add code
	if(@$_GET['popup'] == 1)
	{
		$label = base64_encode($row['Name']);
		$code = "<p><img class=\"nuts_tags\" src=\"/nuts/img/icon_tags/tag.php?tag=media&label=$label\" title=\"{@NUTS    TYPE='MEDIA'    OBJECT='{$row['Type']}'    ID='{$row['ID']}'    NAME='{$row['Name']}'}\" border=\"0\"></p>";
		$code = str_replace('"', '``', $code);
		$code = str_replace("'", "\\'", $code);

		$row['AddCode'] = '<a href="javascript:;" onclick="window.opener.WYSIWYGAddText(\''.$_GET['parentID'].'\', \''.$code.'\'); window.close();" class="tt" title="'.$lang_msg[15].'"><img src="img/icon-next.png" align=\"absmiddle\" /></a>';
	}




	return $row;
}




?>