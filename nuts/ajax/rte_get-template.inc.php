<?php

$_GET['ID'] = (int)@$_GET['ID'];
$tpl = Query::factory()->select("Content")
			           ->from('NutsRteTemplate')
			           ->whereID($_GET['ID'])
			           ->executeAndGetOne();
die($tpl);