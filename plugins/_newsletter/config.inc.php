<?php

// default configuration
$NEWSLETTER_EXPEDITOR = 'newsletter@mydomain.com';
$NEWSLETTER_TO_TEST = 'myemail@mydomain.com';

$NEWSLETTER_SUBJECT = "Newsletter ".ucfirst(strftime('%B')).' '.date('Y');
$NEWSLETTER_IMG_TRACER_COLOR = array(255, 255, 255); // image tracer color
$NEWSLETTER_UNSUSCRIBE_URL_CONFIRMATION = WEBSITE_URL; // url for unsuscribe page

$NEWSLETTER_SUSCRIBE_ERROR_1 = (@$page->language == 'fr') ? "Votre adresse email n'est pas correcte" : "<i18n>Your email address is not correct</i18n>";
$NEWSLETTER_SUSCRIBE_ERROR_2 = "<i18n>Your language is not correct</i18n>";
$NEWSLETTER_SUSCRIBE_ERROR_3 = "<i18n>Your mailing-list is not correct</i18n>";
$NEWSLETTER_SUSCRIBE_OK = (@$page->language == 'fr') ? "<i18n>Votre adresse email a bien été ajoutée</i18n>" : "<i18n>Your email address has been added</i18n>";
$NEWSLETTER_SUSCRIBE_PLACEHOLDER = (@$page->language == 'fr') ? "mon@email" : "<i18n>my@email</i18n>";

$NEWSLETTER_MEMORY_LIMIT = '1024M';
$NEWSLETTER_SCHEDULER_TOKEN = ''; // generate a token => https://guidgenerator.com and add cron task => wget --no-check-certificate --delete "https://www.mywebsite.com/plugins/_newsletter/scheduler.php?token=[TOKEN]"


// default template put [BODY] inside or let it empty
$WEBSITE_URL = WEBSITE_URL;
$HTML_TEMPLATE = <<<EOF
<style type="text/css">
body, td {font-family:"Segoe UI", Candara, "Bitstream Vera Sans", "DejaVu Sans", "Trebuchet MS", Verdana, sans-serif;  font-size:12px;}
img {border:0}
#header {margin-bottom:10px; padding:0;}
</style>

<div id="header">
<a href="{$WEBSITE_URL}"><img src="{$WEBSITE_URL}/nuts/img/logo.png" /></a>
</div>

[BODY]

<br>
<br>
--
<br>
<a href="[UNSUBSCRIBE_LINK]">Unsuscribe</a>
EOF;

// server configuration
define('PLUGIN_NEWSLETTER_BREAK', 100); # make a pause each X send
define('PLUGIN_NEWSLETTER_SLEEPTIME', 3); # pause in seconds
define('PLUGIN_NEWSLETTER_MAXIMUM_SENT_BY_MONTH', 10000); # maximum email by month


$newsletter_www_template = 'template.html'; // leave blank to use default plugin template

