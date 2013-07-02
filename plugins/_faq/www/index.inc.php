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

$lang_file = NUTS_PLUGINS_PATH.'/_faq/lang/'.$page->language.'.inc.php';
if(!file_exists($lang_file))
	$lang_file = NUTS_PLUGINS_PATH.'/_faq/lang/en.inc.php';
include($lang_file);


$plugin->openPluginTemplate();
if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_faq/www/style.css');

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
	$nuts->erasebloc('questions');
	$nuts->erasebloc('answers');
	$nuts->parse('no_record_msg', $lang_msg[8]);
}
else
{
	$nuts->erasebloc('norecord');

	$all_questions = array();

	// parsing questions
	foreach($answers as $category => $questions)
	{
		$nuts->parse('questions.Category', $category);

		foreach($questions as $question)
		{
			$nuts->parse('questions.question.ID', $question['ID']);
			$nuts->parse('questions.question.Question', $question['Question']);
			$nuts->loop('questions.question');
			$all_questions[] = $question;
		}

		$nuts->loop('questions');
	}

	// parsing answers
	foreach($all_questions as $question)
	{
		$nuts->parse('answers.ID', $question['ID']);
		$nuts->parse('answers.AQuestion', $question['Question']);
		$nuts->parse('answers.Answer', $question['Answer']);
		$nuts->loop('answers');
	}

}




$plugin->setNutsContent();



?>