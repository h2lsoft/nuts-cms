<?php

nutsTrigger('page-manager::reload', true, "action reload all pages");
$data = nutsGetMenu($_GET['language'], (int)$_GET['zoneID'], 0, $_GET['state'], $_GET['dID']);
echo $data;
exit(1);






?>