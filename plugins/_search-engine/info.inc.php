<?php

include_once(PLUGIN_PATH.'/config.inc.php');

$lang_msg[2] = str_replace('[SITEMAP_COMPLETE_URL]', PLUGIN_URL.'/exec.php?key='.$spider_key, $lang_msg[2]);
$lang_msg[3] = str_replace('[SITEMAP_COMPLETE_URL]', PLUGIN_URL.'/exec.php?key='.$spider_key, $lang_msg[3]);


$plugin->directRender(PLUGIN_PATH.'/info.html');








?>