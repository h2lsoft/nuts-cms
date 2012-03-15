<?php

include_once($plugin->plugin_path.'/config.inc.php');


/* @var $plugin Page */
$plugin->openPluginTemplate();

$plugin->parse('error1', $NEWSLETTER_SUSCRIBE_ERROR_1);
$plugin->parse('error2', $NEWSLETTER_SUSCRIBE_ERROR_2);
$plugin->parse('error3', $NEWSLETTER_SUSCRIBE_ERROR_3);
$plugin->parse('ok', $NEWSLETTER_SUSCRIBE_OK);

$plugin->setNutsContent();




?>