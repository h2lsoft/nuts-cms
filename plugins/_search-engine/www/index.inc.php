<?php

/* @var $plugin Page */
/* @var $nuts Page */
include($plugin->plugin_path.'/config.inc.php');

// controller *************************************************************************
if(!isset($_GET['q']))$_GET['q'] = '';
$_GET['q'] = html_entity_decode($_GET['q']);
$_GET['q'] = trim($_GET['q']);


// execution *************************************************************************
if(!empty($spider_template))
	$plugin->openPluginTemplate($spider_template);
else
	$plugin->openPluginTemplate();

if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_search-engine/style.css');



// pager
$start_lbl = ($page->language == 'fr') ? 'Début' : 'Start';
if($plugin->itemExists('start_lbl'))$plugin->parse("start_lbl", $start_lbl);

$previous_lbl = ($page->language == 'fr') ? 'Précédent' : 'Previous';
if($plugin->itemExists('previous_lbl'))$plugin->parse("previous_lbl", $previous_lbl);

$next_lbl = ($page->language == 'fr') ? 'Suivant' : 'Next';
if($plugin->itemExists('next_lbl'))$plugin->parse("next_lbl", $next_lbl);

$end_lbl = ($page->language == 'fr') ? 'Fin' : 'End';
if($plugin->itemExists('end_lbl'))$plugin->parse("end_lbl", $end_lbl);



// parsing
$search_lng = ($page->language == 'fr') ? 'Rechercher' : 'Search';
if($plugin->itemExists('Search'))$plugin->parse("Search", $search_lng);

$norecord_lng = ($page->language == 'fr') ? 'Aucun résultat trouvé pour' : 'No result found for';
if($plugin->itemExists('norecord_msg'))$plugin->parse("norecord_msg", $norecord_lng);

$sql = "SELECT * FROM NutsSpider WHERE MATCH (Title, Text) AGAINST ('".addslashes($_GET['q'])."') AND (Language = '{$page->language}' OR Language = '')";

$cur_uri = str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
$plugin->setUrl($cur_uri);




$plugin->urlAddVar("q={$_GET['q']}");

if(empty($_GET['q']) || strlen($_GET['q']) < 3)
{
	$plugin->eraseBloc('data');
}
else
{
	$plugin->showRecords($sql, 10, 'hookData');
}


function hookData($row)
{
	global $page;

	if(empty($row['Title']))
	{
		$title_lng = ($page->language == 'fr') ? 'Sans titre' : 'No title';
		$row['Title'] = '<i18n>'.$title_lng.'</i18n>';
	}

	$row['Resume'] = mb_substr($row['Text'], 0, 150,'UTF-8');

	return $row;
}



$plugin->setNutsContent();



