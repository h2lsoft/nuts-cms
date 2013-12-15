<?php

ob_start();
phpinfo();
$info = ob_get_clean ();
$i = preg_match('#<body>(.*)</body>#msUi', $info, $matches);

$phpinfo = $matches[1];
$phpinfo = str_replace('<table ', '<table style="width:90%; border:1px solid #ccc; background-color:#ccc; text-align:left;" cellspacing="1" ', $phpinfo); # Body
$phpinfo = str_replace('<td class="e"', '<td style="background-color:#e5e5e5; white-space:nowrap;" ', $phpinfo);
$phpinfo = str_replace(' class="v"', ' style="background-color:#fff;" ', $phpinfo);
$phpinfo = str_replace("<a href=", '<a target="_blank" href=', $phpinfo);
$phpinfo = str_replace("<h1", '<h1 style="text-align:left"', $phpinfo);
$phpinfo = str_replace("<h2", '<h2 style="text-align:left"', $phpinfo);

$plugin->render = $phpinfo;

