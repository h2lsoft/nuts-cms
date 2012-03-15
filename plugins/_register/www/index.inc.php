<?php

/* @var $plugin Page */
/* @var $nuts Page */

include($plugin->plugin_path.'/config.inc.php');
include(NUTS_PLUGINS_PATH.'/_login/config.inc.php');

// captcha
if($pluginRegister['captcha'])
{
	@session_start();
	$plugin->setCaptchaMax($pluginRegister['captchaMaximum']);
	$GLOBALS['TPLN_CAPTCHA_FIELD'] = $plugin->getCaptcha(); 
}

$plugin->formSetDisplayMode('T');

$lang = ($page->language == 'fr') ? $page->language : 'en';
$plugin->formSetLang($lang);
$plugin->formSetDisplayMode('T');

if($page->language == 'fr')
{
	foreach($pluginRegister as $key => $val)
	{
		if(preg_match("/^label/", $key))
		{		
			$object = str_replace('label_', '', $key);
			$object = str_replace('_',' ', $object);
			$object = ucwords($object);
			$object = str_replace(' ','', $object);
			
			$object_name = str_replace('Votre ', '', $val);
			$object_name = ucfirst($object_name);
			
			
			$plugin->formSetObjectName($object, $object_name);
		}
	}
	
}


// execution **************************************************************
$plugin->openPluginTemplate();

// captcha exception
if(!$pluginRegister['captcha'])
	$plugin->eraseBloc('captcha');

// parsing labels
foreach($pluginRegister as $key => $val)
{
	if(preg_match("/^label/", $key))
	{		
		$plugin->parse($key, $val);
	}
}

if($login_key != 'Login')
	$plugin->eraseBloc('login');




// form rules
$plugin->notEmpty('FirstName');
$plugin->notEmpty('LastName');
$plugin->notEmpty('Email');
$plugin->email('Email');

if($_POST && $login_key == 'Login')
{
	$plugin->notEmpty('Login');
	$plugin->alphaNumeric('Login', '_');
}

if($_POST)
{
	if($login_key == 'Login' && !empty($_POST['Login']) && nutsUserExists('Login', $_POST['Login']))
	{
		$msg = ($page->language == 'fr') ? "Identifiant déjà existant" : "Login already exists";
		$plugin->addError('Login', $msg);
	}
	
	if(!empty($_POST['Email']) && nutsUserExists('Email', $_POST[$login_key]))
	{
		$msg = ($page->language == 'fr') ? "Email déjà existant" : "Email already exists";
		$plugin->addError('Email', $msg);
	}
}

$plugin->notEmpty('Password');
$plugin->minLength('Password', 5);
$plugin->alphaNumeric('Password', '_-');

if($_POST)
{
	if($_POST['Password'] != $_POST['Password2'])
	{
		$msg = ($page->language == 'fr') ? "Vos mots de passe doivent être identiques" : "Your password must be the same";
		$plugin->addError('Password2', $msg);
	}		
}



if(!$plugin->formIsValid())
{
	if(!$_POST)
	{
		$plugin->eraseBloc('form_error_all');
	}
}
else
{
	$_POST['NutsGroupID'] = $pluginRegister['NutsGroupID'];
	
	$_POST['Company'] = ucfirst(strtolower(trim($_POST['Company'])));
	$_POST['FirstName'] = ucfirst(strtolower(trim($_POST['FirstName'])));
	$_POST['LastName'] = ucfirst(strtolower(trim($_POST['LastName'])));
	$_POST['Email'] = strtolower($_POST['Email']);
	if($login_key == 'Login')
	{
		$_POST['Login'] = strtolower($_POST['Login']);
	}
	else
	{
		$_POST['Login'] = uniqid();
	}
	
	// $pass_original = $_POST['Password'];
	// $_POST['Password'] = nutsCrypt($_POST['Password']);	
	$_POST['Language'] = $page->language;		
	$_POST['FrontOfficeToolbar'] = 'NO';
	$_POST['LogActionCreateDateGMT'] = nutsGetGMTDate();
	
	// register user
	$NutsUserID = nutsUserRegister($_POST, array('Password2', 'tpln_captcha'));	
	
	// save session + preserve key
	nutsUserLogin($NutsUserID, $session_add_sql_fields, $session_preserve_keys);
	
	// send email
	if($pluginRegister['onValidSendEmail'])
	{
		$mail_lang = ($page->language == 'fr') ? 'fr' : 'en';
		$_POST['Password'] = $pass_original;
		nutsSendEmail($email_template[$mail_lang], $_POST, $_POST['Email']);
	}
		
	// page redirection
	if(!empty($pluginRegister['onValidRedirectUrl']))
	{
		$uri = $pluginRegister['onValidRedirectUrl'];
	}
	else
	{
		nutsAccessRestrictedRedirectPage('logon');
	}	
}


$plugin->setNutsContent();




?>