<?php


$tpl_uri = '?_action=rte_get-template&ID=';

$tpls = Query::factory()->select("
										Name AS title,
										Description AS description,
										CONCAT('$tpl_uri', ID) AS url
									")
						    ->from('NutsRteTemplate')
							->order_by('Name')
							->executeAndGetAll();

die(json_encode($tpls));