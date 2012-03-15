<?php

# configuration

// website configuration **********************************************************************************************
# if(is_dir('install')){header('location: install/'); die();} # remove this line after installation

// general
define('WEBSITE_NAME', "Nuts Demo");
define('WEBSITE_PATH', "C:/Program Files (x86)/EasyPHP-5.3.2i/www/test-nuts");
define('WEBSITE_URL', "http://nuts-test");
define('NUTS_ADMIN_EMAIL', "noreply@nuts-test");
define('NUTS_EMAIL_NO_REPLY', "noreply@nuts-test");
define('APP_TITLE', "Nuts™ CMS");

define('BACKOFFICE_LOGO_URL', ''); # empty or absolute url for nuts logo
define('NUTS_TRADEMARK', true);

// security
define('NUTS_CRYPT_KEY', "ncr4e4d60c80043d"); # use to crypt in database (empty = no crypt)
define('NUTS_RTE_FILEBROWSER_OBFUSCATE_KEY', 'nobf4e4d60c800441'); // change this value to obfuscate file browser

define('NUTS_LOG_ERROR_404', false); // log 404 error in control center (recommended for production false)
define('NUTS_LOG_ERROR_TAGS', false); // log nuts tags error in control center (recommended for production false)
define('NUTS_HTML_COMPRESS_TIME', 3600);  # number of seconds to keep files in cache by default

// front office
define('MetaTitle', ''); # default meta Title
define('MetaDescription', ''); # default meta Description
define('MetaKeywords', ''); # default meta Keywords
define('NUTS_ERROR404_TEMPLATE', 'error404.html');
$nuts_front_plugins_direct_access = array();
$nuts_theme_selected = 'default'; // theme selected

// redirect error page absolute url
define('NUTS_ERROR_PAGE_REDIRECT', ""); // fill `/error500.php` if you want to hide error

// output
define('NUTS_HTML_COMPRESS', false);  # active html compression
define('NUTS_TIDY', false);
$nuts_tidy_config = array('clean'  => false, 'indent' => true, 'output-xhtml' => true, 'wrap' => 7000, 'indent-spaces' => 4, 'alt-text' => "");
$nuts_tidy_pageID_exceptions = array(); // exception pages ID

// tools
define('FirePHP_enabled', true); # use false for production mode !


// services
define('WEBSITE_MAINTENANCE', false);
define('WEBSITE_MAINTENANCE_IPS', ''); # enter ip allowed to access (comma separator)
define('WEBSITE_MAINTENANCE_MESSAGE', "Website in maintenance, please come back later");



// database ************************************************************************************************************
define('NUTS_DB_TYPE', 'mysql');
define('NUTS_DB_HOST', '127.0.0.1');
define('NUTS_DB_USER', 'root');
define('NUTS_DB_PASSWORD', '');
define('NUTS_DB_BASE', 'NutsTest5');
define('NUTS_DB_PORT', '');


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


// twitter & facebook
define('TWITTER_LOGIN', 'twitter_login'); // put empty no not publish twitter applet
define('FACEBOOK_PUBLISH_URL', 'http://www.facebook.com'); // enter wall url to publish directly or empty to hide

$nuts_session_preserve_keys = array(); // preserve session keys in login mecanism



# end of configuration

?>