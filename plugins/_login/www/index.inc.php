<?php

/* @var $plugin Page */
/* @var $nuts Page */


// configuration *************************************************************************
include($plugin->plugin_path.'/config.inc.php');
if($plugin->language == 'fr')$plugin->formSetLang('fr');
$plugin->formSetDisplayMode('T');

// pre execution *************************************************************************
$caption = ($plugin->language == 'fr') ? 'Espace sécurisé' : 'User login';
$login_label = ($plugin->language == 'fr') ? 'Identifiant' : 'Login';
if($login_key == 'Email') $login_label = "Email";
$submit_label = ($plugin->language == 'fr') ? 'Envoyer' : 'Submit';
$password_label = ($plugin->language == 'fr') ? 'Mot de passe' : 'Password';
$required_fields_label = ($plugin->language == 'fr') ? 'Champs requis' : 'Required field';
$lost_passwd_label = ($plugin->language == 'fr') ? 'Mot de passe oublié ?' : 'Lost password ?';
$error_login_label = ($plugin->language == 'fr') ? 'Identifiant ou mot de passe non trouvé' : 'User or password not found';
$error_lost_password_invalid = ($plugin->language == 'fr') ? 'ko@@@Email non valide' : 'ko@@@Email not valid';
$error_lost_password_not_found = ($plugin->language == 'fr') ? 'ko@@@Utilisateur non trouvé' : 'ko@@@User not found';
$error_lost_password_found = ($plugin->language == 'fr') ? 'ok@@@Vos identifiants ont été envoyés par email' : 'ok@@@Your login information has been sended by email';

$plugin->formSetObjectNames(array('uLogin' => $login_label, 'uPassword' => $password_label));


// controller *************************************************************************
if(nutsUserIsLogon())
{
	if(isset($_GET['logout']) && $_GET['logout'] == 1)
		nutsUserLogout($session_preserve_keys);
	else
		nutsAccessRestrictedRedirectPage('logon');
}
else
{
	// lost password
	if(@$_GET['action'] == 'lost_password')
	{
		if(!isset($_GET['Email']) || empty($_GET['Email']) || !email($_GET['Email']))
		{
			die($error_lost_password_invalid);
		}
		else
		{
			$nuts->dbSelect("SELECT
									NutsUser.Login,
									DECODE(NutsUser.Password, '".NUTS_CRYPT_KEY."') AS Password,
									NutsUser.FirstName,
									NutsUser.LastName,
									NutsUser.Email
							FROM
									NutsUser,
									NutsGroup
							WHERE
									NutsUser.NutsGroupID = NutsGroup.ID AND
									NutsUser.Email = '%s' AND
									NutsUser.Active = 'YES' AND
									NutsGroup.FrontofficeAccess = 'YES' AND
									NutsGroup.Deleted = 'NO' AND
									NutsUser.Deleted = 'NO'", array($_GET['Email']));

			if($nuts->dbNumRows() == 0)
			{
				if($trace_mode)nutsTrace('front-office', 'lost_password', 'error Email => '.'`'.$_GET['Email'].'`', 0);
				die($error_lost_password_not_found);
			}
			else
			{
				$row = $nuts->dbFetch();
				nutsSendEmail($email_template[$plugin->language], $row, $_GET['Email']);
				if($trace_mode)nutsTrace('front-office', 'lost_password', 'success Email => '.'`'.$_GET['Email'].'`');
				die($error_lost_password_found);
			}
		}
	}
}



// execution *************************************************************************
if(empty($template))$template = 'template.html';
$plugin->openPluginTemplate($template);

if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_login/style.css');

if($plugin->itemExists('caption'))$plugin->parse("caption", $caption);
if($plugin->itemExists('login_label'))$plugin->parse("login_label", $login_label);
if($plugin->itemExists('password_label'))$plugin->parse("password_label", $password_label);
if($plugin->itemExists('required_fields_label'))$plugin->parse("required_fields_label", $required_fields_label);
if($plugin->itemExists('lost_passwd_label'))$plugin->parse("lost_passwd_label", $lost_passwd_label);
if($plugin->itemExists('submit_label'))$plugin->parse("submit_label", $submit_label);


// form rules
$plugin->notEmpty('uLogin');
$plugin->notEmpty('uPassword');

if($_POST && !empty($_POST['uLogin']) && $_POST['uPassword'])
{
	$crypt_pass = nutsCrypt($_POST['uPassword']);

	$nuts->dbSelect("SELECT
							NutsUser.ID
					FROM
							NutsUser,
							NutsGroup
					WHERE
							NutsUser.NutsGroupID = NutsGroup.ID AND
							NutsUser.$login_key = '%s' AND
							NutsUser.Password = '%s' AND
							NutsUser.Active = 'YES' AND
							NutsGroup.FrontofficeAccess = 'YES' AND
							NutsGroup.Deleted = 'NO' AND
							NutsUser.Deleted = 'NO'
					LIMIT
							1", array($_POST['uLogin'], $crypt_pass));

	if($nuts->dbNumRows() == 0)
	{
		if($trace_mode)
			nutsTrace('front-office', 'login', 'error => '.'`'.htmlentities($_POST['uLogin']).'`'."; ".'`'.htmlentities($_POST['uPassword']).'`', 0);
		$nuts->addError('uLogin', $error_login_label);
	}
}

if($plugin->formIsValid())
{
	// logon + trace
	$row = $nuts->dbFetchAssoc();
	nutsUserLogin($row['ID'], $session_add_sql_fields, $session_preserve_keys);

	if($trace_mode)
		nutsTrace('front-office', 'logon');

    // trigger
    nutsTrigger('_login', true, "user logon successful");


	// redirect page detection
	if(@$_GET['r'][0] == '/')
	{
		$nuts->redirect($_GET['r']);
	}
	else
	{
		// redirect logon page
		nutsAccessRestrictedRedirectPage('logon');
	}

}



$plugin->setNutsContent();



?>