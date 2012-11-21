<?php

$include_plugin_css = true; // inlude bundle css dynamically

$template = ''; // custom template (empty = default template)
$trace_mode = false; // allow to track user default action

$login_key = 'Email'; // Email or Login key to connect

$session_add_sql_fields = ""; // add sql fields in session for Login
$session_preserve_keys = array(); // Preserve initial session key like Basket for example

// mail:english **************************************************************************************************
$email_template['en']['subject'] = "Your account information";
$email_template['en']['body'] = "

Hi {FirstName} {LastName},

Your account information for website {WEBSITE_NAME} :

User: {{$login_key}}
Password: {Password}

To connect: {WEBSITE_URL}".LOGIN_PAGE_URL_EN;


// mail:french **************************************************************************************************
$email_template['fr']['subject'] = "Vos informations de connexion";
$email_template['fr']['body'] = "

Bonjour {FirstName} {LastName},

Voici vos identifiants de connexion au site {WEBSITE_NAME} :

Identifiant: {{$login_key}}
Mot de passe: {Password}

Pour vous connecter: {WEBSITE_URL}".LOGIN_PAGE_URL_FR;







?>