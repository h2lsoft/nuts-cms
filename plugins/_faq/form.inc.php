<?php
/**
 * Plugin faq - Form layout
 * 
 * @version 1.0
 * @date 02/07/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */
include(PLUGIN_PATH.'/config.inc.php');

// sql table
$plugin->formDBTable(array('NutsFAQ'));

// fields
$lngs = nutsGetOptionsLanguages();

$plugin->formAddFieldSelectHtml('Language', $lang_msg[1], true, $lngs);
$plugin->formAddFieldTextAjaxAutoComplete('Category', $lang_msg[2], true, 'ucfirst');
$plugin->formAddFieldText('Question', $lang_msg[3], true, 'ucfirst');
$plugin->formAddFieldHtmlArea('Answer', $lang_msg[7], true, 'height:500px');
$plugin->formAddFieldBoolean('Visible', $lang_msg[4], true);

if($plugin->formModeIsEditing())
	$plugin->formAddFieldText('Position', $lang_msg[6], true, 'number');


if($_POST)
{
	$_POST['Category'] = ucfirst($_POST['Category']);
	$_POST['Question'] = ucfirst($_POST['Question']);

	if($plugin->formModeIsAdding())
	{
		$_POST['Position'] = $plugin->formGetMaxPosition('Position', 'Language', $_POST['Language'], 'NutsFAQ');
		$_POST['Position'] += 1;
	}
}





?>