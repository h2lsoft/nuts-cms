<?php
/**
 * Utils for Nuts
 * @package Utils
 * @version 1.0
 */


/**
 * A simple function using Curl to post (GET) to Twitter
 * Kosso : March 14 2007
 *
 * @param string $username
 * @param string $password
 * @param string $message
 * @return boolean
 */
function postToTwitter($username, $password, $message){

	// $host = "http://twitter.com/statuses/update.xml?status=".urlencode(stripslashes(urldecode($message)));
	$url = "http://twitter.com/statuses/update.xml";

	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_POST, 1);
	curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$message");
	curl_setopt($curl_handle, CURLOPT_USERPWD, "$username:$password");
	$buffer = curl_exec($curl_handle);
	curl_close($curl_handle);

	// tweet no more supported
	//if(empty($buffer))
	$twitter_status = false;
	//else
	// $twitter_status = true;

	/*$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	// curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "status=$message");

	$result = curl_exec($ch);
	// Look at the returned header
	$resultArray = curl_getinfo($ch);
	curl_close($ch);

	new dBug($resultArray);

	if($resultArray['http_code'] == "200"){
		 //$twitter_status = 'Your message has been sended! <a href="http://twitter.com/'.$username.'">See your profile</a>';
		 $twitter_status = true;
	} else {
		 //$twitter_status = "Error posting to Twitter. Retry";
		 $twitter_status = false;
	}

	 */
	return $twitter_status;
}

/**
 * Get Youtube player
 *
 * @param string $player_id
 * @param $youtube_url
 * @param string $width
 * @param string $height
 * @param string $attributes
 * @return string
 */
function youtubeGetPlayer($player_id, $youtube_url, $width='', $height='', $attributes="")
{
	// get default parameters
	$video_width = 640;
	$video_height = 360;

	if(!empty($width))$video_width = $width;
	if(!empty($height))$video_height = $height;


	// get video id by url => http://stackoverflow.com/questions/3392993/php-regex-to-get-youtube-video-id
	preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $youtube_url, $matches);
	$youtube_ID = @$matches[0];


	$player = "<iframe class=\"nuts_youtube_iframe_player\" type=\"text/html\" frameborder=\"0\" ";
	$player .= " id=\"nuts_youtube_iframe_player_$player_id\" ";
	$player .= " width=\"$video_width\" ";
	$player .= " height=\"$video_height\" ";
	$player .= " src=\"http://www.youtube.com/embed/$youtube_ID?HD=1&modestbranding=1&showinfo=0&rel=0\" ";
	$player .= " $attributes ";
	$player .= "></iframe>";

	return $player;
}

/**
 * Get Dailymotion player
 *
 * @param string $player_id
 * @param $url url
 * @param string $width
 * @param string $height
 * @param string $attributes
 * @return string
 */
function dailymotionGetPlayer($player_id, $url, $width='', $height='', $attributes="")
{
	// get default parameters
	$video_width = 640;
	$video_height = 360;

	if(!empty($width))$video_width = $width;
	if(!empty($height))$video_height = $height;

	preg_match('#http://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $url, $matches);
	$video_ID = @$matches[1];

	$player = "<iframe class=\"nuts_dailymotion_iframe_player\" frameborder=\"0\" ";
	$player .= " id=\"nuts_dailymotion_iframe_player_$player_id\" ";
	$player .= " width=\"$video_width\" ";
	$player .= " height=\"$video_height\" ";
	$player .= " src=\"http://www.dailymotion.com/embed/video/$video_ID?logo=0\" ";
	$player .= " $attributes ";
	$player .= "></iframe>";

	return $player;

}

/**
 * Return audio flash player with html5 fallback by default in swf
 *
 * @param $player_id
 * @param $url
 * @param $params
 *
 * @return string
 */
function mediaGetAudioPlayer($player_id, $url, $params)
{
	// classic
	$width = 200;
	$height = 20;
	$swf_suffix = '';

	if(@$params['type'] == 'MINI')
	{
		$width = 160;
		$height = 20;
		$swf_suffix = '-mini';
	}
	elseif(@$params['type'] == 'BUBBLE')
	{
		$width = 260;
		$height = 65;
		$swf_suffix = '-bubble';
	}

	// autostart ?
	$html5_params_add = '';
	$url_param = '';
	if(@$params['autoplay'] == 'YES')
	{
		$url_param .= '&amp;autostart=true';
		# $html5_params_add .= ' autoplay';
	}

	// loop ?
	if(@$params['autoreplay'] == 'YES')
	{
		$url_param .= '&amp;autoreplay=true';
		$html5_params_add .= ' loop ';
	}


	$player = <<<EOF
<div id="nuts_audio_player_{$player_id}" class="nuts_audio_player">
    <object type="application/x-shockwave-flash" data="../library/js/dewplayer/dewplayer{$swf_suffix}.swf?mp3={$url}&amp;showtime=1{$url_param}" width="$width" height="$height">
        <param name="wmode" value="transparent" />
        <param name="movie" value="../library/js/dewplayer/dewplayer{$swf_suffix}.swf?mp3={$url}&amp;showtime=1" />

        <audio oncontextmenu="return false;" controls="controls" $html5_params_add>
            <source src="{$url}">
        </audio>

    </object>
</div>
EOF;

	// full html 5
	if(@$params['type'] == 'HTML 5')
	{
		$html5_params_add = '';
		if(@$params['autoplay'] == 'YES')$html5_params_add .= ' autoplay';
		if(@$params['autoreplay'] == 'YES')$html5_params_add .= ' loop';

		$player = <<<EOF
<div id="nuts_audio_player_{$player_id}" class="nuts_audio_player">
        <audio oncontextmenu="return false;" controls="controls" $html5_params_add>
            <source src="{$url}">
        </audio>
</div>
EOF;
	}

	return $player;
}









