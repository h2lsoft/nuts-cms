<?php

// simples replacements *************************
$uri_str_patterns = array();
$uri_str_replaces = array();

$uri_str_patterns[] = "/en/access_restricted/";
$uri_str_replaces[] = "/en/13.html";

$uri_str_patterns[] = "/en/login/";
$uri_str_replaces[] = "/en/10.html";

$uri_str_patterns[] = "/en/register/";
$uri_str_replaces[] = "/en/11.html";

$uri_str_patterns[] = "/en/my_account/";
$uri_str_replaces[] = "/en/12.html";

$uri_str_patterns[] = "/en/my_profile/";
$uri_str_replaces[] = "/en/145.html";



// regex replacements *************************
$uri_patterns = array();
$uri_replaces = array();

$uri_patterns[] = "#^/en/news/$#";
$uri_replaces[] = "/en/14.html";

$uri_patterns[] = "#/en/news/(.*).html#";
$uri_replaces[] = "/en/15.html";


?>