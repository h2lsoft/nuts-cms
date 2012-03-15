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
include(WEBSITE_PATH."/nuts/headers.inc.php");


// execution *************************************************************************
$plugin = new NutsCore();
$plugin->dbConnect();
$nuts = &$plugin;
include("config.inc.php");

$sel_languages = array(nutsGetDefaultLanguage());
$sel_languages_added = nutsGetOptionsLanguages('array');
foreach($sel_languages_added as $sel)
{
	if(!empty($sel))
		$sel_languages[] = $sel;
}
$sel_languages = array_unique($sel_languages);
$sel_languages = join("','", $sel_languages);

// controller *************************************************************************
if(!isset($_GET['key']) || $_GET['key'] != $sitemap_key)
	die();
$fp = fopen($sitemap_filename, 'w') or die("Error: could not open `$sitemap_filename`");
fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');


// update DateStart and DateEnd
$sql = "SELECT 
				ID, Language, VirtualPagename, DATE_FORMAT(DateUpdate, '%Y-%m-%d') AS LastMod, SitemapPriority, SitemapChangefreq, Comments
		FROM
				NutsPage
		WHERE 
				Deleted = 'NO' AND
				State = 'PUBLISHED' AND
				Sitemap = 'YES' AND
				SitemapPageType = 'NORMAL' AND
				
				Language IN('$sel_languages') AND

				(DateStartOption = 'NO' OR (DateStartOption = 'YES' AND DateStart <= NOW())) AND
				(DateEndOption = 'NO' OR (DateEndOption = 'YES' AND DateEnd >= NOW()))

		ORDER BY
				Language,
				ZoneID,
				Position";
$plugin->doQuery($sql);

$pages_done = array();

// automatic pages
$total_pages_automatic = 0;
$qID = $plugin->dbGetQueryID();
while($page = $plugin->dbFetch())
{	
	if(!preg_match('/^http/i', $page['VirtualPagename']) && (strlen($page['VirtualPagename']) > 0 && $page['VirtualPagename'][0] != '{'))
	{
		$plugin->doQuery("SELECT DATE_FORMAT(Date, '%Y-%m-%d') AS Date FROM NutsPageComment WHERE NutsPageID = {$page['ID']} AND Visible = 'YES' AND Deleted = 'NO' ORDER BY Date DESC LIMIT 1");
		if($plugin->dbNumRows() == 1)
		{
			$last_comment_date = $plugin->dbGetOne();
			$plugin->doQuery("SELECT DATEDIFF('{$page['LastMod']}', '$last_comment_date')");
			$diff = (int)$plugin->dbGetOne();
			if($diff < 0)
				$page['LastMod'] = $last_comment_date;	
		}

		$plugin->dbSetQueryID($qID);
		
		// url begins by `/`
		if(!empty($page['VirtualPagename']) && $page['VirtualPagename'][0] == '/')
		{
			$uri = WEBSITE_URL.$page['VirtualPagename'];
		}
		else
		{
			$uri = WEBSITE_URL.'/'.$page['Language'].'/'.$page['ID'];
			$uri .= (!empty($page['VirtualPagename'])) ? '-'.$page['VirtualPagename'].'.html' : '.html';
		}
				
		$pages_done[] = $uri;
		sitemapAddNode($uri, $page['SitemapChangefreq'], $page['SitemapPriority'], $page['LastMod']);
		$total_pages_automatic++;
		
	}
}
echo "<h2>Nuts Sitemap Generator</h2><hr>";
echo "<h4>Automatic pages found : $total_pages_automatic</h4>";


// custom pages
include("custom.inc.php");
$total_pages_custom = 0;
foreach($sitemap_custom as $sc)
{
	$pages_done[] = $sitemap_custom['url'];
	sitemapAddNode($sitemap_custom['url'], $sitemap_custom['changefreq'], $sitemap_custom['priority'], $sitemap_custom['modification']);
	$total_pages_custom++;
}
if($total_pages_custom)
	echo "<h4>Custom pages found : $total_pages_custom</h4>";


// special pages
$total_pages_special = 0;
$report_page_special = array();
$c_index = 0;

// rep parsing to include regex 2 for sub regex
$sql = "SELECT 
				ID, Language, VirtualPagename, DATE_FORMAT(DateUpdate, '%Y-%m-%d') AS LastMod, SitemapPriority, SitemapChangefreq, SitemapUrlRegex1, SitemapUrlRegex2 
		FROM
				NutsPage
		WHERE 
				Deleted = 'NO' AND
				State = 'PUBLISHED' AND
				Sitemap = 'YES' AND
				SitemapPageType = 'TUNNEL' AND

				Language IN('$sel_languages')

		ORDER BY
				Language,
				ZoneID,
				Position";
$plugin->doQuery($sql);
while($rt = $plugin->dbFetch())
{
	// url begins by `/`
	if(!empty($rt['VirtualPagename']) && $rt['VirtualPagename'][0] == '/')
	{
		$uri = WEBSITE_URL.$rt['VirtualPagename'];
	}
	else
	{
		$uri = WEBSITE_URL.'/'.$rt['Language'].'/'.$rt['ID'];
		$uri .= (!empty($rt['VirtualPagename'])) ? '-'.$rt['VirtualPagename'].'.html' : '.html';
	}
	
	$sitemap_special_page[] = array('url_parent' => $uri, 'url_regex' => $rt['SitemapUrlRegex1'], 'url_regex2' => $rt['SitemapUrlRegex2'], 'priority' => $rt['SitemapPriority'], 'changefreq' => $rt['SitemapChangefreq']);
}


$regex2 = array();
$regex2_dones = array();
$sitemap_recreation = array();
foreach($sitemap_special_page as $special_page)
{	
	if(!isset($special_page['url_regex2']) || empty($special_page['url_regex2']))
	{
		$sitemap_recreation[] = $special_page;
	}
	else
	{
		// inlcude parent page		
		$regex2[] = array('url_parent' => $special_page['url_parent'], 'url_regex' => $special_page['url_regex2'], 'priority' => $special_page['priority'], 'changefreq' => $special_page['changefreq']);
		$regex2[] = array('url_parent' => $special_page['url_parent'], 'url_regex' => $special_page['url_regex'], 'priority' => $special_page['priority'], 'changefreq' => $special_page['changefreq']);
		
		$sitemap_recreation[] = $regex2[count($regex2)-2];
		$sitemap_recreation[] = end($regex2);

		//if(!in_array($special_page['url_parent'], $regex2_dones))
		//{
			$regex2_dones[] = $special_page['url_parent'];

			$tmp_page = file_get_contents($special_page['url_parent']);
			$url_regex = str_replace(array('/', '?'), array('\\/', '\?'), $special_page['url_regex']);
			$regex = '@<a.*? href=[\'"]?([^"\'\s\>]*)@i';
			preg_match_all($regex, $tmp_page, $matches);

			foreach($matches[1] as $href)
			{
				$pattern = '@'.$url_regex.'@i';
				if(preg_match($pattern, $href) && !in_array($href, $regex2_dones))
				{
					// 'url_parent' => , 'url_regex' => , 'priority' => , 'changefreq' =>
					$href = str_replace(WEBSITE_URL.'/', '', $href);
					if($href[0] == '/')$href[0] = ' ';
					$href = trim($href);
					
					$href = WEBSITE_URL.'/'.$href;

					$regex2[] = array('url_parent' => $href, 'url_regex' => $special_page['url_regex2'], 'priority' => $special_page['priority'], 'changefreq' => $special_page['changefreq']);
					$regex2_dones[] = $href;
					$sitemap_recreation[] = end($regex2);
				}
			}
		//}
	}
}


$sitemap_special_page = array();
$tmp_url_parents = array();
foreach($sitemap_recreation as $tmp)
{
	$key = $tmp['url_parent'].'_'.$tmp['url_regex'];
	if(!in_array($key, $tmp_url_parents))
	{
		$tmp_url_parents[] = $key;
		$sitemap_special_page[] = $tmp;
	}
}

foreach($sitemap_special_page as $special_page)
{
	echo '<h5> + Manual page for <a href="'.$special_page['url_parent'].'" target="_blank">'.$special_page['url_parent'].'</a> : (regex: `'.$special_page['url_regex'].'`)</h5>';

	$tmp_page = file_get_contents($special_page['url_parent']);
	$url_regex = str_replace(array('/', '?'), array('\\/', '\?'), $special_page['url_regex']);
	$regex = '@<a.*? href=[\'"]?([^"\'\s\>]*)@i';
	preg_match_all($regex, $tmp_page, $matches);
	
	if(!isset($report_page_special[$c_index]))$report_page_special[$c_index] = 0;
	foreach($matches[1] as $href)
	{
		$pattern = '@'.$url_regex.'@i';
		if(preg_match($pattern, $href) && !in_array($href, $pages_done))
		{			
			$pages_done[] = $href;
			$href = WEBSITE_URL.$href;

			if(sitemapAddNode($href, $special_page['changefreq'], $special_page['priority']))
			{				
				echo $href.'<br />';
				$report_page_special[$c_index] += 1;
				$total_pages_special++;
			}
			
		}
	}

	echo '<h5>Total found '.$report_page_special[$c_index].'</h5>';
	$c_index++;
}

echo "<h4>Manual pages found : $total_pages_special</h4>";

$total = $total_pages_special + $total_pages_custom + $total_pages_automatic;
echo "<h3>Total pages found : $total</h3>";

sitemapClose();
$plugin->dbClose();


// ping search engines *************************************************************************
if($sitemap_ping_google || $sitemap_ping_yahoo || $sitemap_ping_bing)
	echo "<hr><h3>Ping search engines</h3><hr>";

// Google
if($sitemap_ping_google)
{
	echo "<h4>Ping Google : </h4>";
	$ping_uri = "http://www.google.com/webmasters/tools/ping?sitemap=".urlencode($sitemap_url);
	$res_google = file_get_contents($ping_uri);
	echo "<div style='height:150px; overflow:scroll; border:1px solid #ccc; padding:5px;'>".$res_google."</div>";
}
// Yahoo
if($sitemap_ping_yahoo)
{
	echo "<h4>Ping Yahoo : </h4>";
	$ping_uri = "http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=$sitemap_ping_yahoo_appid&url=".urlencode($sitemap_url);
	$res_yahoo = file_get_contents($ping_uri);
	echo "<div style='height:50px; overflow:scroll; border:1px solid #ccc; padding:5px;'>".htmlentities($res_yahoo)."</div>";
}
// Bing
if($sitemap_ping_bing)
{
	echo "<h4>Ping Google : </h4>";
	$ping_uri = "http://www.bing.com/webmaster/ping.aspx?siteMap=".urlencode($sitemap_url);
	$res_bing = file_get_contents($ping_uri);
	echo "<div style='height:50px; overflow:scroll; border:1px solid #ccc; padding:5px;'>".$res_bing."</div>";
}

// send mail reporting *************************************************************************
if($sitemap_mail_reporting)
{
	echo "<hr><h3>Sending email report to `$sitemap_mail_admin`</h3><hr>";

	$plugin->mailCharset('UTF8');
	$plugin->mailFrom($sitemap_mail_from);
	$plugin->mailSubject($sitemap_mail_subject);

	// body
	$body = "<h2 style=\"border-bottom:1px solid #DE51AD;\">Sitemap Total pages for `<a href=\"".WEBSITE_URL."\">".WEBSITE_URL."</a>` : $total</h2>";
	$body .= "<h3> &bull; Automatic pages: $total_pages_automatic</h3>";
	$body .= "<h3> &bull; Custom pages: $total_pages_custom</h3>";
	$body .= "<h3> &bull; Manual pages: $total_pages_special</h3>";
	$body .= '<table border="0" cellpadding="5" cellspacing="1" style="border:1px solid #ccc; background-color:#ccc;">';

	for($i=0; $i < count($sitemap_special_page); $i++)
	{
		$body .= '<tr>';
		$body .= '<td style="background-color:#e5e5e5;">'.$sitemap_special_page[$i]['url_parent'].'</td>';
		$body .= '<td style="background-color:#fff;">'.$sitemap_special_page[$i]['url_regex'].'</td>';
		$body .= '<td style="background-color:#fff;">'.$report_page_special[$i].'</td>';
		$body .= '</tr>';
	}
	$body .= '</table>';

	$body .= '<br />';
	$body .= '<br />';

	// search engine
	$body .= "<h2 style=\"border-bottom:1px solid #DE51AD;\">Ping search engine</h2>";

	$img_yes = WEBSITE_URL.'/nuts/img/YES.gif';
	$img_no = WEBSITE_URL.'/nuts/img/icon-error.gif';

	// Google
	if(!$sitemap_ping_google)
		$body .= "<h3> &bull; Ping Google : - </h3>";
	else
	{
		$img = (!$res_google) ?  $img_no : $img_yes;
		$body .= '<h3> &bull; Ping Google : <img src="'.$img.'" align="bottom" /> </h3>';
	}
	
	// Yahoo
	if(!$sitemap_ping_yahoo)
		$body .= "<h3> &bull; Ping Yahoo : - </h3>";
	else
	{
		$img = (!$res_yahoo) ?  $img_no : $img_yes;
		$body .= '<h3> &bull; Ping Yahoo : <img src="'.$img.'" align="bottom" /> </h3>';
	}
	
	// Bing
	if(!$sitemap_ping_bing)
		$body .= "<h3> &bull; Ping Bing : - </h3>";
	else
	{
		$img = (!$res_bing) ?  $img_no : $img_yes;
		$body .= '<h3> &bull; Ping Bing : <img src="'.$img.'" align="bottom" /> </h3>';
	}


	// plugin _email template
	include_once("../_email/config.inc.php");
	$body = str_replace('[BODY]', $body, $HTML_TEMPLATE);
	$plugin->mailBody($body, 'HTML');

	$tos = explode(',', $sitemap_mail_admin);
	$tos = array_map('trim', $tos);
	foreach($tos as $to)
	{
		$plugin->mailTo($to);
		$plugin->mailSend();
	}

}


/**
 * Add node xml to sitemap file
 */
$cf_uris = array();
function sitemapAddNode($uri, $changefreq, $priority, $lastmod='')
{
	global $sitemap_filename, $fp, $cf_uris;
	if(empty($lastmod))$lastmod = date('Y-m-d');
	
	if(@in_array($uri, $cf_uris))return false;
	$cf_uris[] = $uri;

$node = <<<EOF

	<url>
       <loc>$uri</loc>
       <changefreq>$changefreq</changefreq>
       <priority>$priority</priority>
	   <lastmod>$lastmod</lastmod>
	</url>
EOF;

	fwrite($fp, $node);
	return true;
}
/**
 * Close node and close file handle
 */
function sitemapClose()
{
	global $fp;

	$node = "\n</urlset>";
	fwrite($fp, $node);
	fclose($fp);

}







?>