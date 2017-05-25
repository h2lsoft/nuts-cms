<?php

$plugin->viewDbTable(array('NutsZone'));

$plugin->viewAddVar('Type', $lang_msg[1]);
$plugin->viewAddVar('Name', $lang_msg[2]);
$plugin->viewAddVar('CssName', $lang_msg[3]);
$plugin->viewAddVar('Description', $lang_msg[4]);
$plugin->viewAddVar('Navbar', $lang_msg[7]);

$plugin->viewRender();


