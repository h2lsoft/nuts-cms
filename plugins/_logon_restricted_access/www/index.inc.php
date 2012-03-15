<?php

/* @var $plugin Page */
/* @var $nuts Page */

// configuration *************************************************************************
if(!nutsUserIsLogon())
{
	nutsAccessRestrictedRedirectPage('login');
}

// pre execution *************************************************************************
$message = ($plugin->language == 'fr') ? "Vous n'avez pas l'autorisation pour accéder à cet espace" : "You do not have permission to access this area";


// execution *************************************************************************
$plugin->openPluginTemplate();

$plugin->parse("message", $message);



$plugin->setNutsContent();



?>