<?php

$data = array();
$data[] = nutsGetCountPages($_GET['language'], $_GET['zoneID']);
$data[] = nutsGetCountPages($_GET['language'], $_GET['zoneID'], 'PUBLISHED');
$data[] = nutsGetCountPages($_GET['language'], $_GET['zoneID'], 'DRAFT');
$data[] = nutsGetCountPages($_GET['language'], $_GET['zoneID'], 'WAITING MODERATION');

echo $nuts->array2json($data);
exit(1);













?>