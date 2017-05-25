<?php

$help = file_get_contents(PLUGIN_PATH.'/help.txt');
$help = str_replace('- ', '&bull; ', $help);
$GLOBALS['help'] = $help;

$plugin->directRender(PLUGIN_PATH.'/exec.html');

