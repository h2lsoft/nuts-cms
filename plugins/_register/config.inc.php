<?php 
/**
 * User Registration configuration
 */

$pluginRegister = array();

$pluginRegister['captcha'] = false; # use captcha to protect your form
$pluginRegister['captchaMaximum'] = -1; # maximum captcha failed (-1=unlimited)
$pluginRegister['NutsGroupID'] = 3; # ID of group for visitor
$pluginRegister['onValidSendEmail'] = true; # send an email with information after registration
$pluginRegister['onValidRedirectUrl'] = ''; # page redirection after successful registration (empty= logon page)
$pluginRegister['add_address_fields'] = true; // add adress fields group
$pluginRegister['add_phone_fields'] = true; // add phone fields group

$pluginRegister['label_caption'] = ($page->language == 'fr') ? "Merci de remplir les champs suivants" : "Please fill the form below";
$pluginRegister['label_required_fields'] = ($page->language == 'fr') ? "Champs requis" : "Required fields";
$pluginRegister['label_security_code'] = ($page->language == 'fr') ? "Code de sécurité" : "Security code";
$pluginRegister['label_submit'] = ($page->language == 'fr') ? "Créer un nouveau compte" : "Create new account";

$include_plugin_css = true; // inlude bundle css dynamically

// fields **************************************************************************************************************

// company
$pluginRegister['form_fields'][] = array(
                                            'name' => 'Company',
                                            'label_en' => "Company",
                                            'label_fr' => "Société",
                                            'required' => false
                                          );

// last name
$pluginRegister['form_fields'][] = array(
                                            'name' => 'LastName',
                                            'label_en' => "Your name",
                                            'label_fr' => "Votre nom",
                                            'required' => true
                                          );

// first name
$pluginRegister['form_fields'][] = array(
                                            'name' => 'FirstName',
                                            'label_en' => "Your first name",
                                            'label_fr' => "Votre prénom",
                                            'required' => true
                                          );

// address group *************************
if($pluginRegister['add_address_fields'])
{
    // Address
    $pluginRegister['form_fields'][] = array(
                                                'name' => 'Address',
                                                'label_en' => "Address",
                                                'label_fr' => "Address",
                                                'required' => true
                                              );

    // Address 2
    $pluginRegister['form_fields'][] = array(
                                                'name' => 'Address2',
                                                'label_en' => "Address 2",
                                                'label_fr' => "Address 2",
                                                'required' => false
                                              );

    // Zip code
    $pluginRegister['form_fields'][] = array(
                                                'name' => 'ZipCode',
                                                'label_en' => "Zip code",
                                                'label_fr' => "Code postal",
                                                'required' => true
                                              );

    // City
    $pluginRegister['form_fields'][] = array(
                                                'name' => 'City',
                                                'label_en' => "City",
                                                'label_fr' => "Ville",
                                                'required' => true
                                              );

    // Country
    $pluginRegister['form_fields'][] = array(
                                                'name' => 'Country',
                                                'label_en' => "Country",
                                                'label_fr' => "Pays",
                                                'required' => true
                                              );
}

// phone group ************************
if($pluginRegister['add_phone_fields'])
{
    // Phone
    $pluginRegister['form_fields'][] = array(
                                                'name' => 'Phone',
                                                'label_en' => "Your phone",
                                                'label_fr' => "Votre téléphone",
                                                'required' => true
                                              );

    // Gsm
    $pluginRegister['form_fields'][] = array(
                                                'name' => 'Gsm',
                                                'label_en' => "Your mobile",
                                                'label_fr' => "Votre mobile",
                                                'required' => false
                                              );

    // Fax
    $pluginRegister['form_fields'][] = array(
                                                'name' => 'Fax',
                                                'label_en' => "Your fax",
                                                'label_fr' => "Votre fax",
                                                'required' => false
                                              );
}

// login
$pluginRegister['form_fields'][] = array(
                                            'name' => 'Login',
                                            'label_en' => "Login",
                                            'label_fr' => "Identifiant",
                                            'required' => true
                                          );

// Email
$pluginRegister['form_fields'][] = array(
                                            'name' => 'Email',
                                            'label_en' => "Your email",
                                            'label_fr' => "Votre email",
                                            'required' => true
                                          );
// Password
$pluginRegister['form_fields'][] = array(
                                            'name' => 'Password',
                                            'label_en' => "Password",
                                            'label_fr' => "Mot de passe",
                                            'required' => true,
                                            'input_type' => 'password'
                                          );






?>