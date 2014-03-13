<?php

// edit page right *****************************************************************************************************
if(!nutsPageManagerUserHasRight(0, 'edit', 0, $_GET['ID']))
{
	$error_message = "You can not edit this page (right `edit` required)";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Vous ne pouvez pas éditer cette page (droit `éditer` requis)";
	die($error_message);
}

// check user has right
if(!nutsUserHasRight('', '_url_rewriting', 'add'))
{
	$error_message = "Error: you don't have right in url-rewriting";
	die($error_message);
}

// check if page exists
$page_source = "/{$_GET['language']}/{$_GET['ID']}.html";
$res = Query::factory()->count('*')->from('NutsUrlRewriting')->whereEqualTo('Replacement', $page_source)->whereEqualTo('Type', 'SIMPLE')->executeAndGetOne();
if($res >= 1)
{
	$error_message = "Error: this page is already rewrited";
	if($_SESSION['Language'] == 'fr')
		$error_message = "Erreur: cette page est déjà réécrite";
	die($error_message);
}


$max_position = (int)Query::factory()->select('Position')->from('NutsUrlRewriting')->whereEqualTo('Type', 'SIMPLE')->executeAndGetOne();
$max_position += 1;


// create page
$f = array();
$f['Type'] = 'SIMPLE';
$f['Pattern'] = $_GET['uri'];
$f['Replacement'] = $page_source;
$f['Tag'] = 'auto';
$f['Position'] = $max_position;
$nuts->dbInsert('NutsUrlRewriting', $f);


// force regenerate file
include(NUTS_PLUGINS_PATH.'/_url_rewriting/trt_url_rewriting.inc.php');


die('ok');

