<?php 
/**
 * User Registration configuration
 */

$pluginRegister = array();

$pluginRegister['captcha'] = true; # use captcha to protect your form
$pluginRegister['captchaMaximum'] = -1; # maximum captcha failed (-1=unlimited)
$pluginRegister['NutsGroupID'] = 3; # ID of group for visitor
$pluginRegister['onValidSendEmail'] = true; # send an email with information after registration
$pluginRegister['onValidRedirectUrl'] = ''; # page redirection after successful registration (empty= logon page)

// label message
$pluginRegister['label_caption'] = ($page->language == 'fr') ? "Merci de remplir les champs suivants" : "Please fill the form below";
$pluginRegister['label_company'] = ($page->language == 'fr') ? "Votre Société" : "Your company";
$pluginRegister['label_first_name'] = ($page->language == 'fr') ? "Votre nom" : "Your first name";
$pluginRegister['label_last_name'] = ($page->language == 'fr') ? "Votre prénom" : "Your last name";
$pluginRegister['label_login'] = ($page->language == 'fr') ? "Identifiant" : "Login";
$pluginRegister['label_email'] = ($page->language == 'fr') ? "Votre email" : "Votre email";
$pluginRegister['label_password'] =	($page->language == 'fr') ? "Mot de passe" : "Password";
$pluginRegister['label_security_code'] = ($page->language == 'fr') ? "Code de sécurité" : "Security code";
$pluginRegister['label_required_fields'] = ($page->language == 'fr') ? "Champs requis" : "Required fields";
$pluginRegister['label_submit'] = ($page->language == 'fr') ? "Créer un nouveau compte" : "Create new account";






?>