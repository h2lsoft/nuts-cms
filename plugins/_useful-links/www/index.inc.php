<?php
/**
 * Plugin useful links - Front office
 *
 * @version 1.0
 * @date 29/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Page */
/* @var $nuts Page */


include_once($plugin->plugin_path.'/config.inc.php');
include(Plugin::getIncludeUserLanguagePath());


if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_useful-links/www/style.css');


$plugin->openPluginTemplate();

$datas = Query::factory()->select('*')->from('NutsUsefulLinks')->whereEqualTo('Visible', 'YES')->order_by('Position')->executeAndGetAll();
$nuts->loadArrayInBloc('link', $datas, "<tr><td class='no_record'>{$lang_msg[5]}</td></tr>");

$plugin->setNutsContent();




?>