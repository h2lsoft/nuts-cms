<?php
/**
 * Plugin social-share - Front office
 * 
 * @version 1.0
 * @date 30/12/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Page */
/* @var $nuts Page */

include($plugin->plugin_path.'/config.inc.php');

if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_social-share/style.css');

$plugin->openPluginTemplate();

$label = ($page->language == 'fr') ? 'Partager' : 'Share';
$plugin->parse('label', $label);


$title = $page->vars['H1'];
if(empty($title))$title = $page->vars['MetaTitle'];
if(empty($title))$title = $page->vars['MenuName'];

$plugin->parse('title', $title);
$plugin->parse('titleX', urlencode($title));


$plugin->setNutsContent();



?>