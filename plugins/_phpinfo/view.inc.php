<?php

ob_start();
phpinfo();
$info = ob_get_clean ();
$i = preg_match('#<body>(.*)</body>#msUi', $info, $matches);

$phpinfo = $matches[1];
$phpinfo = str_replace('<table ', '<table style="border:1px solid #ccc; background-color:#ccc;" cellspacing="1" ', $phpinfo); # Body
$phpinfo = str_replace('<td class="e"', '<td style="background-color:#e5e5e5;" ', $phpinfo);
$phpinfo = str_replace(' class="v"', ' style="background-color:#fff;" ', $phpinfo);
$phpinfo = str_replace("<a href=", '<a target="_blank" href=', $phpinfo);

$plugin->render = $phpinfo;


?>