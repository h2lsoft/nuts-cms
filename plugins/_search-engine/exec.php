<?php
/**
 * Sitemap generator
 *
 * @version 1.0
 * @date 2010/07/01
 * @author H2lsoft
 *
 */

// configuration *************************************************************************
set_time_limit(0);
@ini_set('memory_limit', '256M');

// includes *************************************************************************
include("../../nuts/config.inc.php");
include(WEBSITE_PATH.'/nuts/headers.inc.php');

// execution *************************************************************************
$plugin = new NutsCore();
$plugin->dbConnect();
$nuts = &$plugin;
include("config.inc.php");

// controller *************************************************************************
if(!isset($_GET['key']) || $_GET['key'] != $spider_key)
	die();

echo "<h2>Nuts Spider</h2><hr>";
$plugin->doQuery("TRUNCATE TABLE NutsSpider");

// parse sitemap file
$sitemap_str = file_get_contents($spider_sitemap_path);
$sitemap_str = str_replace('&amp;', '&', $sitemap_str);
$sitemap_str = str_replace('&', '&amp;', $sitemap_str);

$url_done = array();
$sitemap = simplexml_load_string($sitemap_str);
foreach($sitemap->url as $url)
{
	$url = $url->loc;
	$exlude = false;
	echo "<br /><b>Url: $url</b><br />";

	foreach($spider_special_page_exclusion as $rule_exlusion)
	{
		$rule_exlusion = str_replace(array('/', '?'), array('\\/', '\?'), $rule_exlusion);
		$pattern = '@'.$rule_exlusion.'@i';
		if(!empty($url) && preg_match($pattern, $url))
		{
			$exlude = true;
			echo "<span style='color:red'> - Page Exclude => $url</span><br>";
			break;
		}
	}
	

	if(!in_array($url, $url_done) && !$exlude)
	{
		$text = @file_get_contents($url);

		// capture meta title
		$meta_title = '';
		$regex = '<title>(.*)</title>';
		if(preg_match("#$regex#is", $text, $matches))
		{
			$meta_title = trim($matches[1]);
		}

		// parse text
		if(!empty($spider_pattern_start) && !empty($spider_pattern_end) && !empty($text))
		{
			$text = $nuts->extractStr($text, $spider_pattern_start, $spider_pattern_end);

			// remove nocontent pattern
			if(!empty($spider_nopattern_start) && !empty($spider_nopattern_end) && !empty($text))
			{
				while(($rep = $nuts->extractStr($text, $spider_nopattern_start, $spider_nopattern_end, true)))
				{
					$text = str_replace($rep, '', $text);
				}
			}
			
			
			$text = preg_replace(
										array(
												  // Remove invisible content
													'@<head[^>]*?>.*?</head>@siu',
													'@<style[^>]*?>.*?</style>@siu',
													'@<script[^>]*?.*?</script>@siu',
													'@<object[^>]*?.*?</object>@siu',
													'@<embed[^>]*?.*?</embed>@siu',
													'@<applet[^>]*?.*?</applet>@siu',
													'@<noframes[^>]*?.*?</noframes>@siu',
													'@<noscript[^>]*?.*?</noscript>@siu',
													'@<noembed[^>]*?.*?</noembed>@siu',
												  // Add line breaks before and after blocks
													'@</?((address)|(blockquote)|(center)|(del))@iu',
													'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
													'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
													'@</?((table)|(th)|(td)|(caption))@iu',
													'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
													'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
													'@</?((frameset)|(frame)|(iframe))@iu',
												),
												array(
													' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
													"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
													"\n\$0", "\n\$0",
												),
												$text );


			// remove script tags
			$text = strip_tags($text);
			$text = str_replace(array("\n", "\r", "\t"), ' ', $text);
			do{
				$text = str_replace('  ', ' ', $text);
			}while(strpos($text, '  ') !== false);
			
			$text = trim($text);
		}

		// get language
		$lng = '';
		$tmp_uri = str_replace(WEBSITE_URL.'/', '', $url);
		$tmp_uri = explode('/', $tmp_uri);
		if(strlen($tmp_uri[0]) == 2)
			$lng = $tmp_uri[0];
		

		if(!empty($text))
			$plugin->dbInsert('NutsSpider', array(
												'Language' => $lng,
												'Title' => $meta_title,
												'Text' => $text,
												'Url' => $url));
		
		// sortie
		echo " + title: $meta_title<br>";			
		$url_done[] = $url;	
	}	
}


echo "<h3>Total pages : ".count($url_done)."</h3>";





?>