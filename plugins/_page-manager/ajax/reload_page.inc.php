<?php

nutsTrigger('page-manager::reload_page', true, "action reload on page");
$data = nutsGetMenu($_GET['language'], (int)$_GET['zoneID'], (int)$_GET['ID'], $_GET['state'], @$_GET['dID']);
echo $data;
exit(1);


