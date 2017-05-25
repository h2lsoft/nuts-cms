<?php
/**
 * Sitemap generator
 *
 * @version 3.0
 * @date 23/04/2013
 * @author H2lsoft
 *
 */

// configuration *************************************************************************
set_time_limit(0);
error_reporting(E_ALL);
ini_set('memory_limit', '256M');

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

if(!is_writable($sitemap_filename))
    die("Error: could not open `$sitemap_filename`");


$SITEMAP_CONTENTS = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

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
    if(!preg_match('/^http/i', $page['VirtualPagename']) && @$page['VirtualPagename'][0] != '{')
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
echo "<h4>Total normal pages found : $total_pages_automatic</h4>";



// custom pages *******************************************************************************************************
include("custom.inc.php");
$total_pages_custom = 0;
foreach($sitemap_custom as $sc)
{
    $pages_done[] = $sitemap_custom['url'];
    sitemapAddNode($sitemap_custom['url'], $sitemap_custom['changefreq'], $sitemap_custom['priority'], $sitemap_custom['modification']);
    $total_pages_custom++;
}
if($total_pages_custom)
    echo "<h4>Total custom pages found : $total_pages_custom</h4>";


// tunnel pages ********************************************************************************************************
$sql = "SELECT
				ID, Language, VirtualPagename, DATE_FORMAT(DateUpdate, '%Y-%m-%d') AS LastMod, SitemapPriority, SitemapChangefreq, SitemapUrlRegex1, SitemapUrlRegex2
		FROM
				NutsPage
		WHERE
				Deleted = 'NO' AND
				State = 'PUBLISHED' AND
				Sitemap = 'YES' AND
				SitemapPageType = 'TUNNEL' AND

				Language IN('$sel_languages') AND

				(DateStartOption = 'NO' OR (DateStartOption = 'YES' AND DateStart <= NOW())) AND
				(DateEndOption = 'NO' OR (DateEndOption = 'YES' AND DateEnd >= NOW()))

		ORDER BY
				Language,
				ZoneID,
				Position";
$plugin->doQuery($sql);

$tunnel_pages_found = 0;
$sitemap_tunnel_pages = array();
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

    $nav_pages = getAllUrl($uri, $rt['SitemapUrlRegex1']);


    // no sub page found so add parent
    if(!$nav_pages)
    {
        $sitemap_tunnel_pages[] = array('url_parent' => $uri, 'url_regex' => $rt['SitemapUrlRegex1'], 'url_regex2' => $rt['SitemapUrlRegex2'], 'priority' => $rt['SitemapPriority'],'changefreq' => $rt['SitemapChangefreq']);
    }
    else
    {
        for($i=0; $i < count($nav_pages); $i++)
        {
            $nav_pages2 = getAllUrl($nav_pages[$i], $rt['SitemapUrlRegex1']);

            $new_uri = 0;
            foreach($nav_pages2 as $pg)
            {
                if(!in_array($pg, $nav_pages))
                {
                    $nav_pages[] = $pg;
                    $new_uri++;
                }
            }

            if($new_uri > 0)
            {
                $i = -1;
            }
        }

        $nav_pages = @array_unique($nav_pages);
        @natsort($nav_pages);
        $nav_pages = array_values($nav_pages);
        foreach($nav_pages as $uri_added)
        {
            $sitemap_tunnel_pages[] = array('url_parent' => $uri_added, 'url_regex' => $rt['SitemapUrlRegex1'], 'url_regex2' => $rt['SitemapUrlRegex2'], 'priority' => $rt['SitemapPriority'],'changefreq' => $rt['SitemapChangefreq']);
        }
    }
}

// add tunnel pages and records inside *********************************************************************************
$total_pages_tunnel = 0;
$total_pages_tunnel_2 = 0;
foreach($sitemap_tunnel_pages as $page)
{
    $uri = $page['url_parent'];

    if(!in_array($uri, $pages_done))
    {
        $pages_done[] = $uri;
        sitemapAddNode($uri, $page['changefreq'], $page['priority']);
        $total_pages_tunnel++;

        // regex 2
        if(!empty($page['url_regex2']))
        {
            $sub_pages = getAllUrl($uri, $page['url_regex2']);
            foreach($sub_pages as $uri_added)
            {
                if(!in_array($uri_added, $pages_done))
                {
                    $pages_done[] = $uri_added;
                    sitemapAddNode($uri_added, $page['changefreq'], $page['priority']);
                    $total_pages_tunnel_2++;
                }
            }
        }
    }
}


if($total_pages_tunnel)
    echo "<h4>Total tunnel pages found : $total_pages_tunnel</h4>";

if($total_pages_tunnel_2)
    echo "<h4>Total tunnel level 2 pages found : $total_pages_tunnel_2</h4>";


$total = $total_pages_automatic + $total_pages_custom + $total_pages_tunnel + $total_pages_tunnel_2;
echo "<h3>Total pages found : $total</h3>";


sitemapClose();
$plugin->dbClose();



// ping search engines *************************************************************************************************
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

// send mail reporting *************************************************************************************************
if($sitemap_mail_reporting)
{
    echo "<hr><h3>Sending email report to `$sitemap_mail_admin`</h3><hr>";

    $plugin->mailCharset('UTF8');
    $plugin->mailFrom($sitemap_mail_from);
    $plugin->mailSubject($sitemap_mail_subject);

    // body
    $body = "<h2 style=\"border-bottom:1px solid #DE51AD;\">Sitemap Total pages for `<a href=\"".$sitemap_url."\">".$sitemap_url."</a>`</h2>";
    $body .= "<h3> &bull; Total normal pages found: $total_pages_automatic</h3>";
    $body .= "<h3> &bull; Total custom pages found: $total_pages_custom</h3>";
    $body .= "<h3> &bull; Total tunnel pages found: $total_pages_tunnel</h3>";
    $body .= "<h3> &bull; Total tunnel level 2 pages found: $total_pages_tunnel_2</h3>";
    $body .= "<br><h3>TOTAL : $total</h3>";


    $body .= '<table border="0" cellpadding="5" cellspacing="1" style="border:1px solid #ccc; background-color:#ccc;">';

    for($i=0; $i < count($sitemap_special_page); $i++)
    {
        $body .= '<tr>';
        $body .= '<td style="background-color:#e5e5e5;">'.$sitemap_special_page[$i]['url_parent'].'</td>';
        $body .= '<td style="background-color:#fff;">'.$sitemap_special_page[$i]['url_regex'].'</td>';
        $body .= '<td style="background-color:#fff; text-align:center; font-weight:bold;">'.$report_page_special[$i].'</td>';
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




// functions ***********************************************************************************************************
function getAllUrl($uri, $original_regex)
{
    $contents = file_get_contents($uri);
    $url_regex = str_replace(array('/', '?'), array('\\/', '\?'), $original_regex);
    $regex = '@<a.*? href=[\'"]?([^"\'\s\>]*)@i';
    preg_match_all($regex, $contents, $matches);
    $links = array_unique($matches[1]);

    $tmp = array();
    foreach($links as $link)
    {
        $pattern = '@'.$url_regex.'@i';
        if(preg_match($pattern, $link))
        {
            $link = str_replace(WEBSITE_URL.'/', '', $link);
            if($link[0] == '/')$link[0] = ' ';
            $link = trim($link);
            $link = WEBSITE_URL.'/'.$link;
            $tmp[] = $link;
        }
    }

    return $tmp;
}






/**
 * Add node xml to sitemap file
 */
$cf_uris = array();
function sitemapAddNode($uri, $changefreq, $priority, $lastmod='')
{
    global $sitemap_filename, $fp, $cf_uris, $SITEMAP_CONTENTS;
    if(empty($lastmod))$lastmod = date('Y-m-d');

    if(@in_array($uri, $cf_uris))return false;
    $cf_uris[] = $uri;

    $uri = str_replace('&amp;', '&', $uri);
    $uri = str_replace('&', '&amp;', $uri);


    $node = <<<EOF
\t<url>
\t\t<loc>$uri</loc>
\t\t<changefreq>$changefreq</changefreq>
\t\t<priority>$priority</priority>
\t</url>\n
EOF;

    $SITEMAP_CONTENTS .= $node;
    return true;
}
/**
 * Close node and close file handle
 */
function sitemapClose()
{
    global $sitemap_filename, $sitemap_url, $SITEMAP_CONTENTS;

    $node = "\n</urlset>";
    $SITEMAP_CONTENTS .= $node;

    if(file_put_contents($sitemap_filename, $SITEMAP_CONTENTS) == false)
        die("<font color='red'>Error: cannot put contents on `$sitemap_filename`</font>\n");

    echo "<h2>Sitemap: <a href='$sitemap_url' target='_blank'>$sitemap_url</a> writed !</h2>";

}


