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
$NEWSLETTER_SUSCRIBE_OK = (@$page->language == 'fr') ? "Votre adresse email a bien été ajoutée" : "<i18n>Your email address has been added</i18n>";



// default template put [BODY] inside or let it empty
$HTML_TEMPLATE = <<<EOF
<style type="text/css">
body, td {font-family:arial; font-size:12px;}
img {border:0}
</style>

[BODY]
<br>
<br>
--
<br>
<a href="[UNSUSCRIBE_LINK]">Unsuscribe</a>
EOF;

// server configuration
define('PLUGIN_NEWSLETTER_BREAK', 100); # make a pause each X send
define('PLUGIN_NEWSLETTER_SLEEPTIME', 3); # pause in seconds


?>