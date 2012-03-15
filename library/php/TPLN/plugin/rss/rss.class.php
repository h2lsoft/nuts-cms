<?php
/**
 * TPLN Rss Plugin
 * @package Template Engine
 */
class Rss extends Image
{
	/**
	* This method allows to create a Rss 2.0 feeds
	*
	* @param array $rss
	* @param array $items
	* @param string $charset
	* @param bool $write
	*
	* @return string $xml
	* @since 2.9.2
	* @author H2LSOFT */
	public function rssWrite($rss, $items, $charset='iso-8859-1', $write = true)
	{
		if(empty($charset))$charset = 'iso-8859-1';

		@header("content-type: application/xml");
		$rss['description'] = str_replace(array("&","<",">"), array('&amp;', "&lt;", "&gt;"), $rss['description']);

		$xml = '<?xml version="1.0" encoding="'.$charset.'"?>'."\n";
		$xml .= '<rss version="2.0">'."\n";
		$xml .= '<channel>'."\n";
		$xml .= '<title>'.$rss['title'].'</title>'."\n";
		$xml .= '<link>'.$rss['link'].'</link>'."\n";
		$xml .= '<description>'.$rss['description'].'</description>'."\n";
		$xml .= '<copyright>'.$rss['copyright'].'</copyright>'."\n";
		$xml .= '<generator>'.'TPLN Php Template v'.TPLN_VERSION.'</generator>'."\n";

		foreach($rss as $key => $val)
		{
			if(!in_array($key, array('title', 'link', 'description', 'copyright', 'generator')))
				$xml .= "<$key>".$val."</$key>"."\n";
		}

		// image
		$xml .= '		<image>'."\n";
		$xml .= '			<title>'.$rss['title'].'</title>'."\n";
		$xml .= '			<url>'.$rss['image_url'].'</url>'."\n";
		$xml .= '			<link>'.$rss['link'].'</link>'."\n";
		$xml .= '		</image>'."\n";

		// items
		foreach($items as $item)
		{
				$item['description'] = str_replace(array("&","<",">"), array('&amp;', "&lt;", "&gt;"), $item['description']);
				$xml .= '		<item>'."\n";
				$xml .= '			<title>'.$item['title'].'</title>'."\n";
				$xml .= '			<link>'.$item['link'].'</link>'."\n";
				$xml .= '			<pubDate>'.$item['pubDate'].'</pubDate>'."\n";
				$xml .= '			<description>'.$item['description'].'</description>'."\n";

				foreach($item as $key => $val)
				{
					if(!in_array($key, array('title', 'link', 'pubDate', 'description')))
						$xml .= "			<$key>".$val."</$key>"."\n";
				}

				$xml .= '		</item>'."\n";
		}

		$xml .= '	</channel>'."\n";
		$xml .= '</rss>'."\n";

		// exit or not ?
		if($write)
			echo $xml;
		else
			return $xml;
	}

}


?>