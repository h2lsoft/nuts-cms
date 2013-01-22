<?php

# configuration

// website configuration **********************************************************************************************
# if(is_dir('install')){header('location: install/'); die();} # remove this line after installation

// general
define('WEBSITE_NAME', "Nuts Dev");
define('WEBSITE_PATH', "/home/nuts-cms/domains/dev.nuts-cms.com/public_html");
define('WEBSITE_URL', "http://dev.nuts-cms.com");
define('NUTS_ADMIN_EMAIL', "support@h2lsoft.com");
define('NUTS_EMAIL_NO_REPLY', "noreply@dev.nuts-cms.com");
define('APP_TITLE', "Nuts™ CMS");

define('BACKOFFICE_LOGO_URL', "");
define('NUTS_TRADEMARK', true);

// security
define('NUTS_CRYPT_KEY', "ncr4e4d60c80043d"); # use to crypt in database (empty = no crypt)
define('NUTS_RTE_FILEBROWSER_OBFUSCATE_KEY', 'nobf4e4d60c800441'); // change this value to obfuscate file browser

define('NUTS_LOG_ERROR_404', false);
define('NUTS_LOG_ERROR_TAGS', false);
define('NUTS_HTML_COMPRESS_TIME', 3600);

// front office
define('MetaTitle', "");
define('MetaDescription', "");
define('MetaKeywords', "");
define('NUTS_ERROR404_TEMPLATE', "error404.html");
define('NUTS_WWW_SESSION_INIT', false); // auto start session in front-office

$nuts_front_plugins_direct_access = array();
$nuts_theme_selected = 'default'; // theme selected

// redirect error page absolute url
define('NUTS_ERROR_PAGE_REDIRECT', "");

// output
define('NUTS_HTML_COMPRESS', false);
define('NUTS_TIDY', false);
$nuts_tidy_config = array('clean'  => false, 'indent' => true, 'output-xhtml' => true, 'wrap' => 7000, 'indent-spaces' => 4, 'alt-text' => "");
$nuts_tidy_pageID_exceptions = array(); // exception pages ID

// tools
define('FirePHP_enabled', true);


// services
define('WEBSITE_MAINTENANCE', false);
define('WEBSITE_MAINTENANCE_IPS', ''); # enter ip allowed to access (comma separator)
define('WEBSITE_MAINTENANCE_MESSAGE', "Website in maintenance, please come back later");



// database ************************************************************************************************************
define('NUTS_DB_TYPE', 'mysql');
define('NUTS_DB_HOST', "localhost");
define('NUTS_DB_USER', "nuts-cms");
define('NUTS_DB_PASSWORD', "kyyv4pzErk");
define('NUTS_DB_BASE', "dev");
define('NUTS_DB_PORT', "");


// misc ****************************************************************************************************************

// login page

// put your url for login page
define('LOGIN_PAGE_URL_EN', "/en/login/");
define('LOGIN_PAGE_URL_FR', "");
define('LOGIN_PAGE_URL_ES', "");
define('LOGIN_PAGE_URL_IT', "");
define('LOGIN_PAGE_URL_RU', "");

// put your url for logon page
define('LOGON_PAGE_URL_EN', "/en/my_account/");
define('LOGON_PAGE_URL_FR', "");
define('LOGON_PAGE_URL_ES', "");
define('LOGON_PAGE_URL_IT', "");
define('LOGON_PAGE_URL_RU', "");

// put your url for private page forbidden
define('PRIVATE_PAGE_FORBIDDEN_URL_EN', "/en/access_restricted/");
define('PRIVATE_PAGE_FORBIDDEN_URL_FR', "");
define('PRIVATE_PAGE_FORBIDDEN_URL_ES', "");
define('PRIVATE_PAGE_FORBIDDEN_URL_IT', "");
define('PRIVATE_PAGE_FORBIDDEN_URL_RU', "");


// twitter & facebook & google plus
define('TWITTER_LOGIN', "twitter_login");
define('FACEBOOK_PUBLISH_URL', "http://www.facebook.com");
define('GOOGLEP_PUBLISH_URL', "https://plus.google.com");

$nuts_session_preserve_keys = array(); // preserve session keys in login mecanism



# end of configuration

?>