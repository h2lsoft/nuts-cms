<?php
/**
 * News reader plugin
 */

/* @var $plugin Page */
include('plugins/_news/config.inc.php');

// get news information
if(!$news_new_system) # old system check
{
    if($plugin->getPageParameterCount() == 0)$plugin->error404();
    $newsID = (int)$plugin->getPageParameter(0);
    if($newsID == 0)$plugin->error404();
}
else
{
    $urix = $_SERVER['SCRIPT_URL'];
    $newsID = (int)Query::factory()->select('ID')->from('NutsNews')->where('VirtualPageName', '=', $urix)->executeAndGetOne();
    if(!$newsID)$plugin->error404();
}

$sql = "SELECT
                *
                $sql_front_added
        FROM
                NutsNews
        WHERE
                ID=$newsID AND
                Active = 'YES'";



$plugin->doQuery($sql);
$row = $plugin->dbFetch();


// execution
$plugin->openPluginTemplate();

if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_news-reader/style.css');

$plugin->vars['MenuName'] = $row['Title'];
$plugin->addPattern('@News', $row['Title']);
$plugin->vars['H1'] = $row['Title']; # change H1 dynamically


$plugin->parse('Date', $row['DateGMT']);
$plugin->parse('Resume', $row['Resume']);
$plugin->parse('Content', $row['Text']);

// image parsing
if(empty($row['NewsImageModel']) && empty($row['NewsImage']))
{
	$plugin->eraseBloc('image');
}
else
{
	$news_image = $row['NewsImageModel'];
	if(!empty($row['NewsImage'])){
		$news_image = NUTS_NEWS_IMAGES_URL.'/'.$row['NewsImage'];
	}

	$plugin->parse('Image', $news_image);
	$plugin->parse('Title', $row['Title']);
}

$plugin->setNutsContent();





?>