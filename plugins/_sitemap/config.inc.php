<?php

$sitemap_filename = WEBSITE_PATH."/sitemap.xml"; // path to sitemap
$sitemap_url = WEBSITE_URL."/sitemap.xml"; // complete url to sitemap

$sitemap_mail_reporting = false; // send email reporting after submission
$sitemap_mail_admin = "contact@domain.com"; // email for ping reporting separated by comma
$sitemap_mail_from = 'sitemap@domain.com'; // email expeditor
$sitemap_mail_subject = '[Nuts CMS] Sitemap generated for `'.WEBSITE_URL.'`'; // email subject

$sitemap_ping_google = false; // ping sitemap after creation
$sitemap_ping_yahoo = false; // ping yahoo after creation
$sitemap_ping_yahoo_appid = 'APPID'; // ping yahoo after creation
$sitemap_ping_bing = false; // ping bing after creation

$sitemap_key = "adse861d2M1df3sdf"; // special get to prevent hacking by url

/*********************************************************************************************
 * Special pages to add dynamically
 *
 * Parameters:
 * 
 *  - url_parent: complete url for the crawler (characters / and ? are automatically backslashed)
 *  - priority: number 0.1 to 1.0 (0.5 by default)
 *  - changefreq: hourly, daily, weekly, monthly, always, never
 *
 * $sitemap_special_page[] = array('url_parent' => WEBSITE_URL."/my_url", 'url_regex' => "my regex", 'url_regex2' => 'my second regex (optionnal)', 'priority' => 0.5, 'changefreq' => 'weekly');
 * 
 ***********************************************************************************************/

$sitemap_special_page = array();


/*********************************************************************************************
 * To force some url please edit plugin file `custom.inc.php`
 ********************************************************************************************/

?>