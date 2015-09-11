<?php
/**
 * TPLN template engine - main class
 *
 * @package Template Engine
 * @website http://tpln.sourceforge.net
 * @license LGPL
 */

// Inclusion of the configuration file and the Db
define('TPLN_PATH', dirname(__FILE__));

include_once('TPLN_Cfg.php');
if(TPLN_DBUG_CLASS)include_once('dBug.php');
include_once('lang/error_'.TPLN_LANG.'.inc.php'); // language file
include_once('plugin/form/lang.inc.php'); // form language file

/******************* AUTO SECURITY *******************************************/
function tpln_auto_security($value, $urldecode_before=false, $sanitize=true, $strip_tags_allowed='')
{
	if(is_array($value))
	{
		foreach($value as $key => $val)
			$value[$key] = tpln_auto_security($val, $urldecode_before, $sanitize, $strip_tags_allowed);
		return $value;
	}

	if($urldecode_before) $value = urldecode($value);

    // no comment
    $value = str_ireplace(array('<!--', '-->'), '', $value);



    // tpln code
    $value = str_ireplace('{#', '{ #', $value);
    $value = str_ireplace('{_', '{ _', $value);
    $value = str_ireplace('{$', '{ $', $value);

	// remove invisble characters
	$non_displayables = array();
	if($urldecode_before)
	{
		$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
		$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
	}
	$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

	do{
			$value = preg_replace($non_displayables, '', $value, -1, $count);
	}
	while($count);


	// naughty scripting
	$value = preg_replace('#(alert|cmd|passthru|eval|shell_exec|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#i',
					'[XSS-PROTECT:\\1] &#40;\\3&#41;', $value);

	// never allowed
	$_never_allowed_str =	array('document.cookie', 'document.write', '.parentNode', '.innerHTML', 'window.location', '-moz-binding');
	$value = str_replace($_never_allowed_str, '[REMOVED]', $value);

	$_never_allowed_regex = array('javascript\s*:', 'expression\s*(\(|&\#40;)', 'vbscript\s*:', 'Redirect\s+302', "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?");
	foreach($_never_allowed_regex as $reg)
		$value = preg_replace('#'.$reg.'#is', '[removed]', $value);

	// JSON
	if(!$urldecode_before && strlen($value) >= 2 && $value[0] == '[' && $value[strlen($value)-1] == ']')
	{
		$sanitize = false;
		$strip_tags_allowed = '';
	}

	if($sanitize)$value = trim(filter_var($value, FILTER_SANITIZE_STRING));
	$value = strip_tags($value, $strip_tags_allowed);


	// remove php code
	$value = preg_replace('/(<\?{1}[p\s]{1}.+\?>)/i', '', $value);
	$value = str_ireplace(array('<?php', '<%', '<?=', '<?', '?>'), '', $value);

	// remove xss
	$value = str_ireplace(array("&lt;", "&gt;"), array("&amp;lt;", "&amp;gt;"), $value);
	$value = preg_replace('#(&\#*\w+)[\s\r\n]+;#U', "$1;", $value);
	$value = preg_replace('#(<[^>]+[\s\r\n\"\'])(on|xmlns)[^>]*>#iU', "$1>", $value);
	$value = preg_replace('#(<[^>]+[\s\r\n\"\'])(on|xmlns)[^>]*>#iU', "$1>", $value);
	$value = preg_replace('#([a-z]*)[\s\r\n]*=[\s\n\r]*([\`\'\"]*)[\\s\n\r]*j[\s\n\r]*a[\s\n\r]*v[\s\n\r]*a[\s\n\r]*s[\s\n\r]*c[\s\n\r]*r[\s\n\r]*i[\s\n\r]*p[\s\n\r]*t[\s\n\r]*:#iU', '$1=$2nojavascript...', $value);
	$value = preg_replace('#([a-z]*)[\s\r\n]*=([\'\"]*)[\s\n\r]*v[\s\n\r]*b[\s\n\r]*s[\s\n\r]*c[\s\n\r]*r[\s\n\r]*i[\s\n\r]*p[\s\n\r]*t[\s\n\r]*:#iU', '$1=$2novbscript...', $value);
	$value = preg_replace('#(<[^>]+)style[\s\r\n]*=[\s\r\n]*([\`\'\"]*).*expression[\s\r\n]*\([^>]*>#iU', "$1>", $value);
	$value = preg_replace('#(<[^>]+)style[\s\r\n]*=[\s\r\n]*([\`\'\"]*).*s[\s\n\r]*c[\s\n\r]*r[\s\n\r]*i[\s\n\r]*p[\s\n\r]*t[\s\n\r]*:*[^>]*>#iU', "$1>", $value);
	$value = preg_replace('#</*\w+:\w[^>]*>#i', "", $value);
	do
	{
		$oldstring = $value;
		$value = preg_replace('#</*(style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $value);
	}
	while ($oldstring != $value);

	$value = trim($value);
	return $value;
}


if(!defined('TPLN_AUTO_SECURITY_GET') || TPLN_AUTO_SECURITY_GET == 1)$_GET = tpln_auto_security($_GET, true, true);
if(!defined('TPLN_AUTO_SECURITY_POST') || TPLN_AUTO_SECURITY_POST == 1)$_POST = tpln_auto_security($_POST, false, false, '<h1><h2><h3><h4><h5><h6><a><img><img/><ul><ol><li><caption><p><br><br /><div><span><table><th><tr><td><thead><tfoot><cite><blockquote><pre><abbr><address><hr><audio><video><fieldset><label><map><area><article><code><col><colgroup><dd><dl><dt><kbd><var><strong><b><em><i><samp><pre><source><sub><article><section><small><mark><figcaption><figure><track><source><span><summary>');
if(!defined('TPLN_AUTO_SECURITY_COOKIE') || TPLN_AUTO_SECURITY_COOKIE == 1)$_COOKIE = tpln_auto_security($_COOKIE, true, true);

/******************* /AUTO SECURITY *******************************************/





/******************* Plugins structure *******************************************
- TPLN
|- DB
|- Form
|- Mail
|- Rss
 ************************************************************************************/
include_once('plugin/image/image.class.php');
include_once('plugin/rss/rss.class.php');
include_once('plugin/mail/mail.class.php');
include_once('plugin/form/form.class.php');
include_once('plugin/db/db.class.php');

/******************* File structure **********************************************

f - f_no - name                - string     // file name
- cache_name          - string     // file name for cache
- buffer               - string     // file content
- items                - array      // items in file
- constant_items	   - array		// constants in file
- php_items            - array      // php items in file (begin with $)
- cmd_items            - array      // include items
- create_cached_file   - bool       // file from cache
- time_started         - long int   // chrono start
- cache_expire         - bool       // cache exporation
- execution_time       - long int   // time execution
- chrono_started       - long int   // chrono started

- shortcut_blocs        - array
|_ all              - array      // all bloc names
|_ used             - array      // all bloc used
|_ name             - none
|_ items       		- array      // bloc items

- def_blocs            - array

 */

/******************* Bloc Structure ***************************************

def_blocs - name          - string
|_ structure          - string
|_ parsed             - array
|_ is_looped          - boolean
|_ children           - array

 **************************************************************************************/
class TPLN extends DB
{
	protected $def_tpl = array(); // templates list defined with a name

	/**
	 * @var int index of template
	 */
	public $f_no = -1; // index of file
	/**
	 * @var int index of virtual template
	 */
	public $vf_no = -1; // index of virtual file
	/**
	 * @var array template properties
	 */
	public $f = array(); // array which contains all the properties


	protected $chrono_type = array(); // chrono of ALL or not ?
	protected $HTMLCompress = 0; // activate the HTML compression of the exit ?
	protected $blocs_double_exceptions = array('start', 'end', 'pager', 'in', 'out', 'previous', 'next'); // allows blocs in double

	// Error
	public $error_msg = '';
	protected $error_signaled = array();

	// debugage
	protected $struct_mode = 0;
	protected $struct_tab = array(); // contains array entirety

	/**
	 * TPLN constructor
	 * @author H2LSOFT
	 */
	public function TPLN()
	{
		// looks at the existence of the parameter by default
		if(TPLN_DEFAULT_IND == 1)
		{
			if(!is_dir(TPLN_DEFAULT_PATH))
			{
				if(!@mkdir(TPLN_DEFAULT_PATH, 0755))
				{
					$this->error(7.2);
					return;
				}

				clearstatcache();
			}
		}




	}

	/**
	 * this method allows to use plugin for TPLN.
	 *
	 * @param string
	 *
	 * @deprecated
	 *
	 * @since 2.8
	 * @see Plugin form
	 * @author H2LSOFT
	 */
	public function loadPlugin($name)
	{
		/*
	$name = strtolower($name);

	$tmp = TPLN_PATH."/plugin/$name/$name.class.php";
	include_once($tmp);
	// PHP's version // création dynmique d'objet
	$_php_ver = (float) PHP_VERSION;
	if($_php_ver < 5.0)
	aggregate($this, $name);
	else
	{
		die('PHP version invalid no support for PHP 5');
		// Création dynamique d'objet sans la fonction aggregate
	}
	* @author H2LSOFT */
	}

	/**
	 * This method allows to compress the template.
	 *
	 * @param boolean $bool
	 * @since 2.4
	 * @author H2LSOFT
	 */
	public function htmlCompress($bool)
	{
		$this->htmlCompress = $bool;
	}

	/**
	 * This method allows to display the countain of a php variable to facilite debugging
	 *
	 * @param string $label
	 * @param string $data
	 * @param boolean $return
	 *
	 * @return string
	 * @since 2.6
	 * @author H2LSOFT
	 */
	public function dump($label = 'Debug', $data, $return = 0)
	{
		$format_html = 1;
		($format_html) ? $v = '<pre><div style="text-align:left;margin:5px;border:1px solid #CCCCCC;background-color:#E5E5E5;">' : $v = '<pre>';
		if(!empty($label))
		{
			if($format_html)$v .= '<div style="text-align:left;padding:5px;background-color:#CCCCCC;">';
			$v .= "<b>$label => </b> ";
			if($format_html)$v .= '</div>';
		}
		$v .= print_r($data, 1);
		($format_html) ? $v .= '</div></pre>' : $v .= '</pre>';

		if($return)
			return $v;
		else
			echo $v;

		return;
	}


    /**
     * Get browser information
     * @param string $u_agent (if empty user $_SERVER['HTTP_USER_AGENT'])
     * @return array
     */
    public function getBrowserInfo($u_agent="")
    {
        if(empty($u_agent))$u_agent = @$_SERVER['HTTP_USER_AGENT'];

        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        }
        elseif (preg_match('/Windows 95|Win95|Windows_95/i', $u_agent))$platform = 'Windows 95';
        elseif (preg_match('/Windows 98|Win98/i', $u_agent))$platform = 'Windows 98';
        elseif (preg_match('/Windows NT 5.0|Windows 2000/i', $u_agent))$platform = 'Windows 2000';
        elseif (preg_match('/Windows NT 5.1|Windows xp/i', $u_agent))$platform = 'Windows XP';
        elseif (preg_match('/Windows NT 5.2/i', $u_agent))$platform = 'Windows Server 2003';
        elseif (preg_match('/Windows NT 5.2|Windows NT 6.0/i', $u_agent))$platform = 'Windows Vista';
        elseif (preg_match('/Windows NT 6.1/i', $u_agent))$platform = 'Windows 7';
        elseif (preg_match('/Windows NT 6.2/i', $u_agent))$platform = 'Windows 8';

        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i > 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );

    }



	/**
	 * This method returns ip used.
	 *
	 * @return string
	 * @author H2LSOFT */
	public function getIP()
	{
		/*if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else*/if(isset($_SERVER['HTTP_CLIENT_IP']))
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	else
		$ip = $_SERVER['REMOTE_ADDR'];

		return $ip;
	}

	/**
	 * this method activates template structure
	 *
	 * @author H2LSOFT
	 */
	public function fileStructMode()
	{
		$this->struct_mode = 1;
	}

	/**
	 * this method
	 *
	 * @author H2LSOFT
	 */
	public function traceMode()
	{
		$this->trace_mode = 1;
	}

	/**
	 *
	 * this method gets opened template
	 *
	 * @author H2LSOFT
	 */
	protected function structOpen()
	{
		if($this->struct_mode == 0)
		{
			return;
		}

		$this->structFile();
		$this->structItems();
		$this->structBlocs();
		$this->arr2HtmlTab();
	}

	/**
	 * this method returns file structure
	 *
	 * @author H2LSOFT
	 */
	protected function structFile()
	{
		$this->struct_tab[] = "<b>File:</b> {$this->f[$this->f_no]['name']}<br>\n";
	}

	/**
	 * this method returns item structure
	 *
	 * @author H2LSOFT
	 */
	protected function structItems()
	{
		$p_var = array('_UrlBng',
			'_UrlPrev',
			'_UrlNext',
			'_UrlEnd',
			'_UrlPageNav',
			'_PageNumber',
			'_PageCount',
			'_First',
			'_Last',
			'_Count',
			'_NavColor',
			'_Chrono',
			'_Logo',
			'_Version',
			'_Field',
			'_QueryCount');

		$tab = '<b>Variable(s) found:</b> '.count($this->f[$this->f_no]['items'])."<br>\n";

		$cur_item = array(); // initialisation
		$tpln_var = array(); // initialisation
		if(count($this->f[$this->f_no]['items']) > 0)
		{
			$item_tmp = array_unique($this->f[$this->f_no]['items']);

			foreach($item_tmp as $name)
			{
				if(!in_array($name, $p_var))
				{
					$cur_item[] = $name;
				}
				else
				{
					$tpln_var[] = $name;
				}
			}
		}

		$tab .= $this->arr2List('Constants variable(s)', $this->f[$this->f_no]['php_items'], '{CONST::', '}');
		$tab .= $this->arr2List('Php variable(s)', $this->f[$this->f_no]['php_items'], '{$', '}');
		$tab .= $this->arr2List('User variable(s)', $cur_item, '{', '}');
		$tab .= $this->arr2List('Private variable(s)', $tpln_var, '{', '}');
		$tab .= $this->arr2List('Tpln Include Command(s)', $this->f[$this->f_no]['cmd_items'], '{#include(', ')}');

		$this->struct_tab[] = $tab;
	}

	/**
	 * this method returns block structure
	 *
	 * @author H2LSOFT */
	protected function structBlocs()
	{
		$this->struct_tab[] = $this->arr2List('Blocs', $this->f[$this->f_no]['shortcut_blocs']['all']);
	}

	/**
	 * this method changes array to list
	 *
	 * @param string $text
	 * @param string $arr
	 * @param string $bng
	 * @param string $end
	 *
	 * @return string
	 * @author H2LSOFT */
	protected function arr2List($text = '', $arr, $bng = '', $end = '')
	{
		$txt = null;

		if(!empty($text))
			$txt .= "<b>$text:</b> ".count($arr).'<br>';

		if(count($arr) > 0)
		{
			$txt .= '<ul>';

			foreach($arr as $name)
			{
				$txt .= '<li>'.$bng.$name.$end."</li>\n";

				if($text == 'Blocs')
				{
					if(count($this->f[$this->f_no]['shortcut_blocs'][$name]['items']) > 0)
					{
						$txt .= '<ul>';

						foreach($this->f[$this->f_no]['shortcut_blocs'][$name]['items'] as $item)
							$txt .= "<li>\{$item}</li>";

						$txt .= '</ul>';
					}
				}
			}

			$txt .= '</ul>';
		}

		return $txt;
	}

	/**
	 * this method changes array to html table
	 *
	 * @author H2LSOFT */
	protected function arr2HtmlTab()
	{
		for($i = 0;$i < count($this->struct_tab);$i++)
		{
			if($i == 0)
				echo '<table width="100%" border="1" cellspacing="0" cellpadding="3">';

			echo "<tr><td>{$this->struct_tab[$i]}</td></tr>";

			if($i == count($this->struct_tab)-1)
				echo '</table>';
		}
	}

	/**
	 * this method triggers TPLN error
	 *
	 * @global int $_err
	 * @param string $err_no
	 * @param string $file
	 * @param string $bloc
	 * @param string $item
	 * @author H2LSOFT */
	protected function error($err_no, $file = '', $bloc = '', $item = '')
	{
		global $_err;

		$err_no = str_replace(',', '.', $err_no);

		$err_msg = $_err["$err_no"];
		$err_msg = str_replace(

			array('[:FILE:]', '[:BLOC:]', '[:ITEM:]'),
			array($file, $bloc, $item),
			$err_msg);

		$this->error_msg = "<B>TPLN error $err_no:</B> $err_msg";

		// add url clickable
		$uri = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$this->error_msg .= ' (<a href="'.$uri.'" target="_blank">'.$uri.'</a>'.')';

		// add stack
		$this->error_msg .= "<br /><br />";
		$backtrace1 = debug_backtrace();
		$backtrace1 = array_reverse($backtrace1);
		if(count($backtrace1) > 0)
		{
			$this->error_msg .= "<b>Stack</b>\n";
			$this->error_msg .= "<pre style='border:1px solid #ccc; padding:5px;'>";

			$init = false;
			foreach($backtrace1 as $k => $v)
			{
				if(!$init)
					$this->error_msg .= "<b> &bull; {$v['file']} in line {$v['line']}</b>\n";
				else
					$this->error_msg .= " &bull; {$v['file']} in line {$v['line']}\n";

				$init = true;
			}

			$this->error_msg .= "</pre>\n\n";
		}

		// assign use handler
		if(in_array($err_no, array(0, 9, 10, 11, 12, 13)))
			$this->error_user_level = E_USER_ERROR;
		elseif(in_array($err_no, array( 8, 13)))
			$this->error_user_level = E_USER_WARNING;
		else
			$this->error_user_level = E_USER_NOTICE;

		$this->outPutMessage();
	}

	/**
	 * this method sends mail admin alert
	 *
	 * @author H2LSOFT */
	protected function mailAlert()
	{
		if($this->error_user_level != E_USER_ERROR)return;

		$err_alert = TPLN_ERROR_ALERT;
		$mail_admin = TPLN_MAIL_ADMIN;

		if(($err_alert == 1 && !empty($mail_admin)) && (!isset($_GET['tpln_w']) || $_GET['tpln_w'] != 'adm'))
		{
			$request_url_simple = str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
			$url = 'http://'.$_SERVER['HTTP_HOST'].$request_url_simple;

			// have a query string ?
			if(empty($_SERVER['QUERY_STRING']))
				$url .= '?tpln_w=adm';
			else
				$url .= '?'.$_SERVER['QUERY_STRING'].'&tpln_w=adm';

            $subject = (defined('WEBSITE_PATH')) ? '[NUTS] Alert Error' : '[TPLN] Alert Error';

            $err_msg = $this->error_msg;
			$err_msg = str_replace('&lt;', '<', $err_msg);
			$err_msg = str_replace('&gt;', '>', $err_msg);

			$body = date('[Y-m-d H:i] ')." System has detected an error\n\n";
			// $body .= $err_msg.' in '.$_SERVER['SCRIPT_FILENAME']."\n";
            $body .= $err_msg."\n";

			// $body .= "===========================================\n";

            // add session stack if exists
            if(isset($_SESSION) && count($_SESSION) > 0)
            {
                $body .= "\n<b>Session :</b>";
                $body .= "<pre style='border:1px solid #ccc; padding:5px;'>".@print_r($_SESSION, true).'</pre>'."\n";
            }
            else
            {
                $body .= "\n\n";
            }

            $body .= "<b>Url :</b> <a href=\"$url\">$url</a>\n";

            $body .= "<hr>";
            $body .= '<b>IP :</b> '.$this->GetIP()." (<a href=\"http://www.geoiptool.com/en/?IP=".$this->GetIP()."\">information</a>)"."\n";
            $body .= "<b>Server :</b> {$_SERVER['SERVER_NAME']} ({$_SERVER['SERVER_ADDR']})"."\n";
            $referer = (isset($_SERVER['HTTP_REFERER'])) ? $this->clickable($_SERVER['HTTP_REFERER']) : '-';
            $body .= "<b>Referer :</b> $referer"."\n";

            $browser = $this->getBrowserInfo();
            $body .= "<b>Browser :</b> ".@$browser['name'].' '.@$browser['version']."\n";
            $body .= "<b>System :</b> ".@$browser['platform']."\n";
            $body .= "<b>Agent :</b> ".@$_SERVER['HTTP_USER_AGENT']."\n";

            if(defined('WEBSITE_PATH'))
                $body .= '<b>Powered by Nuts Component TPLN v.'.TPLN_VERSION."</b>\n";
            else
                $body .= '<b>Powered by TPLN v.'.TPLN_VERSION."</b>\n";

            $body = str_replace("\n", "<br />", $body);

            // add trace mode for Nuts
            if(defined('WEBSITE_PATH') && @$GLOBALS['nuts']->dbIsConnected())
            {
                xTrace('system-www', strip_tags($body));
            }

            $body = '<style type="text/css">* , body {font-family: \'Segoe UI\', arial; font-size: 12px;}</style>'.$body;


			$headers = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=utf-8\n";
			$headers .= "From: ".TPLN_MAIL_EXPEDITOR."\n";
			$headers .= 'X-Mailer: PHP/'.phpversion();

			if(@mail(TPLN_MAIL_ADMIN, $subject, $body, $headers))
			{
				if(TPLN_LANG == 'en')
					$msg = "<br><br><hr>An email has been sent to the webmaster $mail_admin";
				elseif(TPLN_LANG == 'fr')
					$msg = "<br><br><hr>Un email a eacute;teacute; envoyeacute; au webmaster $mail_admin";

				$this->error_msg .= $msg;
			}
		}
	}

	/**
	 *
	 * this method displays error message
	 *
	 * @param boolean $exit
	 * @author H2LSOFT */
	protected $error_user_level;
	public function outPutMessage($exit = true)
	{
		$this->mailAlert();

		// logs of errors
		if(TPLN_ERROR_LOGS && (!@in_array($this->error_msg, $this->error_signaled)))
		{
			// check if the file exists else we create it
			if(!($fp = @fopen(TPLN_ERROR_LOGS_FILE , 'a+')))
			{
				trigger_error('Impossible to open/create '.TPLN_ERROR_LOGS_FILE, E_USER_NOTICE);
			}
			else
			{
				// day hour ,msg
				$txt = date("[Y-m-d H:i:s]	").strip_tags($this->error_msg)."\n";
				fwrite($fp, $txt);
				fclose($fp);
			}
		}

		// error signaled
		if(!@in_array($this->error_msg, $this->error_signaled))
		{
			$err_msg = $this->error_msg;

			if('TPLN_OUTPUT_CHARSET' != 'utf-8')
				$err_msg = utf8_decode($this->error_msg);

			// fatal error
			if($this->error_user_level == E_USER_ERROR && TPLN_ERROR_URI != '')
			{
				$this->redirect(TPLN_ERROR_URI);
			}
			else
			{
				trigger_error($err_msg, $this->error_user_level);
				$this->error_signaled[] = $this->error_msg;
			}
		}
	}

	/**
	 * This method allows to redirect to a new web page
	 *
	 * @param string $uri
	 * @param int $timer default 0
	 * @since 2.9
	 * @author H2LSOFT */
	public function redirect($uri, $timer=0)
	{
		if($timer > 0)
		{
			die('<meta http-equiv="refresh" content="'.$timer.';'.$uri.'" />');
		}

		if(!headers_sent())
		{
			header('Location: '.$uri);
			exit();
		}
		else
		{
			die('<script>document.location.href="'.$uri.'"</script>');
		}
	}

	/**
	 * this method initializes template variables
	 *
	 * @author H2LSOFT */
	protected function initTemplateVars()
	{

		// initialisation of variables types for EALL **************************
		$this->f[$this->f_no]['name'] = null;
		$this->f[$this->f_no]['cache_name'] = null;
		$this->f[$this->f_no]['buffer'] = null;
		$this->f[$this->f_no]['items'] = array();
		$this->f[$this->f_no]['constant_items'] = array();
		$this->f[$this->f_no]['php_items'] = array();
		$this->f[$this->f_no]['cmd_items'] = array();

		$this->f[$this->f_no]['create_cached_file'] = 0;
		$this->f[$this->f_no]['time_started'] = 0;
		$this->f[$this->f_no]['cache_expire'] = 0;
		$this->f[$this->f_no]['execution_time'] = 0;
		$this->f[$this->f_no]['chrono_started'] = 0;

		$this->f[$this->f_no]['shortcut_blocs'] = array();
		$this->f[$this->f_no]['shortcut_blocs']['all'] = array();
		$this->f[$this->f_no]['shortcut_blocs']['used'] = array();

		$this->f[$this->f_no]['shortcut_blocs']['name'] = null;
		$this->f[$this->f_no]['shortcut_blocs']['name']['items'] = array();

		$this->f[$this->f_no]['def_blocs'] = array();

		$this->chrono_type[$this->f_no] = null;
	}

	/**
	 * this method allows to open template file which can to support any extensions of file (.htm, .html, .tpl, .php ... ).
	 *
	 * if none name of file has been defined, it will open the file which have the same name
	 *
	 * as the script with extension per defect.
	 *
	 * @param string $file
	 * @param CACHED $cached
	 * @param int $cached_time
	 *
	 * @return boolean
	 * @author H2LSOFT */
	public function open($file = '', $cached = '', $cached_time = '')
	{
		$this->f_no++; // incremente
		$this->initTemplateVars(); // array's initialisation

		// add the name of file
		if(empty($file))
		{
			$file = basename($_SERVER['PHP_SELF']);
			// delete the extension
			preg_match("/\.([^\.]*$)/", $file, $elts);

			if(count($elts) > 1)
				$file = str_replace($elts[count($elts)-1], TPLN_DEFAULT_EXT, $file);
		}

		// check the existence of extension and the directory per defect
		if(TPLN_DEFAULT_IND)
		{
			if(!preg_match("/\./", basename($file)))
			{
				$file .= '.'.TPLN_DEFAULT_EXT;
			}// puts the extension per defect if it's not exists
			$file = TPLN_DEFAULT_PATH.'/'.$file; // puts the directory per defect
		}

		$this->f[$this->f_no]['name'] = $file;

		$time = explode(' ', microtime());
		$this->f[$this->f_no]['time_started'] = time();
		$this->f[$this->f_no]['chrono_started'] = $time[1] + $time[0];

		// define the chrono
		if(!defined('TPLN_CHRONO_STARTED'))
		{
			$chrono = $time[1] + $time[0];
			define('TPLN_CHRONO_STARTED', $chrono);
		}

		// if there is caching file
		if(!empty($cached))
		{
			// the ? is replaced by -- in the caching file
			if(strpos($file, '?'))
				$this->f[$this->f_no]['name'] = substr($this->f[$this->f_no]['name'], 0, strpos($file, '?'));

			$this->f[$this->f_no]['cache_name'] = str_replace('?', '---', $file);
			$this->cacheDirExists();

			if(empty($cached_time))
			{
				$cached_time = TPLN_CACHE_TIME;
			}
			$this->f[$this->f_no]['cache_expire'] = $this->f[$this->f_no]['time_started'] + $cached_time; // caching expiry

			// check if the file is still caching

			// file exists && its date of creation <= time of caching
			if(file_exists(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['cache_name']) && $this->inCachePeriod())
			{
				$this->getCachedFile();
				return true;
			}
			else
			{
				$this->f[$this->f_no]['create_cached_file'] = 1;
				$this->f[$this->f_no]['buffer'] = $this->getFile($this->f[$this->f_no]['name']);
			}
		}
		else
			$this->f[$this->f_no]['buffer'] = $this->getFile($this->f[$this->f_no]['name']);

		// remplaces the constants
		$this->parseConstants();

		// remplaces the variables with $
		if(TPLN_PARSE_GLOBALS)$this->ParseGlobals();
		// remplaces the files to include
		$this->captureIncludeCmd(); // captures the commandes include

		// parsing and evaluation if necessary
		if(count($this->f[$this->f_no]['cmd_items']) > 0)
		{
			$this->parseAllIncludeCmd();
			$this->f[$this->f_no]['cmd_items'] = array(); // delete
		}


		$this->parsePhpCommands();


		// replaces the $ if contained in includind files
		if(TPLN_PARSE_GLOBALS)$this->parseGlobals();
		$this->f[$this->f_no]['shortcut_blocs']['all'] = $this->captureAllBlocs();
		$this->endBlocVerify();
		$this->dualBlocVerify();

		// captures of the items after checking of blocs
		$this->f[$this->f_no]['items'] = $this->captureItems();
		$this->captureItemsInEachBloc();

		// debugger
		$this->structOpen();

		if($this->f[$this->f_no]['create_cached_file'] == 1)return false;
	}

	/**
	 * this method replaces all php constants in template
	 *
	 * @author H2LSOFT */
	public function parseConstants()
	{
		$this->f[$this->f_no]['constant_items'] = $this->captureItems('', 'CONSTANT');
		if(count($this->f[$this->f_no]['constant_items']) == 0)return;

		$defined_constants = get_defined_constants();

		foreach($this->f[$this->f_no]['constant_items'] as $item)
		{
			$this->f[$this->f_no]['buffer'] = $this->replaceItem('CONST::'.$item, $defined_constants[$item], $this->f[$this->f_no]['buffer']);
		}
	}

	/**
	 * this method allows to delete the blocks.
	 *
	 * @since 2.2
	 * @see EraseBloc(), EraseItem().
	 * @author H2LSOFT */
	public function cleanBlocs()
	{
		for($i = 0; $i < count($this->f[$this->f_no]['shortcut_blocs']['all']); $i++)
		{
			$this->f[$this->f_no]['buffer'] = str_replace(
				array(
					"<bloc::{$this->f[$this->f_no]['shortcut_blocs']['all'][$i]}>",
					"</bloc::{$this->f[$this->f_no]['shortcut_blocs']['all'][$i]}>"
				), '', $this->f[$this->f_no]['buffer']); // bloc before/after
		}
	}

	/**
	 * this method allows to create a virtual template.
	 *
	 * @param string $countain
	 *
	 * @return boolean
	 * @author H2LSOFT */
	public function createVirtualTemplate($countain)
	{
		$this->vf_no++; // increments
		$this->f_no++; // increments
		//$this->f_no = count($this->def_tpl) + 1; // increments

		// initialisation of variables types for EALL **************************
		$this->f[$this->f_no]['name'] = 'Virtual'.$this->vf_no;
		$this->f[$this->f_no]['buffer'] = $countain;
		$this->f[$this->f_no]['items'] = array();

		$this->f[$this->f_no]['constant_items'] = array();
		$this->f[$this->f_no]['php_items'] = array();
		$this->f[$this->f_no]['cmd_items'] = array();

		$this->f[$this->f_no]['create_cached_file'] = 0;
		$this->f[$this->f_no]['time_started'] = 0;
		$this->f[$this->f_no]['cache_expire'] = 0;
		$this->f[$this->f_no]['execution_time'] = 0;
		$this->f[$this->f_no]['chrono_started'] = 0;

		$this->f[$this->f_no]['shortcut_blocs'] = array();
		$this->f[$this->f_no]['shortcut_blocs']['all'] = array();
		$this->f[$this->f_no]['shortcut_blocs']['used'] = array();

		$this->f[$this->f_no]['shortcut_blocs']['name'] = null;
		$this->f[$this->f_no]['shortcut_blocs']['name']['items'] = array();

		$this->f[$this->f_no]['def_blocs'] = array();

		$this->chrono_type[$this->f_no] = null;

		$time = explode(' ', microtime());
		$this->f[$this->f_no]['time_started'] = time();
		$this->f[$this->f_no]['chrono_started'] = $time[1] + $time[0];

		// defines the chrono
		if(!defined('TPLN_CHRONO_STARTED'))
		{
			$chrono = $time[1] + $time[0];
			define('TPLN_CHRONO_STARTED', $chrono);
		}

		$this->parseConstants();

		// replaces the variables with $
		if(TPLN_PARSE_GLOBALS)$this->ParseGlobals();

		// replaces the files to include
		$this->captureIncludeCmd(); // captures the include commands

		// parsing and evaluation if necessary
		if(count($this->f[$this->f_no]['cmd_items']) > 0)
		{
			$this->parseAllIncludeCmd();
			$this->f[$this->f_no]['cmd_items'] = array(); // delete
		}
		// replaces the $ if there are contained in including files
		if(TPLN_PARSE_GLOBALS) $this->ParseGlobals();

		$this->f[$this->f_no]['shortcut_blocs']['all'] = $this->captureAllBlocs();
		$this->endBlocVerify();
		$this->dualBlocVerify();

		// Captures the items after cheking the blocs
		$this->f[$this->f_no]['items'] = $this->captureItems();
		$this->captureItemsInEachBloc();

		// debugger
		$this->structOpen();

		if($this->f[$this->f_no]['create_cached_file'] == 1)
			return false;
	}

	/**
	 * this method is a macro which allows to open and to write a template.
	 *
	 * @param string $file
	 * @author H2LSOFT */
	public function directIoWrite($file = '')
	{
		$c_id = $this->f_no; // saves the actual number
		$this->open($file);
		$this->write();
		$this->f_no = $c_id; // puts the number
	}

	/**
	 * this method is a macro wich allows to open and to return a template.
	 *
	 * @param string $file
	 *
	 * @return string
	 * @author H2LSOFT */
	public function directIoOutput($file = '')
	{
		$c_id = $this->f_no; // saves the actual number
		$this->open($file);
		$output = $this->outPut();
		$this->f_no = $c_id; // puts the number

		return $output;
	}

	/**
	 * this method is a macro which allows to open and to save a template.
	 *
	 * @param string $file
	 * @param string $path
	 * @author H2LSOFT */
	public function directIOSave($file = '', $path)
	{
		$c_id = $this->f_no; // saves the actual number
		$this->open($file);
		$this->saveTemplate($path);
		$this->f_no = $c_id; // puts the number
	}

	/**
	 * this method allows to open and to name various templates.
	 *
	 * @param array $arr
	 *
	 * @author H2LSOFT */
	public function defineTemplate($arr)
	{
		if(!is_array($arr))
		{
			$this->error(12);
			return;
		}

		foreach($arr as $key => $val)
		{
			$this->open($val);
			$this->def_tpl[count($this->f)-1] = $key;
		}
	}

	/**
	 * this method allows to select a template which has been opened by the Open()
	 *
	 * or DefineTemplate() methods is by a name or a number.
	 *
	 * @param string $key
	 *
	 * @deprecated
	 * @since 1.4
	 * @author H2LSOFT */
	public function setTemplate($key)
	{
		$this->changeTemplate($key);
	}

	/**
	 * this method allows you to select a template opened by Open()
	 *
	 * or DefineTemplate() methods either by name or by number.
	 *
	 * @param string $key
	 * @since 2.9
	 * @see getTemplateID
	 * @author H2LSOFT */
	public function setTemplateID($key)
	{
		$this->changeTemplate($key);
	}

	/**
	 * This method returns curent template index
	 *
	 * @return int template index
	 */
	public function getTemplateID()
	{
		return $this->f_no;

	}

	/**
	 * this method allows to select a template opened by Open()
	 *
	 * or DefineTemplate() methods either by name or by number.
	 *
	 * @param string $key
	 *
	 * @author H2LSOFT */
	public function changeTemplate($key)
	{
		if(is_string($key))
		{
			// taks the id
			if(!in_array($key, $this->def_tpl, true))
			{
				$this->error(11, $key);
				return;
			}

			$key = array_search($key, $this->def_tpl, true);
		}

		if(is_int($key))
		{
			if($key < 0 || $key >= count($this->f))
			{
				$this->error(10, $key);
			}
		}

		$this->f_no = $key;
	}

	/**
	 * this method allows to parse a variable Php by a variable contained in the template.
	 * @since 2.1, 2.4
	 * @see Parse(), FastParse().
	 * @author H2LSOFT */
	public function parseGlobals()
	{

		$this->f[$this->f_no]['php_items'] = $this->captureItems('', 'PHP');

		if(count($this->f[$this->f_no]['php_items']) == 0)
		{
			return;
		}

		// @extract($GLOBALS, EXTR_SKIP || EXTR_REFS);
		foreach($this->f[$this->f_no]['php_items'] as $item)
		{
			$replace = '$'.$item;
			$tmp = '';
			if(strpos($replace, "(") !== true) // protection security of methods and functions !
			{

				// patch security: 26/08/2015 !
				preg_match_all("/\[([^\]]*)\]/s", $replace, $brakets);
				if(count($brakets[1]) == 0)
				{
					// object detected $obj->attr
					if(strpos($item, '->') !== false)
					{
						$v = explode('->', $item);
						if(count($v) == 2 && isset($GLOBALS[$v[0]]))
						{
							$tmp = $GLOBALS[$v[0]]->{$v[1]};
						}
					}
					else
					{
						if(isset($GLOBALS[$item]))
							$tmp = $GLOBALS[$item];

					}

				}
				elseif(count($brakets[1]) == 1)
				{
					$brakets[1][0] = str_replace(array('"', "'"), '', $brakets[1][0]);
					$brakets[1][0] = trim($brakets[1][0]);

					list($arr_name, ) = explode('[', $item);
					if(isset($GLOBALS[$arr_name][$brakets[1][0]]))
							$tmp = $GLOBALS[$arr_name][$brakets[1][0]];
				}
				elseif(count($brakets[1]) == 2)
				{
					$brakets[1][0] = trim(str_replace(array('"', "'"), '', $brakets[1][0]));
					$brakets[1][1] = trim(str_replace(array('"', "'"), '', $brakets[1][1]));

					list($arr_name, ) = explode('[', $item);
					if(isset($GLOBALS[$arr_name][$brakets[1][0]][$brakets[1][1]]))
							$tmp = $GLOBALS[$arr_name][$brakets[1][0]][$brakets[1][1]];
				}

				/*
				@eval("
						\$tmp = str_ireplace(array('<?php','<?','?>', '<%'), '', $replace);
				");

				if(strpos($replace, '$_GET') !== false || strpos($replace, '$_POST') !== false || strpos($replace, '$_COOKIE') !== false || strpos($replace, '$_SESSION') !== false)
				{
					$tmp = $this->xssProtect($tmp);
				}
				*/


				$item = '$'.$item;
				$item = str_replace('$', '\$', $item);
				$item = str_replace('[', '\[', $item);
				$item = str_replace(']', '\]', $item);
				$this->f[$this->f_no]['buffer'] = $this->replaceItem($item, $tmp, $this->f[$this->f_no]['buffer']);
			}
		}
	}

	/**
	 * this method allows to substitute a variable defined inside the template file.
	 *
	 * TPLN use a style javascript pseudo object language to access to the variable by the separator ".".
	 *
	 * @param string $path
	 * @param string $replace
	 * @param string $functions
	 * @see ParseBloc()
	 * @author H2LSOFT */
	public function parse($path, $replace, $functions = '')
	{
		// formatting function of data
		$replace = $this->applySpecialFunction($replace, $functions);

		if($this->isBlocDesired($path)) // is it a bloc ?

		{
			$item = $this->getItem($path); // taks the item
			$fathers_arr = $this->getFathers($path, 'ARRAY'); // les p�res ds un array
			$bloc = $this->getFather($path); // on prend le p�re

			if(!$this->blocExists($bloc))
				$this->error(2, $this->f[$this->f_no]['name'], $bloc);

			$this->itemVerify($item, $bloc); // verification

			if(!$this->isDefined($path)) // the path is saved ?
				$this->defineBloc($path); // define the blocs one by one

			$this->directParseInBloc($fathers_arr, $item, $replace);
		}
		else
		{
			$this->itemVerify($path); // verification
			$this->directParseInFile($path, $replace); // direct parsing in the file
		}
	}

	/**
	 * this method
	 *
	 * @param int $item
	 * @param int $replace
	 * @author H2LSOFT */
	protected function directParseInFile($item, $replace)
	{
		$this->f[$this->f_no]['buffer'] = $this->replaceItem($item, $replace, $this->f[$this->f_no]['buffer']);
	}

	/**
	 * this method
	 *
	 * @param array $fathers_arr
	 * @param int $item
	 * @param int $replace
	 * @author H2LSOFT */
	protected function directParseInBloc($fathers_arr, $item, $replace)
	{
		$b_ref = & $this->f[$this->f_no]['def_blocs'];
		// reachs the block
		for($i = 0;$i < count($fathers_arr);$i++)
		{
			$b_ref = & $b_ref[$fathers_arr[$i]];

			if($i < count($fathers_arr)-1)
				$b_ref = & $b_ref['children'];
		}

		// check if there was a loop>0
		$loop_nb = count($b_ref['parsed']);
		$b_ref['parsed'][$loop_nb-1] = $this->replaceItem($item, $replace, $b_ref['parsed'][$loop_nb-1]);
	}

	/**
	 * this method is like Parse() method but it avoids to rewrite definied variables.
	 *
	 * @param string $path
	 * @param string $functions
	 * @author H2LSOFT */
	public function fastParse($path, $functions = '')
	{
		// if it's a block then replace
		if($this->isBlocDesired($path))
			$item = $this->getItem($path); // taks the last item
		else
			$item = $path;
		$this->parse($path, $GLOBALS[$item], $functions);
	}

	/**
	 * this method allows to substitute the items by the pairs keys of an array in a whole block.
	 *
	 * The 3rd parameters allows to replace entire block if no data foud in array.
	 *
	 * @param string $path
	 * @param array $arr
	 * @param string $msg
	 * @param boolean $looped
	 *
	 * @since 2.3
	 * @author H2LSOFT */
	public function loadArrayInBloc($path, $arr, $msg = '', $looped = false)
	{
		if(count($arr) == 0)
		{
			if(!$looped)
			{
				$this->parseBloc($path, $msg);
			}
			else
			{
				$this->eraseBloc($path, $msg);
				$this->loop($path);
			}
		}
		else
		{
			foreach($arr as $current_arr)
			{
				$sub_blocs = array();
				$keys = @array_keys($current_arr);
				foreach($keys as $key)
				{
					if(!is_array($current_arr[$key]))
					{
						$zpath = $path;
						if($looped)
						{
							$ttmp = explode('.', $zpath);
							$zpath = $ttmp[count($ttmp)-1];
						}

						if($this->itemExists($key, $zpath))
						{
							//echo "parse: $path.$key".'<br />';
							$this->parse("$path.$key", $current_arr[$key]);
						}
					}
					else
					{
						$sub_bloc = $key;
						$this->loadArrayInBloc($path.'.'.$sub_bloc, $current_arr[$key], $msg, true);
					}
				}
				//echo "loop: $path".'<hr />';
				$this->loop($path);
			}
		}
	}

	/**
	 * this method is applied to a block to report is looping.
	 *
	 * @param string $path
	 * @author H2LSOFT */
	public function loop($path)
	{
		// check the existence of the path
		if(!$this->isDefined($path, 'NOITEM'))
		{
			$this->error(4.1, $this->f[$this->f_no]['name'], $path);
			return;
		}

		$fathers_arr = $this->getFathers($path, 'ARRAY', 0);

		// touch the block
		$b_ref = & $this->f[$this->f_no]['def_blocs'];

		for($i = 0;$i < count($fathers_arr);$i++)
		{
			$b_ref = & $b_ref[$fathers_arr[$i]];

			if($i < count($fathers_arr)-1)
			{
				$b_ref = & $b_ref['children'];
			}
		}

		// all the following génération is replaced
		if(count($b_ref['children']) > 0)
		{
			// $level = count($fathers_arr)-1;
			$child_blocs = $this->getNextGenerationBlocs($fathers_arr);
			// encapsulate the blocks
			$this->encapsuleBlocs($fathers_arr, $child_blocs);
		}

		// add one session parse
		// increment the value of loop
		$b_ref['parsed'][] = $b_ref['structure'];
		$b_ref['is_looped'] = 1;
	}

	/**
	 * this method
	 *
	 * @param array $bloc_arr
	 * @return array
	 * @author H2LSOFT */
	protected function getNextGenerationBlocs($bloc_arr)
	{
		// touch the block
		$b_ref = & $this->f[$this->f_no]['def_blocs'];

		for($i = 0;$i < count($bloc_arr);$i++)
		{
			$b_ref = & $b_ref[$bloc_arr[$i]];

			if($i < count($bloc_arr)-1)
			{
				$b_ref = & $b_ref['children'];
			}
		}

		$b_names = array_keys($b_ref['children']);

		return $b_names;
	}

	/**
	 * this method
	 *
	 * @param array $bloc_arr
	 * @param array $child_blocs
	 * @author H2LSOFT */
	protected function encapsuleBlocs($bloc_arr, $child_blocs)
	{
		// encapsulate each child CAD
		// contracting the block if it has chidren
		// parsing at father of the son parsed
		// puts zero in the son block
		foreach($child_blocs as $children)
		{
			// touch the block
			$b_ref = & $this->f[$this->f_no]['def_blocs'];

			for($i = 0;$i < count($bloc_arr);$i++)
			{
				$b_ref = & $b_ref[$bloc_arr[$i]];

				if($i == count($bloc_arr)-1)
					$father_parsed = & $b_ref['parsed'][count($b_ref['parsed'])-1]; // c'est le dernier

				$b_ref = & $b_ref['children'];
			}

			$children_all_parsed = & $b_ref[$children]['parsed'];
			$children_structure = & $b_ref[$children]['structure'];
			$children_parsed = '';
			// block
			if(count($children_all_parsed) == 1)
				$children_parsed = $children_all_parsed['0'];

			if(count($children_all_parsed) > 1)
			{
				for($l = 0; $l < count($children_all_parsed)-1; $l++)
					$children_parsed = $children_parsed.$children_all_parsed[$l];
			}
			// parsing at the father
			$father_parsed = $this->replaceBloc($children, $children_parsed, $father_parsed);
			// puts zero in the child block
			$children_all_parsed = array($children_structure);
			unset($children_parsed);
		}
	}

	/**
	 * this method allows to delete a variable.
	 *
	 * @param string $item
	 *
	 * @since 2.9
	 * @author H2LSOFT */
	public function eraseItem($item)
	{
		if(is_array($item))
		{
			foreach($item as $tmp)
				$this->parse($tmp, '');
		}
		else
			$this->parse($item, '');
	}

	/**
	 * this method allows you to delete a block.
	 *
	 * But if the bolck is one, the substitution will be done in the template.
	 *
	 * @param string $path
	 * @param string $msg
	 *
	 * @since 2.9
	 * @author H2LSOFT */
	public function eraseBloc($path, $msg='')
	{
		if(is_array($path))
		{
			foreach($path as $tmp)
				$this->parseBloc($tmp, $msg);
		}
		else
			$this->parseBloc($path, $msg);
	}

	/**
	 * this method allows you to return the contents of file.
	 *
	 * If the block name optional parameter is specified the method will return
	 *
	 * the contents of the block defined in the file.
	 *
	 * @param string $filename
	 * @param string $blocname
	 *
	 * @return string
	 * @author H2LSOFT */
	public function getFile($filename, $blocname = '')
	{
		if(!$fp = @fopen($filename, 'r')) // opening the file on reding
		{
			$this->error(0, $filename);
		}

		$filebuffer = @fread($fp, filesize($filename));
		@fclose($fp);
		clearstatcache(); // delete the Data buffer

		if(!empty($blocname)) // capture the block
			$filebuffer = $this->captureBloc($blocname, $filebuffer);

		return $filebuffer;
	}

	/**
	 * this method allows to substitute a whole bloc and markers also.
	 *
	 * @param string $path
	 * @param string $replace
	 * @see parse()
	 * @author H2LSOFT */
	public function parseBloc($path, $replace)
	{
		$this->pathVerify($path); // check the existence of blocks of the path

		// definition of the path
		if(!$this->isdefined($path, 'NOITEM')) $this->defineBloc($path.'.none');

		// imbricated block?
		if(!$this->isBlocDesired($path))
		{
			$this->f[$this->f_no]['buffer'] = $this->replaceBloc($path, $replace, $this->f[$this->f_no]['buffer']);
			$bloc_arr[] = $path;
		}
		else
		{
			// go to the bloc for éliminating !
			$bloc_arr = $this->getFathers($path, 'ARRAY', 0);
		}

		// delete the blok
		$b_ref = & $this->f[$this->f_no]['def_blocs'];

		// only one block ?
		if(count($bloc_arr) == 1)
		{
			$b_ref = & $b_ref[$bloc_arr[0]];
		}
		else
		{
			// delete the current block in the last father
			for($i = 0;$i < count($bloc_arr);$i++)
			{
				$b_ref = & $b_ref[$bloc_arr[$i]];

				if($i == count($bloc_arr)-2)
					$last_father_parsed = & $b_ref['parsed'][count($b_ref['parsed'])-1];

				if($i < count($bloc_arr)-1)
					$b_ref = & $b_ref['children'];
			}

			$last_father_parsed = $this->replaceBloc($bloc_arr[count($bloc_arr)-1], $replace, $last_father_parsed);
		}

		$b_ref['parsed'] = array(); // delete the session of the last block !
	}

	/**
	 * this method allows to reload variables of a block contained in the template.
	 *
	 * @param string $path
	 * @since 1.5
	 * @author H2LSOFT */
	public function reloadBlocVars($path)
	{
		$this->pathVerify($path);

		$last_bloc = $this->getItem($path);
		$this->f[$this->f_no]['shortcut_blocs'][$last_bloc]['items'] = $this->captureItems($last_bloc);
	}

	/**
	 * this method allows to substitute a variable from the template file by the contents of a file.
	 *
	 * @param string $item
	 * @param string $file
	 *
	 * @see GetBlocInFile()
	 * @author H2LSOFT */
	public function includeFile($item, $file)
	{
		// checking for one block
		$this->parse($item, "{#include(\"$file\");}"); // replace by the include command
		$this->f[$this->f_no]['cmd_items'][] = $file; // add to the list of f_cmd
	}

	/**
	 * this method allows to return a whole block from the template file.
	 *
	 * @param string $path
	 *
	 * @return string
	 *
	 * @since IncludeFile()
	 * @author H2LSOFT */
	public function getBlocInFile($path)
	{
		$this->pathVerify($path);
		$all_bloc = explode('.', $path);
		$bloc_name = $all_bloc[count($all_bloc)-1];
		$bloc = $this->captureBloc($bloc_name, $this->f[$this->f_no]['buffer']);

		return $bloc;
	}

	/**
	 * this method allows to print file the template parsed.
	 * @see Parse(), Output()
	 * @author H2LSOFT */
	public function write()
	{
		$this->out();
		echo $this->f[$this->f_no]['buffer'];
		$this->createCache(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['name']);
		if(TPLN_AUTO_UNSET_TEMPLATE) $this->initTemplateVars();
	}

	/**
	 * this method allows to return the contents of the template file parsed.
	 *
	 * @return string
	 * @author H2LSOFT */
	public function output()
	{
		$this->out();
		$this->createCache(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['name']);
		$buffer = $this->f[$this->f_no]['buffer'];
		if(TPLN_AUTO_UNSET_TEMPLATE) $this->initTemplateVars();

		return $buffer;
	}

	/**
	 * this method allows to save the contents of the template file parsed.
	 *
	 * @param string $path
	 * @see write()
	 * @author H2LSOFT */
	public function saveTemplate($path)
	{
		// add function for creating folders
		$act_dir = '';
		$all_dir = explode('/', $path);

		if(count($all_dir) > 1)
		{
			for($i = 0;$i < count($all_dir)-1;$i++)
			{
				if($i > 0)$act_dir .= '/';
				$act_dir .= $all_dir[$i];

				if(!is_dir($act_dir))
				{
					if(!@mkdir($act_dir, 0755))
					{
						$this->error(7.1, $act_dir);
						return;
					}

					clearstatcache();
				}
			}
		}

		$output = $this->Output();
		$fp = @fopen($path, 'w'); // open file
		@fwrite($fp, $output);
		@fclose($fp);
		clearstatcache(); // erase the file
	}

	/**
	 * this method applays special function in item
	 *
	 * @param string $replace
	 * @param string $functions
	 *
	 * @return string
	 * @since 2.9
	 * @author H2LSOFT */
	protected function applySpecialFunction($replace = '', $functions = '')
	{
		if(empty($functions))return $replace;

		// there is a special function ?
		$functions = explode('|', $functions);

		foreach($functions as $function_name)
		{
			if($function_name != 'B' && $function_name != 'I' && $function_name != 'U' && $function_name != 'S' && !empty($function_name))
			{
				$replace = $function_name($replace);
			}
		}
		// Bold, Italic, Underline
		if(in_array('B', $functions))$replace = "<strong>$replace</strong>";
		if(in_array('I', $functions))$replace = "<i>$replace</i>";
		if(in_array('U', $functions))$replace = "<u>$replace</u>";
		if(in_array('S', $functions))$replace = "<s>$replace</s>";

		return $replace;
	}

// replace the son in the father
	/**
	 * this method
	 *
	 * @param array $bloc
	 * @author H2LSOFT */
	protected function parseEncapsuledBlocs($bloc)
	{
		// reach the last block
		// preserve conserve the father (adress) and his son (nom, structure)
		// delete the last member of the block
		while(count($bloc) > 1)
		{
			$b_ref = & $this->f[$this->f_no]['def_blocs'];

			for($i = 0;$i < count($bloc);$i++)
			{
				$b_ref = & $b_ref[$bloc[$i]];

				if($i == count($bloc) - 2) // capture of the last father
					$father_parsed = & $b_ref['parsed'][count($b_ref['parsed'])-1];

				if($i == count($bloc) - 1) // capture of the last son

				{
					$children_name = $bloc[$i];
					$children_all_parsed = $b_ref['parsed'];
					$children_parsed = null;

					if(count($children_all_parsed) > 1)
					{
						for($j = 0;$j < count($children_all_parsed)-1;$j++)
							$children_parsed .= $children_all_parsed[$j];
					}
					else
					{
						if(count($children_all_parsed) == 1)
							$children_parsed = $children_all_parsed[0];
					}
				}

				$b_ref = & $b_ref['children'];
			}

			// replace the son in the father
			$father_parsed = $this->replaceBloc($children_name, $children_parsed, $father_parsed);

			// delete the last block
			unset($children_parsed);
			$bloc = array_slice($bloc, 0, count($bloc)-1);
		}
	}

	/**
	 * this method
	 *
	 * @param int $level
	 * @param array $bloc_arr
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function parseSubBlocs($level, $bloc_arr)
	{
		$b_ref = & $this->f[$this->f_no]['def_blocs'];

		foreach($bloc_arr as $cur_bloc)
		{
			$b_ref = & $b_ref[$cur_bloc]['children'];
		}

		if(!is_array($b_ref))return;

		$b_names = array_keys($b_ref); // contains all names of the blocks

		foreach($b_names as $bloc)
		{
			if(count($b_ref[$bloc]['children']) > 0)
			{
				$bloc_arr[] = $bloc;
				$this->parseSubBlocs($level + 1, $bloc_arr);
			}
			else
			{
				$bloc_arr = array_slice($bloc_arr, 0, $level + 1);
				$bloc_arr[] = $bloc;
				$this->parseEncapsuledBlocs($bloc_arr);
			}
		}
	}

	/**
	 * this method
	 *
	 * @author H2LSOFT */
	protected function parseBigFathers()
	{
		// takes the parsed father and replace it in the buffer
		$b_ref = & $this->f[$this->f_no]['def_blocs'];
		// takes the names or parent BIG
		$b_names = array_keys($b_ref);
		$b_parsed = ''; // initialisation

		foreach($b_names as $bloc)
		{
			if(count($b_ref[$bloc]['parsed']) == 1 && array_key_exists('0', $b_ref[$bloc]['parsed']))
				$b_parsed = $b_ref[$bloc]['parsed'][0];

			// if a block is in loop
			if(count($b_ref[$bloc]['parsed']) > 1)
			{
				// don't takes the first
				for($i = 0;$i < count($b_ref[$bloc]['parsed'])-1;$i++)
					$b_parsed .= $b_ref[$bloc]['parsed'][$i];
			}

			$this->f[$this->f_no]['buffer'] = $this->replaceBloc($bloc, $b_parsed, $this->f[$this->f_no]['buffer']);
			// unset($b_parsed);
			$b_parsed = '';
		}
	}

	/**
	 * this method
	 *
	 * @return string
	 * @author H2LSOFT */
	protected function getBigBlocs()
	{
		// touch the block
		$b_ref = & $this->f[$this->f_no]['def_blocs'];
		$b_names = array_keys($b_ref);
		return $b_names;
	}

	/**
	 * this method
	 *
	 * @author H2LSOFT */
	protected function out()
	{
		if(count($this->f[$this->f_no]['def_blocs']) > 0)
		{
			$b_ref = & $this->f[$this->f_no]['def_blocs'];
			// obtains the names of big blocks
			$big_blocs = $this->getBigBlocs();

			foreach($big_blocs as $bloc)
			{
				if(count($b_ref[$bloc]['children']) > 0)
					$this->parseSubBlocs(0, (array)$bloc);
			}

			$this->parseBigFathers(); // compress the big blocks and parse in the buffer
		}

		$this->parseAllIncludeCmd();
		$this->parseChrono();
		$this->parseLogo();
		$this->parseVersion();
		$this->parseQueryCount();

		if(TPLN_AUTO_CLEAN_BLOCS) $this->cleanBLocs();
		// compression ?
		if($this->HTMLCompress)
		{
			$this->f[$this->f_no]['buffer'] = preg_replace("/(\r\n|\n)/", '', $this->f[$this->f_no]['buffer']);
		}
	}

	/**
	 * this method captures items
	 *
	 * @param string $subject
	 * @param string $type
	 *
	 * @return string
	 * @author H2LSOFT */
	protected function captureItems($subject = '', $type = '')
	{
		if(empty($subject))
		{
			$subject = $this->f[$this->f_no]['buffer']; // in the file
			$blocs = $this->f[$this->f_no]['shortcut_blocs']['all'];
		}
		else
		{
			$subject = $this->captureBloc($subject, $this->f[$this->f_no]['buffer']);
			// capture the blocks of subjet
			$blocs = $this->captureAllBlocs($subject);
			// delete them
			foreach($blocs as $bloc)
				$subject = $this->replaceBloc($bloc, '', $subject);
		}
		// exclusion of spaces etc
		if($type == 'PHP')
		{
			$motif = "/\{\\$([^ ;\*\$\\\,\\n\\t]+)?\}/msU";
		}
		elseif($type == 'CONSTANT')
		{
			$motif = "/\{CONST::([^ ;\.\*\$\\\,\\n\\t]+)?\}/msU";
		}
		else
		{
			$motif = "/\{([^ ;\.\*\$\\\,\\n\\t]+)?\}/msU";
		}

		$match = preg_match_all($motif, $subject, $tab);

		// duplicate the array
		$one = array();

		if(count($tab[1]) > 0)
			$one = array_unique($tab[1]);

		return $one;
	}

	/**
	 * this method captures block
	 *
	 * @param string $name
	 * @param string $subject
	 *
	 * @return string
	 * @author H2LSOFT */
	protected function captureBloc($name, $subject)
	{
		if(empty($name))
		{
			$this->error(5, $this->f[$this->f_no]['name']);
			return;
		}

		$motif = "<bloc::$name>(.*)?<\\/bloc::$name>";
		$match = @preg_match("/$motif/msU", $subject, $bloc);
		// error
		if(!$match)
		{
			$this->error(2, $this->f[$this->f_no]['name'], $name);
			return;
		}

		$bloc = @$bloc[1];
		$bloc = @rtrim($bloc);

		return $bloc;
	}

	/**
	 * this method captures all blocks
	 *
	 * @param string $subject
	 * @return string
	 * @author H2LSOFT */
	protected function captureAllBlocs($subject = '')
	{
		if(empty($subject))
		{
			$subject = & $this->f[$this->f_no]['buffer'];
		}

		$motif = "<bloc::([^ ;\.\*\$\\\,\\n\\t]+)?>";
		$match = preg_match_all("/$motif/U", $subject, $blocs);

		return $blocs[1];
	}

	/**
	 * this method captures items in each block
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function captureItemsInEachBloc()
	{
		if(count($this->f[$this->f_no]['shortcut_blocs']['all']) == 0)
			return;

		foreach($this->f[$this->f_no]['shortcut_blocs']['all'] as $bloc)
			$this->f[$this->f_no]['shortcut_blocs'][$bloc]['items'] = $this->captureItems($bloc);
	}

	/**
	 * this method gets all blocks
	 *
	 * @param string $subject
	 *
	 * @return array
	 * @author H2LSOFT */
	protected function getAllBlocs($subject)
	{
		$motif = "<bloc::([^ ;\.\*\$\\\,\\n\\t]+)?>";
		preg_match_all("/$motif/U", $subject, $blocs);
		return $blocs[1];
	}

	/**
	 * this method captures include commands in the template
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function captureIncludeCmd()
	{
		$motif = "\{#include\(([^ ;\*,\\n\\t]+)?\);\}";
		$match = preg_match_all("/$motif/U", $this->f[$this->f_no]['buffer'], $tab);

		if(count($tab[1]) == 0)
			return;

		$one = array_unique($tab[1]);
		$this->f[$this->f_no]['cmd_items'] = $one;
	}

	/**
	 * this method allows to know if a variable exists in the template file.
	 *
	 * @param string $item_name
	 * @param string $bloc
	 *
	 * @return boolean
	 * @see BlocExists()
	 * @author H2LSOFT */
	public function itemExists($item_name, $bloc = '')
	{
		if(empty($bloc))
		{
			if(@in_array($item_name, $this->f[$this->f_no]['items']))
				return true;
			else
				return false;
		}
		else
		{
			if(@in_array($item_name, $this->f[$this->f_no]['shortcut_blocs'][$bloc]['items']))
				return true;
			else
				return false;
		}
	}

	/**
	 * this method verifies item
	 *
	 * @param string $item
	 * @param string $bloc
	 *
	 * @return boolean
	 *
	 * @author H2LSOFT */
	protected function itemVerify($item, $bloc = '')
	{
		if(empty($bloc))
		{
			if(!$this->itemExists($item))
			{
				$this->error(1, $this->f[$this->f_no]['name'], '', $item);
				return;
			}
		}
		else
		{
			if(!$this->itemExists($item, $bloc))
			{
				$this->error(1.1, $this->f[$this->f_no]['name'], $bloc, $item);
				return;
			}
		}
	}

	/**
	 * this method verifies path
	 *
	 * @param string $path
	 * @return boolean
	 * @author H2LSOFT */
	public function pathVerify($path)
	{
		$tab = @explode('.', $path);
		// reach the block and add the items to this block
		for($i = 0; $i < count($tab); $i++)
		{
			if(!$this->blocExists($tab[$i]))
			{
				$this->error(2, $this->f[$this->f_no]['name'], $tab[$i]);
				return;
			}
		}
	}

	/**
	 * method allows to know if a block exists in the template file.
	 *
	 * @param string $bloc_name
	 *
	 * @return boolean
	 * @author H2LSOFT */
	public function blocExists($bloc_name)
	{
		if(@in_array($bloc_name, $this->f[$this->f_no]['shortcut_blocs']['all']))
			return true;
		else
			return false;
	}

	/**
	 * this method verifies if end block is not in double
	 *
	 * @return <type>
	 * @author H2LSOFT */
	protected function endBlocVerify()
	{
		foreach($this->f[$this->f_no]['shortcut_blocs']['all'] as $bloc_name)
		{
			$motif = "/<\\/bloc::$bloc_name>/U";
			if(!preg_match($motif, $this->f[$this->f_no]['buffer']))
			{
				$this->error(8, $this->f[$this->f_no]['name'], $bloc_name);
				return;
			}
		}
	}

	/**
	 * this method verifies if block is not in double
	 *
	 * @author H2LSOFT */
	protected function dualBlocVerify()
	{
		if(count($this->f[$this->f_no]['shortcut_blocs']['all']) == 0)
			return;

		$blocs = array();
		foreach($this->f[$this->f_no]['shortcut_blocs']['all'] as $bloc)
		{
			if(!in_array($bloc, $blocs))
				$blocs[] = $bloc;
			elseif(!in_array($bloc, $this->blocs_double_exceptions))
				$this->error(2.1, $this->f[$this->f_no]['name'], $bloc);
		}
	}

	/**
	 * This method allows to rise an exception for blocks defined twice or more times in the template.
	 *
	 * @param string $str
	 * @since 2.7
	 * @author H2LSOFT */
	public function addBlocException($str)
	{
		if(is_array($str))
		{
			foreach($str as $bloc)
				$this->addBlocException($bloc);
		}
		else
		{
			if(!in_array($str, $this->blocs_double_exceptions))
				$this->blocs_double_exceptions[] = $str;
		}
	}

	/**
	 * this method replaces item
	 *
	 * @param string $name
	 * @param string $replace
	 * @param string $subject
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function replaceItem($name, $replace, $subject)
	{
		if(empty($name))
		{
			$this->error(6, $this->f[$this->f_no]['name']);
			return;
		}

		$str = preg_replace("/\{$name\}/U", $replace, $subject);
		return $str;
	}

	/**
	 * this method replaces block
	 *
	 * @param string $name
	 * @param string $replace
	 * @param string $subject
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function replaceBloc($name, $replace, $subject)
	{
		if(empty($name))
		{
			$this->error(5, $this->f[$this->f_no]['name']);
			return;
		}
		// it's not an array
		if(!is_array($name) && !is_array($replace))
		{
			$motif = "<bloc::$name>(.*)?<\\/bloc::$name>";
			$str = @preg_replace("/$motif/msU", $replace, $subject);
		}
		else
		{
			$str = @preg_replace($name, $replace, $subject);
		}
		return $str;
	}

	/**
	 * this method replaces include command
	 *
	 * @param string $file
	 * @author H2LSOFT */
	protected function parseIncludeCmd($file)
	{
		// parsing the file's name
		$file = str_replace(chr(34), ' ', $file);
		$file = str_replace(chr(39), ' ', $file);
		$file = trim($file);

		$filebuffer = $this->getFile($file);

		if($this->isPhpFile($file))
		{
			$filebuffer = $this->evalHtml($filebuffer);
		}

		$file = str_replace('/', '\/', $file);
		$motif = "/\{\#include\(\"$file\"\)\;\}|\{\#include\(\'$file\'\)\;\}/";
		$this->f[$this->f_no]['buffer'] = @preg_replace($motif, $filebuffer, $this->f[$this->f_no]['buffer']);
	}

	/**
	 * this method replaces all PHP commands
	 *
	 * @author H2LSOFT */
	protected function parsePhpCommands()
	{
		// parse the if
		// $motif = "#{\#(.*)\)}#i";
		$motif = "/{#(if|else|elseif|else|endif)(.*)}/sUi";
		preg_match_all($motif, $this->f[$this->f_no]['buffer'], $matches);
		if(count($matches) == 3 && $matches[0] > 0)
		{
			// replace by the globals variables
			foreach($matches[0] as $m)
			{
				$m2 = $m;
				preg_match_all('#\$([[:alnum:]|\_]*)#', $m, $ms);

				if(count($ms) == 2)
				{
					foreach($ms[0] as $tm)
					{
						if(!empty($tm) && !in_array($tm, array('$_GET', '$_POST', '$_COOKIE', '$_SERVER', '$_SESSION')))
							$m2 = str_replace($tm, '$GLOBALS["'.str_replace('$','',$tm).'"]', $m);
					}
				}

				$this->f[$this->f_no]['buffer'] = str_replace($m, $m2, $this->f[$this->f_no]['buffer']);

				// replace commands
				$cmd2 = $m2;
				$cmd2 = str_ireplace('{#if(', '<?php if(', $cmd2);
				$cmd2 = str_ireplace('{#elseif(', '<?php elseif(', $cmd2);
				$cmd2 = str_ireplace(')}', '): ?>', $cmd2);
				$cmd2 = str_ireplace('{#else}', '<?php else : ?>', $cmd2);
				$cmd2 = str_ireplace('{#endif}', '<?php endif; ?>', $cmd2);

				$this->f[$this->f_no]['buffer'] = str_replace($m2, $cmd2, $this->f[$this->f_no]['buffer']);
			}

			$this->f[$this->f_no]['buffer'] = $this->evalHtml($this->f[$this->f_no]['buffer']);
		}
	}

	/**
	 * this method replaces all include commands
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function parseAllIncludeCmd()
	{
		if(count($this->f[$this->f_no]['cmd_items']) == 0)
			return;

		foreach($this->f[$this->f_no]['cmd_items'] as $filename)
			$this->parseIncludeCmd($filename);
	}

	/**
	 * this method evals template file
	 *
	 * @param string $string
	 *
	 * @return string
	 * @author H2LSOFT */
	protected function evalHtml($string)
	{
		// $string = implode('', $string);
		ob_start();
		eval('?>'.$string);
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}

	/**
	 * this method returns if template is a PHP file
	 *
	 * @param string $filename
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function isPhpFile($filename)
	{
		$php_file_extension = 'php|phtml|php4|php3'; // extensions of the php file

		if(preg_match("/\.($php_file_extension)$/i", basename($filename)))
			return true;

		return false;
	}

	/**
	 * this method returns if it is a block
	 *
	 * @param string $path
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function isBlocDesired($path)
	{
		if(preg_match("/[.]/", $path))
			return true;
		else
			return false;
	}

	/**
	 * this method verifies if all the block are defined
	 *
	 * @param string $path
	 * @param string $type
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function isDefined($path, $type = '')
	{
		if(empty($type))
			$fathers_path = $this->getFathers($path); // r�cup�re les p�res

		if($type == 'NOITEM')
			$fathers_path = $path; // recover the fathers

		if(@in_array($fathers_path, $this->f[$this->f_no]['shortcut_blocs']['used'], true))
			return true;
		else
			return false;
	}

// recover the text of the bloc
// recover the item of path
	/**
	 * this method returns the last item
	 *
	 * @param string $path
	 *
	 * @return string
	 * @author H2LSOFT */
	protected function getItem($path)
	{
		// if(!preg_match("/[.]/",$path)) {return;}
		$path_arr = explode('.', $path);
		return $path_arr[count($path_arr)-1];
	}

// recover the father, the next to last block
	/**
	 * this method returns the last father
	 *
	 * @param string $path
	 * @return string
	 * @author H2LSOFT */
	protected function getFather($path)
	{
		// if(!preg_match("/[.]/",$path)) {return;}
		$path_arr = explode('.', $path);
		return $path_arr[count($path_arr)-2];
	}

// recover the fathers
	/**
	 * this method returns all fathers in array
	 *
	 * @param string $path
	 * @param string $type
	 * @param int $with_item
	 *
	 * @return string
	 * @author H2LSOFT */
	protected function getFathers($path, $type = '', $with_item = 1)
	{
		// if(!preg_match("/[.]/",$path)) {return;}
		$path_arr = explode('.', $path);

		if($with_item == 1)
			$fathers_arr = array_slice($path_arr, 0, count($path_arr)-1); // all the names except the last
		else
			$fathers_arr = $path_arr;

		if($type == 'ARRAY')
			return $fathers_arr;
		else
		{
			$fathers_path = join('.', $fathers_arr);
			return $fathers_path;
		}
	}

	/**
	 * this method
	 *
	 * @param string $path
	 * @author H2LSOFT */
	protected function defineBloc($path)
	{
		$fathers_path = $this->getFathers($path); // no item
		$fathers_arr = $this->getFathers($path, 'ARRAY'); // array

		foreach($fathers_arr as $bloc)
		{
			$cur_arr[] = $bloc;

			if(count($cur_arr) == 1)
				$cur_path = $cur_arr[0];
			else
				$cur_path = join('.', $cur_arr);

			// definition of all the fathers
			if(!$this->isDefined($cur_path, 'NOITEM'))
			{
				$this->savePath($cur_path);
				$a_bloc = $this->initialiazeBloc($bloc);
				// storage
				$b_ref = & $this->f[$this->f_no]['def_blocs'];

				$i = 0;
				foreach($cur_arr as $cur_bloc)
				{
					$b_ref = & $b_ref[$cur_bloc];
					if($i < (count($cur_arr)-1))
						$b_ref = & $b_ref['children'];
					$i++;
				}

				$b_ref = $a_bloc;
			}
		}
	}

	/**
	 * this method inisializes path in TPLN memory manager
	 *
	 * @param <type> $bloc
	 *
	 * @return array
	 * @author H2LSOFT */
	protected function initialiazeBloc($bloc)
	{
		$cur['structure'] = $this->captureBloc($bloc, $this->f[$this->f_no]['buffer']); // structure
		$cur['parsed'][] = $cur['structure']; // parsed
		$cur['is_looped'] = 0;
		$cur['children'] = array();

		return $cur;
	}

	/**
	 * this method registers path in TPLN memory manager
	 *
	 * @param string $fathers_path
	 * @author H2LSOFT */
	protected function savePath($fathers_path)
	{
		$this->f[$this->f_no]['shortcut_blocs']['used'][] = $fathers_path; // save in defined blocks
	}

// code from bitlux
	/**
	 * This method allows to protect data against XSS attack,
	 *
	 * this method is used before insetring records in a database.
	 *
	 * @param string $string
	 * @return string
	 * @since 2.5, 2.2.5
	 * @author H2LSOFT */
	public function xssProtect($string)
	{
		if(is_array($string))
		{
			foreach($string as $key => $val)
				$string[$key] = $this->XSSProtect($val);

			return $string;
		}

		if(get_magic_quotes_gpc())$string = stripslashes($string);

		$string = str_ireplace(array("&lt;", "&gt;"), array("&amp;lt;", "&amp;gt;"), $string);
		$string = str_ireplace(array("<?php", "<?", "<%", "?>"), '', $string);

		// fix &entitiy\n;
		$string = preg_replace('#(&\#*\w+)[\s\r\n]+;#U', "$1;", $string);
		// $string = @html_entity_decode($string, ENT_COMPAT, "UTF-8");
		// remove any attribute starting with "on" or xmlns
		$string = preg_replace('#(<[^>]+[\s\r\n\"\'])(on|xmlns)[^>]*>#iU', "$1>", $string);
		// remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\s\r\n]*=[\s\n\r]*([\`\'\"]*)[\\s\n\r]*j[\s\n\r]*a[\s\n\r]*v[\s\n\r]*a[\s\n\r]*s[\s\n\r]*c[\s\n\r]*r[\s\n\r]*i[\s\n\r]*p[\s\n\r]*t[\s\n\r]*:#iU', '$1=$2nojavascript...', $string);
		$string = preg_replace('#([a-z]*)[\s\r\n]*=([\'\"]*)[\s\n\r]*v[\s\n\r]*b[\s\n\r]*s[\s\n\r]*c[\s\n\r]*r[\s\n\r]*i[\s\n\r]*p[\s\n\r]*t[\s\n\r]*:#iU', '$1=$2novbscript...', $string);
		// <span style="width: expression(alert('Ping!'));"></span>
		// only works in ie...
		$string = preg_replace('#(<[^>]+)style[\s\r\n]*=[\s\r\n]*([\`\'\"]*).*expression[\s\r\n]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\s\r\n]*=[\s\r\n]*([\`\'\"]*).*s[\s\n\r]*c[\s\n\r]*r[\s\n\r]*i[\s\n\r]*p[\s\n\r]*t[\s\n\r]*:*[^>]*>#iU', "$1>", $string);
		// remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i', "", $string);
		// remove really unwanted tags
		do
		{
			$oldstring = $string;
			$string = preg_replace('#</*(style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $string);
		}
		while ($oldstring != $string);

		return $string;
	}

	/**
	 * this method replaces { _Version} by TPLN current version
	 *
	 * @author H2LSOFT */
	protected function parseVersion()
	{
		if($this->itemExists('_Version')) // place the logo
			$this->parse('_Version', TPLN_VERSION);
	}

	/**
	 * this method replaces { _QueryCount} by the numbre of queries executed.
	 *
	 * @author H2LSOFT */
	protected function parseQueryCount()
	{
		if($this->itemExists('_QueryCount')) // place the logo
			$this->parse('_QueryCount', $this->query_count);
	}

	/**
	 * this method replaces { _Logo} by TPLN Logo
	 *
	 * @author H2LSOFT */
	protected function parseLogo()
	{
		if($this->itemExists('_Logo')) // place the logo
		{
			$this->parse('_Logo', '<a href="http://tpln.sourceforge.net" title="Powered by TPLN template !"><img src="http://tpln.sourceforge.net/logo.gif" alt="made with TPLN Template!" border="0" target="_blank"  /></a>');
		}
	}

	/**
	 * this method allows to know template total time execution.
	 *
	 * The variable { _Chrono} must be imperatively replaced inside your template file.
	 *
	 * @param string $type
	 * @see write(), OutPut().
	 * @author H2LSOFT */
	public function setChrono($type = 'this')
	{
		$this->chrono_type[$this->f_no] = $type;
	}

	/**
	 * this method replaces { _Chrono} by the time clock of the template
	 *
	 * @author H2LSOFT */
	protected function parseChrono()
	{
		if($this->itemExists('_Chrono')) // place the chrono

		{
			$this->getExecutionTime();
			$this->parse('_Chrono', $this->f[$this->f_no]['execution_time']);
		}
	}

// gestion du chrono
	/**
	 * this method
	 *
	 * @author H2LSOFT */
	protected function getExecutionTime()
	{
		$time = explode(' ', microtime());
		$fin = $time[1] + $time[0];
		// we want the perf of this cette session or all
		if($this->chrono_type[$this->f_no] == 'ALL')
			$this->f[$this->f_no]['execution_time'] = intval(10000 * ((double)$fin - (double)TPLN_CHRONO_STARTED)) / 10000;
		else
			$this->f[$this->f_no]['execution_time'] = intval(10000 * ((double)$fin - (double)$this->f[$this->f_no]['chrono_started'])) / 10000;
	}

// retourne la valeur de la premi�re ligne
	/**
	 * this method verifies if template is expired
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function inCachePeriod()
	{
		$expire = $this->getTime(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['cache_name']);

		if($expire >= $this->f[$this->f_no]['time_started'])
		{
			return true;
		}
		else
		{
			@unlink(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['cache_name']); // remove the file
			return false;
		}
	}

// prend le temps au dessus du fichier
	/**
	 * this method gets current time
	 *
	 * @return int
	 * @author H2LSOFT */
	protected function getTime()
	{
		$fp = @fopen(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['cache_name'], 'r'); // open the file
		$expire = trim(@fgets($fp, 12));
		// $data = fread($fp, filesize($file));
		@fclose($fp);

		clearstatcache();

		return $expire;
	}

	/**
	 * this method creates file cache
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function createCache()
	{
		if(!$this->f[$this->f_no]['create_cached_file'])
		{
			return;
		}
		else
		{
			// add a function for creating folders
			$all_dir = explode('/', $this->f[$this->f_no]['cache_name']);
			if(count($all_dir) > 0)
			{
				$count = count($all_dir)-1; // we want the last
				$act_dir = TPLN_CACHE_DIR;
				for($i = 0;$i < $count;$i++)
				{
					$act_dir .= '/'.$all_dir[$i];
					if(!is_dir($act_dir))
					{
						if(!@mkdir($act_dir, 0755))
						{
							$this->error(7);
							return;
						}

						clearstatcache();
					}
				}
			}

			$cache_file_content = $this->f[$this->f_no]['cache_expire']."\r".$this->f[$this->f_no]['buffer'];
			$fp = @fopen(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['cache_name'], 'w'); // open the file
			@fwrite($fp, $cache_file_content);
			@fclose($fp);
			clearstatcache(); // erase the data buffer
		}
	}

	/**
	 * this method returns cached file
	 *
	 * @author H2LSOFT */
	protected function getCachedFile()
	{
		$this->f[$this->f_no]['create_cached_file'] = 0;

		$fp = @fopen(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['cache_name'], 'r');
		$tmp_expire = @fgets($fp, 12); // for the pointer's position after the time
		$this->f[$this->f_no]['buffer'] = @fread($fp, filesize(TPLN_CACHE_DIR.'/'.$this->f[$this->f_no]['cache_name']));
		@fclose($fp);
		clearstatcache(); // erase the data buffer
	}

	/**
	 * this method verifies if cach directory exists
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function cacheDirExists()
	{
		// the directory exists ?
		if(!is_dir(TPLN_CACHE_DIR))
		{
			if(!@mkdir(TPLN_CACHE_DIR, 0755))
			{
				// $this->_AddTraceMsg();
				$this->error(7);
				return;
			}
			clearstatcache();
		}
	}

	/**
	 * this method extracts string
	 *
	 * @param  string $str
	 * @param  string $start
	 * @param  string $end
	 * @param bool  $inc_markup
	 *
	 * @return string
	 * @author H2LSOFT */
	public function extractStr($str, $start, $end, $inc_markup = 0)
	{
		$pos_start = strpos($str, $start);

		if($inc_markup)
			$pos_end = strpos($str, $end, ($pos_start));
		else
			$pos_end = strpos($str, $end, ($pos_start + strlen($start)));

		if(($pos_start !== false) && ($pos_end !== false))
		{
			if($inc_markup)
			{
				$pos1 = $pos_start;
				$pos2 = ($pos_end + strlen($end)) - $pos1;
			}
			else
			{
				$pos1 = $pos_start + strlen($start);
				$pos2 = $pos_end - $pos1;
			}

			return substr($str, $pos1, $pos2);
		}
	}

	/**
	 * PHP str replace with count parameter
	 *
	 * @param string $search
	 * @param string $replace
	 * @param string $subject
	 * @param int $times
	 *
	 * @return string
	 * @author H2LSOFT */
	public function str_replace_count($search, $replace, $subject, $times)
	{
		$subject_original = $subject;

		$len = strlen($search);
		$pos = 0;
		for ($i = 1;$i <= $times;$i++)
		{
			$pos = strpos($subject, $search, $pos);

			if($pos === false) break;

			$subject = substr($subject_original, 0, $pos);
			$subject .= $replace;
			$subject .= substr($subject_original, $pos + $len);
			$subject_original = $subject;

		}

		return($subject);
	}

	/**
	 * This method allows to convert a php array to a javascript array, it is useful for Ajax.
	 *
	 * @param array $arr
	 *
	 * @return string
	 * @author H2LSOFT */
	public function array2json($arr)
	{
		if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality.
		$parts = array();
		$is_list = false;

		//Find out if the given array is a numerical array
		$keys = array_keys($arr);
		$max_length = count($arr)-1;
		if(($keys[0] == 0) and ($keys[$max_length] == $max_length))
		{
			//See if the first key is 0 and last key is length - 1
			$is_list = true;
			for($i=0; $i<count($keys); $i++)
			{
				//See if each key correspondes to its position
				if($i != $keys[$i])
				{
					//A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}

		foreach($arr as $key=>$value)
		{
			if(is_array($value))
			{
				//Custom handling for arrays
				if($is_list) $parts[] = array2json($value); /* :RECURSION: */
				else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
			}
			else
			{
				$str = '';
				if(!$is_list) $str = '"' . $key . '":';

				//Custom handling for multiple data types
				if(is_numeric($value)) $str .= $value; //Numbers
				elseif($value === false) $str .= 'false'; //The booleans
				elseif($value === true) $str .= 'true';
				else $str .= '"' . addslashes($value) . '"'; //All other things
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)

				$parts[] = $str;
			}
		}

		$json = implode(',',$parts);
		if($is_list) return '[' . $json . ']';//Return numerical JSON
		return '{' . $json . '}';//Return associative JSON
	}

	/**
	 * This method allows to recover the contents of the text of the block,
	 *
	 * useful to recover the text after an exit of template
	 *
	 * @param string $bloc_name
	 * @param string $out
	 *
	 * @return string
	 * @since 2.9
	 * @author H2LSOFT */
	public function getAjaxBloc($bloc_name, $out='')
	{
		if(empty($out))	$out = $this->output();

		$pattern_s = "<!-- ajax::$bloc_name -->";
		$pattern_e = "<!-- /ajax::$bloc_name -->";

		$pos_start = strpos($out, $pattern_s);
		if($pos_start === false)return '';
		$pos_start += strlen($pattern_s);

		$pos_end = strpos($out, $pattern_e, $pos_start);
		if($pos_end === false)return '';

		$bloc_content = substr($out, $pos_start, $pos_end-$pos_start);
		return $bloc_content;
	}

	/**
	 * This method allows to convert http, www, ftp, mailto into clickable element
	 *
	 * @param string $ret
	 * @param boolean $add_nofollow default false
	 * @return string
	 * @author H2LSOFT */
	public function clickable($ret, $add_nofollow=false)
	{
		/*$str = eregi_replace("([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)",
							   "<a href=\"mailto:\\1\">\\1</a>", $str);
	$str = eregi_replace("([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
									"<a href=\"\\1://\\2\\3\" target=\"_blank\">\\1://\\2\\3</a>", $str);
	* @author H2LSOFT */

		// matches an "xxxx://yyyy" URL at the start of a line, or after a space.
		// xxxx can only be alpha characters.
		// yyyy is anything up to the first space, newline, comma, double quote or <
        $nofollow = ($add_nofollow) ? 'rel="nofollow"' : '';
		$ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" $nofollow target=\"_blank\">\\2</a>", $ret);

		// matches an email@domain type address at the start of a line, or after a space.
		// Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
		$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\" $nofollow>\\2@\\3</a>", $ret);

		// matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
		// Must contain at least 2 dots. xxxx contains either alphanum, or "-"
		// zzzz is optional.. will contain everything up to the first space, newline,
		// comma, double quote or <.
		//$ret = preg_replace("#(^|[\n ]|)((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);


		// Remove our padding..
		// $ret = substr($ret, 1);



		return $ret;
	}
}

