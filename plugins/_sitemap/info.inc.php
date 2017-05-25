<?php

include_once(PLUGIN_PATH.'/config.inc.php');

$lang_msg[2] = str_replace('[SITEMAP_COMPLETE_URL]', PLUGIN_URL.'/exec.php?key='.$sitemap_key, $lang_msg[2]);
$lang_msg[3] = str_replace('[SITEMAP_COMPLETE_URL]', PLUGIN_URL.'/exec.php?key='.$sitemap_key, $lang_msg[3]);
$lang_msg[4] = str_replace('[SITEMAP_COMPLETE_URL]', $sitemap_url, $lang_msg[4]);


$lang_msg[4] = str_replace('[CONTROL_CENTER_URL]', WEBSITE_URL.'/nuts/index.php?mod=_control-center&do=exec', $lang_msg[4]);


$plugin->directRender(PLUGIN_PATH.'/info.html');



