<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');

// twitter *************************************************************************
if(@$_GET['_action'] == 'twitter')
{
	$msg = @$_POST['message'];
	$msg = trim($msg);

	// lib curl
	if(!function_exists('curl_init'))die($lang_msg[22]);

	// empty msg
	if(strlen($msg) == 0)die($lang_msg[23]);
	if(strlen($msg) > 140)die($lang_msg[24]);

	$res = postToTwitter(TWITTER_LOGIN, TWITTER_PASSWORD, $msg) ? $lang_msg[25] : $lang_msg[26];
	die($res);
}


$theme = nutsGetTheme();
$langs = nutsGetOptionsLanguages();
$hidden_fields_arr = explode(',', str_replace(' ', '', $hidden_fields));


// sql table
$plugin->formDBTable(array('NutsNews'));

// fields
$plugin->formAddFieldsetStart('Image');
/*$plugin->formAddField('News', 'Image', 'image', false, array(
																'path' => NUTS_NEWS_IMAGES_PATH,
																'url' => NUTS_NEWS_IMAGES_URL,
																'size' => $news_thumb_parent_max_size,

																'parent_resize' => true,
																'parent_constraint' => $news_thumb_parent_constraint,
																'parent_width' => $news_thumb_parent_max_width,
																'parent_height' => $news_thumb_parent_max_height,
																'parent_background_color' => $news_thumb_parent_bkg_color,

																'thumbnail_new' => true,
																'thumbnail_constraint' => $news_thumb_constraint,
																'thumbnail_width' => $news_thumb_width,
																'thumbnail_height' => $news_thumb_height,
																'thumbnail_background_color' => $news_thumb_bkg_color

															));*/

$plugin->formAddFieldImage('News', 'Image', false, NUTS_NEWS_IMAGES_PATH, NUTS_NEWS_IMAGES_URL, $news_thumb_parent_max_size,
						   '', '', '', '', true, $news_thumb_parent_max_width, $news_thumb_parent_max_height, $news_thumb_parent_constraint,
						   $news_thumb_parent_bkg_color, true, $news_thumb_width, $news_thumb_height, $news_thumb_constraint, $news_thumb_bkg_color);

$plugin->formAddFieldImageBrowser('NewsImageModel', $lang_msg[28], false, 'nuts_news_models');
$plugin->formAddFieldsetEnd();

$plugin->formAddFieldSelect('Language', $lang_msg[1], true, $langs, "", "", "", "", true);
$plugin->formAddFieldDate('DateGMT', $lang_msg[2], true, $lang_msg[11]);

if(!in_array('DateGMTExpiration', $hidden_fields_arr))
{
	$plugin->formAddField('DateGMTExpiration', $lang_msg[3], 'date', false, array('help' => $lang_msg[12]));
}

$plugin->formAddFieldText('Title', $lang_msg[4], true, 'ucfirst');
if(!in_array('Resume', $hidden_fields_arr))$plugin->formAddFieldHtmlArea('Resume', $lang_msg[5], false, '', $lang_msg[13]);
if(!in_array('Text', $hidden_fields_arr))$plugin->formAddFieldHtmlArea('Text', $lang_msg[6], false, "height:400px");

$plugin->formAddFieldTextArea('Tags', $lang_msg[7], false, '', '', '', $lang_msg[14]);
$plugin->formAddFieldBoolean('Event', $lang_msg[8], true);
$plugin->formAddFieldBoolean('Comment', $lang_msg[17], true);
$plugin->formAddFieldBoolean('Active', $lang_msg[9], true, $lang_msg[15]);
$plugin->formAddFieldText('VirtualPageName', $lang_msg[18], false, '', '', '', '', $lang_msg[19]);

// filters *********************************************************
$plugin->formAddFieldsetStart('CustomFilter', $lang_msg[10]);

for($i=0; $i < count($cf); $i++)
{
	$help = (!isset($cf[$i]['help'])) ? '' : $cf[$i]['help'];

	if($cf[$i]['type'] == 'text')
		$plugin->formAddFieldText('Filter'.($i+1), toPascalCase($cf[$i]['name']), false, '', '', '', '', $help);
	elseif($cf[$i]['type'] == 'select')
		$plugin->formAddFieldSelect('Filter'.($i+1), toPascalCase($cf[$i]['name']), false, $cf[$i]['options'], '', '', '', $help);
}

$plugin->formAddFieldsetEnd();
// end of filters **************************************************


// Social networking
if(TWITTER_LOGIN != '' || FACEBOOK_PUBLISH_URL != '')
{
	$plugin->formAddFieldsetStart('Social networking');

	// twitter
	if(TWITTER_LOGIN != '')
	{
		$a_publish = '[ <a href="javascript:;" id="twitter_a" data-login="'.TWITTER_LOGIN.'">'.$lang_msg[21].'</a> ]';
		$plugin->formAddFieldTextArea('Twitter', '<img src="img/twitter_logo.png" style="width:70px" /><br/><span id="twitter_count">0</span> '.$lang_msg[20].'<br>'.$a_publish, false, '', 'height:50px;');
		$plugin->formAddException('Twitter');
	}

	// facebook
	if(FACEBOOK_PUBLISH_URL != '')
	{
		$plugin->formAddFieldText('Facebook', '<a title="'.$lang_msg[27].'" href="javascript:openFacebook(\''.FACEBOOK_PUBLISH_URL.'\');"><img src="img/facebook_logo.png" /></a>', false, '', 'display:none;');
		$plugin->formAddException('Facebook');
	}

	$plugin->formAddFieldsetEnd();
}



$plugin->formAddEndText("

<script>var hfs = explode(',', '$hidden_fields');</script>
<script src=\"".PLUGIN_URL."/form_custom.js\"></script>


");


?>