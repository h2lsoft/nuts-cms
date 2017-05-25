<?php

/* @var $plugin Page */
/* @var $nuts Page */

include($plugin->plugin_path.'/config.inc.php');
include(NUTS_PLUGINS_PATH.'/_login/config.inc.php');


// configuration *******************************************************************************************************
$plugin->formSetDisplayMode('T');
$lang = ($page->language == 'fr') ? $page->language : 'en';
$plugin->formSetLang($lang);
$plugin->formSetDisplayMode('T');


// user info
$user = Query::factory()->select('*')
                       ->from('NutsUser')
                       ->where("ID", '=', (int)$_SESSION['NutsUserID'])
                       ->executeAndFetch();


// form rules
foreach($pluginRegister['form_fields'] as $form_fields)
{
    // update label
    $label = ($lang == 'fr') ? $form_fields['label_fr'] : $form_fields['label_en'];
    $plugin->formSetObjectName($form_fields['name'], $label);

    if(@$form_fields['required'])
    {
        if($form_fields['name'] != 'Login')
        {
            $plugin->notEmpty($form_fields['name']);
        }
    }
}

$plugin->email('Email');
if($_POST)
{
    if($login_key == 'Login')
    {
        $plugin->notEmpty('Login');
	    $plugin->alphaNumeric('Login', '_');
    }

    if($login_key == 'Login' && !empty($_POST['Login']) && nutsUserExists('Login', $_POST['Login'], $_SESSION['NutsUserID']))
	{
		$msg = ($page->language == 'fr') ? "Identifiant déjà existant" : "Login already exists";
		$plugin->addError('Login', $msg);
	}

	if(!empty($_POST['Email']) && nutsUserExists('Email', $_POST[$login_key], $_SESSION['NutsUserID']))
	{
		$msg = ($page->language == 'fr') ? "Email déjà existant" : "Email already exists";
		$plugin->addError('Email', $msg);
	}
}



if($_POST)
{
    if(!empty($_POST['Password']))
    {
        $plugin->minLength('Password', 5);
        $plugin->alphaNumeric('Password', '_-');

        if($_POST['Password'] != $_POST['Password2'])
        {
            $msg = ($page->language == 'fr') ? "Vos mots de passe doivent être identiques" : "Your password must be the same";
            $plugin->addError('Password2', $msg);
        }
    }

}



// execution ***********************************************************************************************************
$plugin->openPluginTemplate();

// generate fields
foreach($pluginRegister['form_fields'] as $form_fields)
{
    $label = ($lang == 'fr') ? $form_fields['label_fr'] : $form_fields['label_en'];
    $required = (!@$form_fields['required']) ? "" : '<span class="required">*</span>';
    $text_after = ($lang == 'fr') ? @$form_fields['text_after_fr'] : @$form_fields['text_after_en'];
    $input_type = (!@$form_fields['input_type']) ? "text" : $form_fields['input_type'];

    if($form_fields['name'] != 'Login' || ($form_fields['name'] == 'Login' && $login_key == 'Login'))
    {
        $v = (isset($_POST[$form_fields['name']])) ? $_POST[$form_fields['name']] : @$user[$form_fields['name']];
        if($form_fields['name'] == 'Password') $v = "";

        $plugin->parse('fields.name', $form_fields['name']);
        $plugin->parse('fields.required', $required);
        $plugin->parse('fields.input_type', $input_type);
        $plugin->parse('fields.label', $label);
        $plugin->parse('fields.value', $v);
        $plugin->parse('fields.text_after', $text_after);
        $plugin->loop('fields');
    }

    // add special fields Password2
    if($form_fields['name'] == 'Password')
    {
        $plugin->parse('fields.name', 'Password2');
        $plugin->parse('fields.required', $required);
        $plugin->parse('fields.label', $label.' 2');
        $plugin->parse('fields.input_type', $input_type);
        $plugin->parse('fields.value', '');
        $plugin->parse('fields.text_after', "");
        $plugin->loop('fields');
    }
}


// parsing labels
foreach($pluginRegister as $key => $val)
{
	if(preg_match("/^label/", $key))
		$plugin->parse($key, $val);
}


$output = $plugin->output();
$output = str_replace('< bloc::', '<bloc::', $output);
$output = str_replace('</ bloc::', '</bloc::', $output);
$plugin->createVirtualTemplate($output);


// form validation
if(!$plugin->formIsValid())
{
	if(!$_POST)
	{
		$plugin->eraseBloc('form_error_all');
	}
}
else
{
    $pass_original = $_POST['Password'];

    $_POST['Company'] = ucfirst(strtolower(trim($_POST['Company'])));
	$_POST['FirstName'] = ucfirst(strtolower(trim($_POST['FirstName'])));
	$_POST['LastName'] = ucfirst(strtolower(trim($_POST['LastName'])));
	$_POST['Email'] = strtolower($_POST['Email']);
	if($login_key == 'Login')
	{
		$_POST['Login'] = strtolower($_POST['Login']);
	}

    // recontruct exact form
    $f = array();
    foreach($pluginRegister['form_fields'] as $form_fields)
    {
        if(isset($_POST[$form_fields['name']]) && $form_fields['name'] != 'Password')
        {
            $f[$form_fields['name']] = $_POST[$form_fields['name']];
        }
    }
	$f['LogActionUpdateDateGMT'] = nutsGetGMTDate();

	// update profile
    nutsUserUpdate($f, true, array('Password2', 'tpln_captcha'));
    if(!empty($_POST['Password']))
        nutsUserSetPassword($_SESSION['NutsUserID'], $pass_original);

	// send email
	if($pluginRegister['onValidSendEmail'] && !empty($_POST['Password']))
	{
		$mail_lang = ($page->language == 'fr') ? 'fr' : 'en';
		$_POST['Password'] = $pass_original;
		nutsSendEmail($email_template[$mail_lang], $_POST, $_POST['Email']);
	}

    // trigger
    nutsTrigger('_profile', true, "user has updated profile");

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

// dynamic template parsing
if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_register/style.css');
$plugin->setNutsContent();


