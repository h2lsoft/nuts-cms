<?php

$include_plugin_css = true; // inlude bundle css dynamically

$spider_sitemap_path = WEBSITE_PATH.'/sitemap.xml'; // enter the path for the xml sitemap
$spider_key = "key4e0ba69861ab0"; // special get to prevent hacking by url

// properties
$spider_template = ""; // template for spider empty = template.html

$spider_pattern_start = "<!-- content -->"; // pattern start for capturing content
$spider_pattern_end = "<!-- /content -->"; // pattern end for capturing content

/*********************************************************************************************
 * Special pages to exlude
 *
 * Parameters:
 *
 *  - url_parent: complete url for the crawler (characters / and ? are automatically backslashed)
 *
 * $spider_special_page_exclusion[] = "my regex";
 *
 ***********************************************************************************************/

$spider_special_page_exclusion = array();

$spider_special_page_exclusion[] = '?tpg=';

/** update 2.1 **/
$spider_nopattern_start = "<!-- nocontent -->"; // pattern start for no capturing content
$spider_nopattern_end = "<!-- /nocontent -->"; // pattern end for no capturing content


