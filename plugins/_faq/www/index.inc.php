<?php
/**
 * Plugin faq - Front office
 *
 * @version 1.0
 * @date 02/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Page */
/* @var $nuts Page */
include(NUTS_PLUGINS_PATH.'/_faq/config.inc.php');
if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_faq/www/style.css');


$lang_file = NUTS_PLUGINS_PATH.'/_faq/lang/'.$page->language.'.inc.php';
if(!file_exists($lang_file))
	$lang_file = NUTS_PLUGINS_PATH.'/_faq/lang/en.inc.php';
include($lang_file);


// execution ***********************************************************************************************************
$plugin->openPluginTemplate();

Query::factory()->select('*')
				->from('NutsFAQ')
				->whereEqualTo('Language', $page->language)
				->whereEqualTo('Visible', 'YES')
				->order_by('Category, Position')
				->execute();

$answers = array();
while($row = $nuts->dbFetch())
{
	$answers[$row['Category']][] = $row;
}

if(count($answers) == 0)
{
	$nuts->erasebloc('qas');
	$nuts->parse('no_record_msg', $lang_msg[8]);
}
else
{
	$nuts->erasebloc('norecord');
	$qas = array();

	// parsing qas
	foreach($answers as $category => $questions)
	{
		if($category[0] == '-')$category[0] = '';
		$category = trim($category);
		$nuts->parse('qas.Category', $category);

		foreach($questions as $question)
		{
			$nuts->parse('qas.qa.ID', $question['ID']);
			$nuts->parse('qas.qa.Question', $question['Question']);
			$nuts->parse('qas.qa.Answer', $question['Answer']);
			$nuts->loop('qas.qa');
		}

		$nuts->loop('qas');
	}

}


$plugin->setNutsContent();


?>