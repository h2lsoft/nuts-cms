<?php
/**
 * Class use for front office
 * @package Nuts
 */
class Page extends NutsCore
{
	/**
	 * change user view mode `view` or `preview`
	 * @var string
	 */
	public $mode = 'view';

	/**
	 * page theme
	 * @var string
	 */
	public $theme;

	/**
	 * page language
	 * @var string
	 */
	public $language;

	/**
	 * system default language
	 * @var string
	 */
	public $languageDefault;

	/**
	 * languages system available
	 * @var array
	 */
	public $languages = array();

	/**
	 * page variables
	 * @var array
	 */
	public $vars = array();

	/**
	 * main plugin arguments
	 * @var array
	 */
	public $args = array();

	/**
	 * current plugin name
	 * @var string
	 */
	public $plugin = 'page';

	/**
	 * plugin system name
	 * @var string
	 */
	public $plugin_real_name = '_page-manager';
	/**
	 * plugin arguments
	 * @var array
	 */
	public $plugin_args = array();

	/**
	 * plugin embed
	 * @var array
	 */
	public $plugin_embed = array();

	/**
	 * current page is homepage
	 * @var bool
	 */
	public $isHome = false;

	/**
	 * current plugin path
	 * @var string
	 */
	public $plugin_path;

	/**
	 * current plugin front path
	 * @var string
	 */
	public $plugin_front_path;

	private $dbIsConnected = false;
	private $pageID;

	/**
	 * current page cache time in seconds
	 * @var int
	 */
	private $pageCacheTime = -1;

	/**
	 * chrono start
	 * @var int
	 */
	private $pageChronoStart = 0;

	/**
	 * pages has a restricted access
	 * @var boolean
	 */
	private $accessRestricted = false;


	/**
	 * dynamic patterns
	 * @var array
	 */
	private $patterns = array();

    /**
     * Constructor
     */
	public function __construct()
	{
		// preview
		if(isset($_GET['nuts_preview']) && $_GET['nuts_preview'] == 1)
		{
			@session_start();
			if(isset($_SESSION['NutsUserID']))
			{
				$this->mode = 'preview';
			}
		}

		// maintenance ?
		$allowed_ips = explode(',', WEBSITE_MAINTENANCE_IPS);
		$allowed_ips = array_map('trim', $allowed_ips);

		if(WEBSITE_MAINTENANCE == true && !in_array($this->getIP(), $allowed_ips))
        {
            // normal message or redirection ?
            $maintenance_msg = WEBSITE_MAINTENANCE_MESSAGE;
            if(strpos($maintenance_msg, 'http') === false && strpos($maintenance_msg, '<script') === false)
            {
                $maintenance_msg = '<html>';
                $maintenance_msg .= '<head>';
                $maintenance_msg .= '   <META NAME="robots" CONTENT="noindex,nofollow">';
                $maintenance_msg .= '   <title>'.WEBSITE_NAME.'</title>';
                $maintenance_msg .= '<head>';
                $maintenance_msg .= '<body>';
                $maintenance_msg .= '   <div style="margin: 100px auto 0 auto; width:550px; white-space: nowrap; text-align:center; padding:15px; font-family: arial; font-weight: bold; font-size: 16px; border: 1px solid navy; border-radius: 5px; color: navy;">';
                $maintenance_msg .= '   <img src="/nuts/img/icon-tag-moderator.png" align="absmiddle" /> ';
                $maintenance_msg .= WEBSITE_MAINTENANCE_MESSAGE;
                $maintenance_msg .= '   </div>';
                $maintenance_msg .= '</body>';
                $maintenance_msg .= '</html>';
            }

            die($maintenance_msg);

        }

		/*if(in_array($this->getIP(), $allowed_ips))
			$this->mode = 'preview';*/

		$this->dbConnect();
		$this->dbIsConnected = true;

		// load vars
		$this->doQuery("SELECT * FROM NutsTemplateConfiguration WHERE Deleted = 'NO' LIMIT 1");
		$row = $this->dbFetch();

		// $this->theme = $row['Theme'];
		$this->theme = $GLOBALS['nuts_theme_selected'];
		$this->language = $row['LanguageDefault'];
		$this->languageDefault = $this->language;
		$row['Languages'] = trim($row['Languages']);
		if(!empty($row['Languages']))
			$this->languages = explode(',', $row['Languages']);
		$this->languages = array_map('trim', $this->languages);

	}


	/**
     * Destructor: kill db connection
     */
	public function __destruct()
	{
		if($this->dbIsConnected)
			$this->dbClose();
	}

    /**
     * Add SQL concatenation between News or
     *
     * @param bool $for_news
	 * @param string $add_table_name
     * @return string sql to include in WHERE clause
     */
	private function sqlAdded($for_news = false, $add_table_name = '')
	{
		$str = " AND {$add_table_name}Deleted = 'NO'";
		if($this->mode == 'view')
		{
			if(!$for_news)
			{
				$str .= " AND {$add_table_name}State = 'PUBLISHED' ";
				$str .= " AND (DateStartOption = 'NO' OR (DateStartOption = 'YES' AND DateStart <= NOW())) ";
				$str .= " AND (DateEndOption = 'NO' OR (DateEndOption = 'YES' AND DateEnd >= NOW())) ";
			}
			else
			{
				$str .= " AND {$add_table_name}Active = 'YES'";
			}
		}

		return $str;
	}

	/**
	 * Add SQL access restriction
	 *
	 * @return string sql to include in where clause
	 */
	private function sqlAddedAccessRestricted()
	{
		if(!nutsUserIsLogon())
		{
			$sql_access_restricted = " AccessRestricted = 'NO' ";
		}
		else
		{
			$_SESSION['NutsGroupID'] = (int)$_SESSION['NutsGroupID'];
			$sql_access_restricted = "(
											AccessRestricted = 'NO' OR
											(
												AccessRestricted = 'YES' AND
												NutsPage.ID IN(SELECT NutsPageID FROM NutsPageAccess WHERE NutsGroupID = {$_SESSION['NutsGroupID']})
											)
										)";
		}

		return $sql_access_restricted;
	}



    /**
     * Generates web 404 error
     */
	public function error404()
	{

		$IP_long = (float)ip2long($this->getIP());

		// store error 404 in log
		if(NUTS_LOG_ERROR_404 == true)
		{
			// get last error
			$this->dbSelect("SELECT ID FROM NutsLog WHERE Application = '_fo-error' AND Action = 'Error 404' AND Resume = '%s' LIMIT 1", array($_SERVER['REQUEST_URI']));
			if($this->dbNumrows() == 0)
			{
                $script_uri = (isset($_SERVER['SCRIPT_URI'])) ? $_SERVER['SCRIPT_URI'] : $_SERVER['REQUEST_URI'];
				$this->dbInsert('NutsLog', array(
                                                    'DateGMT' => 'NOW()',
											        'Application' => '_fo-error',
											        'Action' => 'Error 404',
											        'Resume' => $script_uri.' (referer => `'.@$_SERVER['HTTP_REFERER'].'`)',
											        'IP' => $IP_long
                                                ));
			}
		}

		// 404 error
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");

		if(!preg_match('/^http/i', NUTS_ERROR404_TEMPLATE))
		{
            $tpl = NUTS_ERROR404_TEMPLATE;
            if($tpl == 'error404.html')$tpl = WEBSITE_PATH.'/nuts/_templates/error404.html';

			$this->open($tpl);
			echo $this->output();
		}
		else
		{
			die(file_get_contents(NUTS_ERROR404_TEMPLATE));
		}


		// header("Location: /error404.php?uri=".base64_encode($_SERVER['REQUEST_URI']));
		exit();
	}

    /**
     * Load nuts plugin
     *
     * @access
     */
	private function loadNutsPlugin()
	{
		global $plugin, $page, $nuts;

		if($this->plugin == 'page')$this->plugin_real_name = '_page-manager';
		elseif($this->plugin == 'news')$this->plugin_real_name = '_news';
		else {$this->plugin_real_name = $this->plugin;}
		$this->plugin_path = 'plugins/'.$this->plugin_real_name;
		$this->plugin_front_path = $this->plugin_path.'/www';

		if(!file_exists('plugins/'.$this->plugin_real_name.'/www/index.inc.php'))
		{
			$this->setNutsContent("Plugin `{$this->plugin}` not found");
		}
		else
		{
			include('plugins/'.$this->plugin_real_name.'/www/index.inc.php');
			// $this->error404();
		}

	}

    /**
     * Parse nuts command
     *
     * @param string $cmd
     * @return array formatted
     */
	private function parseCommand($cmd)
	{
		$cmd = str_replace('NUTS', '', $cmd);
		// $cmd = str_replace('    ', "\t", $cmd);
		$cmd = trim($cmd);
		$tmp = explode("\t", $cmd);

		// @NUTS + \t found
		if(count($tmp) > 1)
		{
			$f = array();
			foreach($tmp as $c)
			{
				list($key, $val) = explode("='", $c);

				$val[strlen($val)-1] = '';
				$val = trim($val);
				$f[strtoupper($key)] = $val;
			}
		}
		$f[] = $cmd;

		return $f;
	}

    /**
     * Display page in browser (launch every treatment)
     */
	public function write()
	{
		global $nuts_front_plugins_direct_access;

		$this->pageChronoStart = microtime(true);

		// apply url custom rewriting
		include(NUTS_PATH."/url_rewriting_rules.inc.php");
		$_SERVER['REQUEST_URI'] = str_replace($uri_str_patterns, $uri_str_replaces, $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = preg_replace($uri_patterns, $uri_replaces, $_SERVER['REQUEST_URI']);

		// force uri get
		$url_tmp = @parse_url($_SERVER['REQUEST_URI']);
		if(!$url_tmp)$this->error404();

		if(isset($url_tmp['query']))
			parse_str($url_tmp['query'], $_GET);

        // get information page
        $port = ($_SERVER['SERVER_PORT'] == 80) ? '' : ':'.$_SERVER['SERVER_PORT'];
		$url = 'http'.((!empty($_SERVER['HTTPS'])) ? 's' : '').'://'.$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];

		// no query string for control
		$url = explode('?', $url);
		$url = $url[0];

        $curl = str_replace(WEBSITE_URL.'/', '', $url);

		$curl = explode('/', $curl);




		// homepage ?
		$this->isHome = false;
		if(count($curl) == 0 || $curl[0] == '' || $curl[0] == 'index.php')
		{
			$lang = $this->language;
			$this->doQuery("SELECT
									ID
							FROM
									NutsPage
							WHERE
									NutsPageID = 0 AND
									Language = '{$lang}' AND
									ZoneID = 0 ".$this->sqlAdded()."
							ORDER BY
									Position
							LIMIT 1");
			$this->pageID = (int)$this->getOne();
			$this->isHome = true;
		}
		else
		{

			// error 404
			if($curl[0] != $this->language && !in_array($curl[0], $this->languages))
			{
				$this->error404();
			}
			$lang = $curl[0];
			$this->language = $lang;

			// plugin other than page ?
			if(count($curl) == 1 || (count($curl) == 2 && empty($curl[1])))
			{
                $sql = "SELECT ID FROM NutsPage WHERE Language = '{$lang}' AND NutsPageID = 0 AND ZoneID = 0".$this->sqlAdded()." ORDER BY Position LIMIT 1";
				$this->doQuery($sql);
				$this->pageID = (int)$this->getOne();
				$this->isHome = true;
			}
			else
			{
				// detect plugin
				$this->pageID = (int)$curl[1];

				if($this->pageID == 0)
				{
					$this->plugin = $curl[1];
					$tmp = $curl[1];

					$tmp = preg_replace('/-(.*)\.html$/', '', $tmp);
					$tmp = str_replace('.html', '', $tmp);
					$tmp = trim($tmp);

					// load plugin process !
					$tmp = explode(',', $tmp);
					$tmp[0] = explode('?', $tmp[0]);
					$tmp[0] = $tmp[0][0];
					$this->plugin = trim($tmp[0]);

					if($this->plugin != 'news' && !in_array($this->plugin, $nuts_front_plugins_direct_access))
						$this->error404();

					if(count($tmp) > 1)
						$this->plugin_args = explode(',', $tmp[1]);
				}
				else // page has arguments ?
				{

					// detect homepage
					$this->doQuery("SELECT
											ID
									FROM
											NutsPage
									WHERE
											NutsPageID = 0 AND
											Language = '{$lang}' AND
											ZoneID = 0 ".$this->sqlAdded()."
									ORDER BY
											Position
									LIMIT 1");
					if($this->getOne() == $this->pageID)
						$this->isHome = true;

					$args = explode(',', $curl[1]);
					for($i=1; $i < count($args)-1; $i++)
					{
						$this->args[] = $args[$i];
					}
				}
			}
		}

		// possible cache ?
		if($this->cacheIsPossible() && $this->cacheExists())
		{
			$cacheFile = $this->cacheGetFileName();
			$out = file_get_contents($cacheFile);
			$out = $this->chronoReplace($out);
           	die($out);
		}

		// page is access restricted ?
		if($this->pageID != 0)
		{
            $this->doQuery("SELECT AccessRestricted FROM NutsPage WHERE ID = {$this->pageID}");
			$this->accessRestricted = ($this->dbGetOne() == 'YES') ? true : false;

			if($this->accessRestricted)
			{
				if(!session_id())@session_start();

				// user has access
				if(!nutsUserIsLogon())
				{
					// redirection to private page
					nutsAccessRestrictedRedirectPage('login');
				}
				else
				{
					// user has right access this page ?
					$this->doQuery("SELECT COUNT(*) FROM NutsPageAccess WHERE NutsPageID = {$this->pageID} AND NutsGroupID = {$_SESSION['NutsGroupID']} LIMIT 1");
					if((int)$this->dbGetOne() == 0)
					{
						// redirection to private page
						nutsAccessRestrictedRedirectPage('forbidden');
					}
				}
			}
		}

		// load plugin
		$this->loadNutsPlugin();

		// variables assignation
		if(empty($this->vars['Template']))$this->vars['Template'] = 'index.html';
		if(empty($this->vars['MetaTitle']))$this->vars['MetaTitle'] = MetaTitle;
		if(empty($this->vars['MetaDescription']))$this->vars['MetaDescription'] = MetaDescription;
		if(empty($this->vars['MetaKeywords']))$this->vars['MetaKeywords'] = MetaKeywords;

		// output
		$this->open(NUTS_THEMES_PATH.'/'.$this->theme.'/'.$this->vars['Template']);
		// $this->htmlCompress(NUTS_HTML_COMPRESS);
		$out = parent::output();

		// ajax optimisation
		if(isset($_GET['ajax']) && $_GET['ajax'] == 1)
		{
			$out = $this->getAjaxBloc('content', $out);
		}

		// @PAGE_CONTENT parsing ***************************************************************************
		// <editor-fold defaultstate="collapsed">
		$out = str_replace("{@PAGE_CONTENT_RESUME}", @$this->vars['ContentResume'], $out);

        if(isset($this->vars['NutsPageContentViewID']) && $this->vars['NutsPageContentViewID'] != 0)
        {
            Query::factory()->select('Name, Html, HookData')
                            ->from('NutsPageContentView')
                            ->where('ID', '=', $this->vars['NutsPageContentViewID'])
                            ->execute();


            if(!$this->dbNumRows())
            {
                $rep = $this->setNutsCommentMarkup("view no found #".$this->vars['NutsPageContentViewID'], "");
            }
            else
            {
                $r = $this->dbFetch();
                $rep = $r['Html'];

                // get field in $row
                $sql = "SELECT
                                  NutsPageContentViewField.Name,
                                  NutsPageContentViewFieldData.Value
                        FROM
                                  NutsPageContentViewField,
                                  NutsPageContentViewFieldData
                        WHERE
                                  NutsPageContentViewField.Deleted = 'NO' AND
                                  NutsPageContentViewFieldData.Deleted = 'NO' AND
                                  NutsPageContentViewField.ID = NutsPageContentViewFieldData.NutsPageContentViewFieldID AND
                                  NutsPageContentViewFieldData.NutsPageContentViewID = {$this->vars['NutsPageContentViewID']} AND
                                  NutsPageContentViewFieldData.NutsPageID = {$this->vars['ID']}";
                $this->doQuery($sql);

                $row = array();
                while($rX = $this->dbFetch())
                {
                    $row[$rX['Name']] = $rX['Value'];
                }

                // hook data foreach
                $r['HookData'] = trim($r['HookData']);
                if(!empty($r['HookData']))
                {
                    $r['HookData'] = str_replace('$nuts->', '$this->', $r['HookData']);
                    eval($r['HookData']);
                }

                // parsing values
                foreach($row as $key => $val)
                {
                    $rep = str_replace("{".$key."}", $val, $rep);
                }



                $rep = $this->setNutsCommentMarkup("view ".$r['Name'], $rep);
            }



            $this->vars['Content'] = $rep;
        }

		$out = str_replace("{@PAGE_CONTENT}", @$this->vars['Content'], $out);
		$out = $this->formatOutput($out);
		// </editor-fold>

		// PLUGIN replacement ##############################################################################
		// <editor-fold defaultstate="collapsed">
		preg_match_all("#\{@NUTS	TYPE='PLUGIN'	NAME='(.*)'\}#U", $out, $matches);
		if(count($matches) >= 2)
		{
			$old_plugin = $this->plugin;
			for($i=0; $i < count($matches[1]); $i++)
			{
				$pl = trim($matches[1][$i]);

				// parameters ?
				$tmp = explode("'	PARAMETERS='", $pl);
				if(count($tmp) > 1)
				{
					$pl = $tmp[0];
					$this->plugin_embed[$pl] = explode(",", $tmp[1]);
				}

				if(!empty($pl))
				{
					$this->plugin = strtolower($pl);
					$this->loadNutsPlugin();

					$rep = $GLOBALS['NUTS_CONTENT'];
					$rep = $this->setNutsCommentMarkup('plugin '.$pl." ".@$tmp[1], $rep);
					$out = str_replace($matches[0][$i], $rep, $out);
				}
			}
			$this->plugin = $old_plugin;
			$out = $this->formatOutput($out);
		}
		// </editor-fold>

		// BLOCKS  replacement ! *************************************************************************************************
		// <editor-fold defaultstate="collapsed">
		preg_match_all("#\{@NUTS	TYPE='BLOCK'	GROUP='(.*)'\}#U", $out, $matches);
		if(count($matches) >= 2)
		{
			$matches[1] = array_unique($matches[1]);

			// detect defined
			if(!isset($this->vars['CustomBlock']))$this->vars['CustomBlock'] = array();
			if(!is_array($this->vars['CustomBlock']) || count($this->vars['CustomBlock']) == 0)
			{
				foreach($matches[1] as $cmd)
					$out = str_replace("{@NUTS	TYPE='BLOCK'	GROUP='{$cmd}'}", '', $out);
			}
			else
			{
				// get each block with content
				foreach($matches[1] as $cmd)
				{
					$rep = '';
					if(isset($this->vars['CustomBlock'][$cmd]) && count($this->vars['CustomBlock'][$cmd]) > 0)
					{
						$sql = "SELECT
										ID,
										Text
								FROM
										NutsBlock
								WHERE
										Deleted = 'NO' AND
										Visible = 'YES' AND
										GroupName = '".addslashes($cmd)."' AND
										ID	IN(".join(',', $this->vars['CustomBlock'][$cmd]).")";
						$this->DoQuery($sql);
						if($this->dbNumRows() == 0)
						{
							$rep = '';
						}
						else
						{
							$blocks = array();
							while($row = $this->DBFetch())
								$blocks[$row['ID']] = $row['Text'];

							$rep = '';
							foreach($this->vars['CustomBlock'][$cmd] as $blockID)
							{
								if(isset($blocks[$blockID]))
									$rep .= $blocks[$blockID];
							}
							$blocks = array();
						}
					}
					$rep = $this->setNutsCommentMarkup('block group '.$cmd, $rep);
					$out = str_replace("{@NUTS	TYPE='BLOCK'	GROUP='$cmd'}", $rep, $out);
				}
			}
			$out = $this->formatOutput($out);
		}
		// block names
		preg_match_all("#\{@NUTS	TYPE='BLOCK'	NAME='(.*)'\}#U", $out, $matches);
		if(count($matches) >= 2)
		{
			$matches[1] = array_unique($matches[1]);

			// get each block with content
			foreach($matches[1] as $cmd)
			{
				$rep = '';
				$sql = "SELECT
								Text
						FROM
								NutsBlock
						WHERE
								Deleted = 'NO' AND
								Visible = 'YES' AND
								Name = '".addslashes($cmd)."'";
				$this->DoQuery($sql);
				if($this->dbNumRows() == 0)
					$rep = '';
				else
				{
					$row = $this->DBFetch();
					$rep = $row['Text'];
				}

				$rep = $this->setNutsCommentMarkup('block '.$cmd, $rep);
				$out = str_replace("{@NUTS	TYPE='BLOCK'	NAME='$cmd'}", $rep, $out);
			}
			$out = $this->formatOutput($out);
		}
		// </editor-fold>

		// PLUGIN x2 replacement ##############################################################################
		// <editor-fold defaultstate="collapsed">
		preg_match_all("#\{@NUTS	TYPE='PLUGIN'	NAME='(.*)'\}#U", $out, $matches);
		if(count($matches) >= 2)
		{
			$old_plugin = $this->plugin;
			for($i=0; $i < count($matches[1]); $i++)
			{
				$pl = trim($matches[1][$i]);

				// parameters ?
				$tmp = explode("'	PARAMETERS='", $pl);
				if(count($tmp) > 1)
				{
					$pl = $tmp[0];
					$this->plugin_embed[$pl] = explode(",", $tmp[1]);
				}

				if(!empty($pl))
				{
					$this->plugin = strtolower($pl);
					$this->loadNutsPlugin();

					$rep = $GLOBALS['NUTS_CONTENT'];
					$rep = $this->setNutsCommentMarkup('plugin '.$pl." ".@$tmp[1], $rep);
					$out = str_replace($matches[0][$i], $rep, $out);
				}
			}
			$this->plugin = $old_plugin;
		}
		// </editor-fold>

		// REGION  replacement ! *************************************************************************************************
		// <editor-fold defaultstate="collapsed">
		preg_match_all("#\{@(.*)\}#U", $out, $matches);
		if(count($matches) == 2)
		{
			$matches[1] = array_unique($matches[1]);
			foreach($matches[1] as $cmd)
			{
				$cmd2 = $this->parseCommand($cmd);
				if(isset($cmd2['TYPE']) && $cmd2['TYPE'] == 'REGION')
				{
					$rep = $this->getShowRecords($cmd2['NAME']);

					$rep = $this->setNutsCommentMarkup('region '.$cmd2['NAME'], $rep);
					$out = str_replace("{@$cmd}", $rep, $out);
				}
			}
			$out = $this->formatOutput($out);
		}
		// </editor-fold>

		// GALLERY replacement ##############################################################################
		// <editor-fold defaultstate="collapsed">
		preg_match_all("#\{@NUTS	TYPE='GALLERY'	NAME='(.*)'\}#U", $out, $matches);
		if(count($matches) >= 2)
		{
			$matches[1] = array_unique($matches[1]);
			foreach($matches[1] as $cmd)
			{
				$sql = "SELECT
								NutsGalleryImage.*,
								NutsGallery.GenerateJs
						FROM
								NutsGallery,
								NutsGalleryImage
						WHERE
								NutsGallery.Deleted = 'NO' AND
								NutsGallery.Active = 'YES' AND
								NutsGallery.ID = NutsGalleryImage.NutsGalleryID AND
								NutsGallery.Name = '".addslashes($cmd)."' AND
								NutsGalleryImage.Deleted = 'NO' AND
								NutsGalleryImage.Active = 'YES'
						ORDER BY
								NutsGalleryImage.Position";
				$this->DoQuery($sql);
				if($this->dbNumRows() == 0)
				{
					$rep = "";
				}
				else
				{

					$rep = '<div class="nuts_gallery">'."\n";
					$rep .= '<ul>'."\n";

					$generate_js = false;
					$nuts_gallery_id = 0;

					while($row = $this->DBFetch())
					{
						if(!$nuts_gallery_id)
						{
							$nuts_gallery_id = $row['NutsGalleryID'];
							$generate_js = ($row['GenerateJs'] == 'YES') ? true: false;
						}

						$legend = ucfirst($row['Legend']);
						$rep .= '<li><a class="nuts_gallery_'.$row['NutsGalleryID'].'" rel="nuts_gallery_'.$row['NutsGalleryID'].'" title="'.$legend.'" href="'.NUTS_GALLERY_IMAGES_URL.'/'.$row['MainImage'].'">';

						$big_node = '';
						if(!empty($row['BigImage']))
						{
							$big_node = 'big="'.NUTS_GALLERY_IMAGES_URL.'/'.$row['BigImage'].'"';
						}

						$hd_node = '';
						if(!empty($row['HDImage']))
						{
							$hd_node = 'hd="'.NUTS_GALLERY_IMAGES_HD_URL.'/'.$row['HDImage'].'"';
						}

						$img = '<img alt="'.$legend.'" src="'.NUTS_GALLERY_IMAGES_URL.'/thumb_'.$row['MainImage'].'" '.$big_node.' '.$hd_node.' />';

						$rep .= str_replace('  ', ' ', $img);
						$rep .= '</a></li>'."\n";
					}

					$rep .= '</ul>'."\n";
					$rep .= '</div>'."\n";
					$rep .= '<div class="nuts_gallery_spacer"></div>'."\n";

					// add custom js ?
					if($generate_js)
					{
						$rep .= "\n";
						$rep .= '<script type="text/javascript">'."\n";
						$rep .= '$(document).ready(function() {'."\n";
						$rep .= '	$(\'a.nuts_gallery_'.$nuts_gallery_id.'\').fancybox({\'centerOnScroll\':true});'."\n";
						$rep .= '});'."\n";
						$rep .= '</script>'."\n";
					}

				}

				$rep = $this->setNutsCommentMarkup('gallery '.$cmd, $rep);
				$out = str_replace("{@NUTS	TYPE='GALLERY'	NAME='$cmd'}", $rep, $out);
			}
			$out = $this->formatOutput($out);
		}
		// </editor-fold>

		// NUTS Media replacement ******************************************************************
		// <editor-fold defaultstate="collapsed" desc="">
		preg_match_all("#\{@NUTS	TYPE='MEDIA'	OBJECT='(.*)'	ID='(.*)'	NAME='(.*)'\}#U", $out, $matches);
		if(count($matches[0]) > 0)
		{
			$tmp_num = 0;
			foreach($matches[0] as $cmd)
			{
				$rep = '';

				$ID  = (int)$matches[2][$tmp_num];
				$sql = "SELECT
								ID, Type, Name, Url, Parameters, EmbedCode
						FROM
								NutsMedia
						WHERE
								ID = $ID AND
								Deleted = 'NO'";
				$this->DoQuery($sql);
				if($this->dbNumRows() > 0)
				{
					$row = $this->DBFetch();

					// decoding parameters
					$parameters  = array();
					$ps = explode('@@', $row['Parameters']);
					foreach($ps as $r)
					{
						if(!empty($r))
						{
							$r2 = explode('=>', $r);
							if(!empty($r2[0]))
								$parameters[$r2[0]] = $r2[1];
						}
					}


                    if($row['Type'] == 'YOUTUBE VIDEO')
                    {
                        $params = explode('@@', $row['Parameters']);
                        $paramsX = array();
                        foreach($params as $param)
                        {
                            if(!empty($param))
                            {
                                list($p,$v) = explode('=>', $param);
                                $paramsX[$p] = $v;
                            }
                        }

                        $rep = youtubeGetPlayer($row['ID'], $paramsX['url'], $paramsX['width'], $paramsX['height']);
                    }
                    elseif($row['Type'] == 'DAILYMOTION')
                    {
                        $params = explode('@@', $row['Parameters']);
                        $paramsX = array();
                        foreach($params as $param)
                        {
                            if(!empty($param))
                            {
                                list($p,$v) = explode('=>', $param);
                                $paramsX[$p] = $v;
                            }
                        }

                        $rep = dailymotionGetPlayer($row['ID'], $paramsX['url'], $paramsX['width'], $paramsX['height']);
                    }
					elseif($row['Type'] == 'AUDIO')
					{
						/*$autoreplay = ($parameters['autoreplay'] == 'NO') ? 0 : 1;

						$rep = '<div class="media_audio"><object wmode="transparent" type="application/x-shockwave-flash" data="/library/js/dewplayer/dewplayer.swf?mp3='.$row['Url'].'&amp;showtime=1&amp;autoreplay='.$autoreplay.'" width="200" height="20">
							<param name="movie" value="/library/js/dewplayer/dewplayer.swf?mp3='.$row['Url'].'&amp;showtime=1&amp;autoreplay='.$autoreplay.'" />
							<param name="wmode" value="transparent" />
						 </object></div>';

                        // html5 fallback mp3
                        $browser = $this->getBrowserInfo();
                        if(@stripos($browser['name'], 'mozilla firefox') === false)
                        {
                            $tmp = '<div class="media_audio">'.CR;
                            $tmp = '<audio controls>'.CR;
                            $tmp .= '<source src="'.$row['Url'].'"  type="audio/mpeg">'.CR;
                            $tmp .= '</audio>';
                            $tmp .= '</div>';


                            $rep = $tmp;
                        }*/

                        $params = explode('@@', $row['Parameters']);
                        $paramsX = array();
                        foreach($params as $param)
                        {
                            if(!empty($param))
                            {
                                list($p,$v) = explode('=>', $param);
                                $paramsX[$p] = $v;
                            }
                        }

                        $rep = mediaGetAudioPlayer($row['ID'], $row['Url'], $paramsX);

					}
					elseif($row['Type'] == 'VIDEO')
					{

						$flash_vars = 'flv='.$row['Url'].'&amp;';
						$flash_vars .= 'width='.$parameters['width'].'&amp;';
						$flash_vars .= 'height='.$parameters['height'].'&amp;';
						$flash_vars .= 'loop='.$parameters['loop'].'&amp;';
						$flash_vars .= 'autoplay='.$parameters['autoplay'].'&amp;';
						$flash_vars .= 'showstop=1'.'&amp;';
						$flash_vars .= 'showvolume=1'.'&amp;';
						$flash_vars .= 'autoload=0'.'&amp;';
						$flash_vars .= 'showtime=1'.'&amp;';
						$flash_vars .= 'showplayer=always&amp;';
						$flash_vars .= 'showloading=always&amp;';
						$flash_vars .= 'showfullscreen=1&amp;';
						$flash_vars .= 'ondoubleclick=fullscreen&amp;';
						$flash_vars .= 'loadonstop=0&amp;';
						$flash_vars .= 'showiconplay=1&amp;';

						$flash_vars .= 'showmouse=autohide&amp;';
						$flash_vars .= 'srt=1&amp;';
						$flash_vars .= 'margin=2&amp;';

						$flash_vars .= 'playercolor=a3a3a3&amp;';
						$flash_vars .= 'playeralpha=100&amp;';
						$flash_vars .= 'iconplaybgalpha=50&amp;';
						$flash_vars .= 'iconplaybgcolor=ffffff&amp;';


						$flash_vars .= 'startimage='.$parameters['startimage'].'&amp;';
						$flash_vars .= 'top1='.$parameters['top1'].'&amp;';
						$flash_vars .= 'skin='.$parameters['skin'].'&amp;';

						// logo ?



						$rep = '<div class="media_video"><object type="application/x-shockwave-flash" data="/nuts/player_flv_maxi.swf" width="'.$parameters['width'].'" height="'.$parameters['height'].'">
								<param name="movie" value="/nuts/player_flv_maxi.swf" />
								<param name="allowFullScreen" value="true" />
								<param name="wmode" value="transparent" />
								<param name="FlashVars" value="'.$flash_vars.'" />
							</object></div>';
					}
                    elseif($row['Type'] == 'IFRAME')
                    {
                        $params = explode('@@', $row['Parameters']);
                        $paramsX = array();
                        foreach($params as $param)
                        {
                            if(!empty($param))
                            {
                                list($p,$v) = explode('=>', $param);
                                $paramsX[$p] = $v;
                            }
                        }

                        $rep = "<iframe id=\"nuts_iframe_{$row['ID']}\" class=\"nuts_iframe\" src=\"{$paramsX['url']}\" frameborder=\"0\" width=\"{$paramsX['width']}\" height=\"{$paramsX['height']}\"></iframe>";
                    }
					elseif($row['Type'] == 'EMBED CODE')
					{
						$rep = $row['EmbedCode'];
					}
				}

				$rep = $this->setNutsCommentMarkup('media '.strtolower($matches[1][$tmp_num]).' '.$matches[3][$tmp_num], $rep);
				$out = str_replace($cmd, $rep, $out);

				$tmp_num++;
			}
			$out = $this->formatOutput($out);

		}
		// </editor-fold>

		// FORMS  replacement ! *************************************************************************************************
		// <editor-fold defaultstate="collapsed">

        preg_match_all("#\{@NUTS	TYPE='FORM'	NAME='(.*)'\}#U", $out, $matches);
        if (count($matches) >= 2)
		{
            $matches[1] = array_unique($matches[1]);

            // get each block with content
            foreach ($matches[1] as $cmd)
			{
                $rep = $this->getForm($cmd);
                $rep = $this->setNutsCommentMarkup('form ' . $cmd, $rep);
                $out = str_replace("{@NUTS	TYPE='FORM'	NAME='$cmd'}", $rep, $out);
            }
			$out = $this->formatOutput($out);
        }
        // </editor-fold>

		// SURVEY  replacement ! *************************************************************************************************
		// <editor-fold defaultstate="collapsed">

        preg_match_all("#\{@NUTS	TYPE='SURVEY'	ID='(.*)'	TITLE='(.*)'\}#U", $out, $matches);
        if (count($matches) >= 2)
		{
            $matches[1] = array_unique($matches[1]);

            // get each block with content
            foreach ($matches[1] as $cmd)
			{
                $rep = $this->getSurvey($cmd);
                $rep = $this->setNutsCommentMarkup('survey '. $cmd, $rep);
                $out = str_replace($matches[0][0], $rep, $out);
            }
			$out = $this->formatOutput($out);
        }
        // </editor-fold>

        // PATTERN replacement ##############################################################################
		//<editor-fold defaultstate="collapsed">
        $sql = "SELECT
                        Pattern,
                        Type,
                        Code,
                        BlocStart,
                        BlocEnd
                FROM
                        NutsPattern
                WHERE
                        Deleted = 'NO'";
        $this->doQuery($sql);
        while($row = $this->dbFetch())
        {
            $rep = $row['Code'];
            $bloc_inside_detected = false;

            if(!empty($row['BlocStart']) && !empty($row['BlocEnd']))
            {
                $bloc_original = $this->extractStr($out, $row['BlocStart'], $row['BlocEnd'], true);
                $bloc_inside_detected = true;
            }

            if($row['Type'] == 'REGEX')
            {
                if(!$bloc_inside_detected)
                {
                    $out = preg_replace($row['Pattern'], $rep, $out);
                }
                else
                {
                    $bloc_parsed = preg_replace($row['Pattern'], $rep, $bloc_original);
                    $out = str_replace($bloc_original, $bloc_parsed, $out);
                }
            }
            elseif($row['Type'] == 'PHP')
            {
                eval("\$rep = $rep;");
                if(!$bloc_inside_detected)
                {
                    $out = str_replace($row['Pattern'], $rep, $out);
                }
                else
                {
                    $bloc_parsed = str_replace($row['Pattern'], $rep, $bloc_original);
                    $out = str_replace($bloc_original, $bloc_parsed, $out);
                }
            }
            elseif($row['Type'] == 'HTML')
            {
                if(!$bloc_inside_detected)
                {
                    $out = str_replace($row['Pattern'], $rep, $out);
                }
                else
                {
                    $bloc_parsed = str_replace($row['Pattern'], $rep, $bloc_original);
                    $out = str_replace($bloc_original, $bloc_parsed, $out);
                }
            }
        }

        $out = $this->formatOutput($out);

		//</editor-fold>

		// parsing ********************************************************************
		//<editor-fold defaultstate="collapsed">
		preg_match_all("#\{@(.*)\}#U", $out, $matches);
		$pages = array();
		if(count($matches) == 2)
		{
			$matches[1] = array_unique($matches[1]);

			foreach($matches[1] as $cmd)
			{
				// variable
				if(!preg_match('/^NUTS/', $cmd))
				{
					// NUTS VARS  replacement ! *************************************************************************************************
					// $out = $this->parseNutsVars($cmd, $out);
				}
				else
				{
					$cmd2 = $this->parseCommand($cmd);

					## NAVBAR ############################################################################
					// <editor-fold defaultstate="collapsed">
					if(@$cmd2['TYPE'] == 'NAVBAR')
					{
						// get navbar
						$separator_type = (isset($cmd2['SEPARATOR'])) ? $cmd2['SEPARATOR'] : '| ';
						$navbar = $this->getNavbar($this->pageID, $separator_type);

						$rep = $this->setNutsCommentMarkup('navbar', $navbar);
						$out = str_replace("{@$cmd}", $rep, $out);
					}
					// </editor-fold>

					## MENU ############################################################################
					// <editor-fold defaultstate="collapsed">
					elseif(@$cmd2['TYPE'] == 'MENU' || @$cmd2['TYPE'] == 'ZONE')
					{
						// list all children of the current page
						if(!isset($cmd2['CONTENT']))$cmd2['CONTENT'] = 'ALL CHILDRENS';
						if($cmd2['CONTENT'] == 'ALL CHILDRENS')
						{
							// include parent page ?
							if(!isset($cmd2['INCLUDE_PARENT']))$cmd2['INCLUDE_PARENT'] = 0;

							// get menu ID by name
							if($cmd2['TYPE'] == 'ZONE')
							{
								$sql = "SELECT ID FROM NutsZone WHERE Name = '".addslashes($cmd2['NAME'])."' AND Deleted = 'NO'";
								$this->doQuery($sql);
								$cmd2['ZONE_ID'] = $this->getOne();
								$cmd2['ID']  = 'ZONE '.$cmd2['ZONE_ID'];
							}

							// get all pages
							if(!isset($cmd2['ZONE_ID']))
							{
								$cmd2['ZONE_ID'] = 0;
								$rep = $this->getMenu((int)@$cmd2['ID'], $cmd2['OUTPUT'], $cmd2['CSS'], $cmd2['ATTRIBUTES'], 0, -1, $cmd2['INCLUDE_PARENT']);
								/*if($cmd2['OUTPUT'] == 'UL')
								{
									$rep = sprintf('<ul id="%s">'."\n".'%s'."\n".'</ul>', $cmd2['CSS'], $rep);
								}*/
							}
							else
							{
								// get zone css
								$this->doQuery("SELECT CssName FROM NutsZone WHERE ID={$cmd2['ZONE_ID']}");

								if(!isset($cmd2['CSS']))$cmd2['CSS'] = '';
								$cmd2['CSS'] .= $this->getOne();

								if(!isset($cmd2['ATTRIBUTES']))$cmd2['ATTRIBUTES'] = '';

								// get all pages
								$this->doquery("SELECT ID, Language, MenuName, VirtualPagename FROM NutsPage WHERE NutsPageID = 0 AND ZoneID = {$cmd2['ZONE_ID']} AND Language = '{$this->language}' AND MenuVisible = 'YES' AND ".$this->sqlAddedAccessRestricted()." ".$this->sqlAdded()." ORDER BY Position");
								$rs = $this->getData();

								if(count($rs) == 0)
								{
									$rep = '';
								}
								else
								{
									$rep = sprintf('<ul id="%s">'."\n", $cmd2['CSS']);
									foreach($rs as $r)
									{
										$class_selected = ($this->pageID != $r['ID']) ? '' : ' class="selected"';
										$rep .= sprintf('<li data-page-id="'.$r['ID'].'" id="%s%s" %s><a href="%s">%s</a>'."\n", $cmd2['CSS'], $r['ID'], $class_selected, $this->getUrl($r['ID'], $r['Language'], $r['VirtualPagename']), $r['MenuName']);
										$rep .= "\t".$this->getMenu($r['ID'], 'LI', $cmd2['CSS'], $cmd2['ATTRIBUTES'], 1)."\n";
										$rep .= "\t</li>\n";
									}
									$rep .= "</ul>\n";
								}
							}
						}
						if(!isset($cmd2['ID']))$cmd2['ID'] = $cmd2['ZONE_ID'];
						$rep = $this->setNutsCommentMarkup('menu '.$cmd2['ID'], $rep);

						// echo  "{@$cmd}".' => '.$rep."<br>";
						$out = str_replace("{@$cmd}", $rep, $out);
					}
					// </editor-fold>
				}
			}

			$out = $this->formatOutput($out);
		}
		//</editor-fold>

		// PAGE replacement ! *************************************************************************************************
		// <editor-fold defaultstate="collapsed">
		$pages = array();
		$pages_url_pattern = array();
		$pages_name_pattern = array();
		preg_match_all("#\{@(.*)\}#U", $out, $matches);
		if(count($matches) == 2)
		{
			$matches[1] = array_unique($matches[1]);
			$ids = array();
			foreach($matches[1] as $cmd)
			{
				$cmd2 = $this->parseCommand($cmd);
				if(isset($cmd2['TYPE']) && $cmd2['TYPE'] == 'PAGE')
				{
					$pages[] = @(int)$cmd2['ID'];
					if($cmd2['CONTENT'] == 'URL')
						$pages_url_pattern[end($pages)] = '{@'.$cmd.'}';
					else
						$pages_name_pattern[end($pages)] = '{@'.$cmd.'}';
				}
			}
		}



		// page replacement
		if(count($pages) > 0)
		{
			$this->doQuery("SELECT
									ID,
									VirtualPagename,
									Language,
									MenuName
							FROM
									NutsPage
							WHERE
									ID IN (".join(', ', $pages).") ".$this->sqlAdded());
			$done = array();
			while($row = $this->dbFetch())
			{
				$uri = $this->getUrl($row['ID'], $row['Language'], $row['VirtualPagename']);

				// $resp = array("{@NUTS	TYPE='PAGE'	CONTENT='URL'	ID='{$row['ID']}'}", "{@NUTS	TYPE='PAGE'	CONTENT='URL'	FROM='$name'	ID='{$row['ID']}'}");
				$resp = array("{@NUTS	TYPE='PAGE'	CONTENT='URL'	ID='{$row['ID']}'}", @$pages_url_pattern[$row['ID']]);
				$out = str_replace($resp, $uri, $out);

				// $resp = array("{@NUTS	TYPE='PAGE'	CONTENT='MENU_NAME'	ID='{$row['ID']}'}", "{@NUTS	TYPE='PAGE'	CONTENT='MENU_NAME'	FROM='$name'	ID='{$row['ID']}'}");
				$resp = array("{@NUTS	TYPE='PAGE'	CONTENT='MENU_NAME'	ID='{$row['ID']}'}", @$pages_name_pattern[$row['ID']]);
				$out = str_replace($resp, $row['MenuName'], $out);

				$done[] = $row['ID'];
			}

			// replace erased page by 0 ?
			foreach($pages as $p)
			{
				if(!in_array($p, $done))
				{
					$out = str_replace("{@NUTS	TYPE='PAGE'	CONTENT='URL'	ID='{$row['ID']}'}", "/$lang/0.html", $out);
					$out = str_replace("{@NUTS	TYPE='PAGE'	CONTENT='MENU_NAME'	ID='{$row['ID']}'}", 'Untitled', $out);
				}
			}
		}
		// </editor-fold>

		// NUTS VARS replacement ! *************************************************************************************************
		// <editor-fold defaultstate="collapsed">
		preg_match_all("#\{@(.*)\}#U", $out, $matches);
		if(count($matches) == 2)
		{
			$matches[1] = array_unique($matches[1]);
			foreach($matches[1] as $cmd)
			{
				$out = $this->parseNutsVars($cmd, $out);
			}
		}

		// second times form variable inside variable
		preg_match_all("#\{@(.*)\}#U", $out, $matches);
		if(count($matches) == 2)
		{
			$matches[1] = array_unique($matches[1]);
			foreach($matches[1] as $cmd)
			{
				$out = $this->parseNutsVars($cmd, $out);
			}
		}

		// dynamic patterns
		foreach($this->patterns as $pattern => $rep)
		{
			 $out = str_replace($pattern, $rep, $out);
		}
		$out = $this->formatOutput($out);

		// </editor-fold>


		// pseudo parsing for if condition
		$this->createVirtualTemplate($out);
		$out = parent::output();

		// I18N replacement ! *************************************************************************************************
		// <editor-fold defaultstate="collapsed">

		// form error interception - Company
		$out = str_replace(array('&lt;i18n&gt;','&lt;/i18n&gt;'), array('<i18n>', '</i18n>'), $out);

		if(count($this->languages) > 0 && $this->language != $this->languageDefault)
		{
			$this->doQuery("SELECT * FROM NutsI18n WHERE Deleted = 'NO'");

			$pattern = $rep = array();
			while($row = $this->dbFetch())
			{
				$pattern[] = '<i18n>'.$row['Pattern'].'</i18n>';
				$res = unserialize($row['Replacement']);
				$rep[] = $res[$this->language];
			}

			$out = str_replace($pattern, $rep, $out);
		}

		// suppress empty tags
		if(!isset($_GET['i18n']))
		{
			$out = str_replace(array('<i18n>','</i18n>'), '', $out);
		}


		// </editor-fold>


		// add special bottom bar at end page for shortcuts
		$out = $this->addNutsToolbar($out);

		// add special mainenece bottom bar
		$out = $this->addNutsMaintenanceToolbar($out);

		// add special meta robots
		if(!empty($this->vars['MetaRobots']))
		{
			$out = str_replace('<head>', '<head>'.CR.TAB.TAB.'<meta name="robots" content="'.$this->vars['MetaRobots'].'" />'.CR, $out);
		}

		// add dynamically header file
        $header_files_inserted_done = array();
		if(count($this->header_files_added))
		{
			$str = "\n\t<!-- dynamic plugins insertion -->";
			foreach($this->header_files_added as $hf)
			{
                if(!in_array($hf, $header_files_inserted_done))
                {
                    if($hf['type'] == 'css')
                    {
                        $str .= "\n\t<link href=\"{$hf['url']}\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />";
                    }
                    elseif($hf['type'] == 'js')
                    {
                        $str .= "\n\t<script type=\"text/javascript\"  src=\"{$hf['url']}\"></script>";
                    }
                    else
                    {
                        $str .= "\n\t{$hf['url']}";
                    }

                    $header_files_inserted_done[] = $hf['url'];
                }

			}
			$str .= "\n\t<!-- /dynamic plugins insertion -->";

			$out = str_replace('<head>', "<head>$str\n", $out);
		}

        // add dynamically header file
        if(count($this->header_after_files_added))
        {
            $str = "\n\t<!-- dynamic plugins insertion -->";
            foreach($this->header_after_files_added as $hf)
            {
                if(!in_array($hf, $header_files_inserted_done))
                {
                    if($hf['type'] == 'css')
                    {
                        $str .= "\n\t<link href=\"{$hf['url']}\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />";
                    }
                    elseif($hf['type'] == 'js')
                    {
                        $str .= "\n\t<script type=\"text/javascript\"  src=\"{$hf['url']}\"></script>";
                    }
                    else
                    {
                        $str .= "\n\t{$hf['url']}";
                    }

                    $header_files_inserted_done[] = $hf['url'];
                }
            }
            $str .= "\n\t<!-- /dynamic plugins insertion -->";

            $out = str_replace('</head>', "$str\n</head>", $out);
        }






		// output content ##############################################################################
		if($this->cacheIsPossible())
		{
			// initialisation
			if($this->pageCacheTime == -1)$this->cacheGetPageTime();
			if($this->pageCacheTime > 0)
			{
				$filename = $this->cacheGetFileName();
				$date = date("Y-m-d H:i:s");
				$out = str_replace('<head>', "<head>\n\t\t<!-- Nuts CMS cache generated at: $date -->", $out);
				file_put_contents($filename, $out);
           	}
		}

		$out = $this->chronoReplace($out);

		// Html compression *********************************************************************************************
		if(NUTS_HTML_COMPRESS)
		{
			// verify time for css
			$css_cache_creation = true;
			$cacheCssFile = WEBSITE_PATH.'/nuts_auto_compress.css';

			if(($stats = @stat($cacheCssFile)) && (($stats['mtime'] + (int)NUTS_HTML_COMPRESS_TIME) > mktime()))
			{
				$css_cache_creation = false;
			}

			// verify time for js
			$js_cache_creation = true;
			$cacheJsFile = WEBSITE_PATH.'/nuts_auto_compress.js';
			if(($stats = @stat($cacheJsFile)) && (($stats['mtime'] + (int)NUTS_HTML_COMPRESS_TIME) > mktime()))
			{
				$js_cache_creation = false;
			}


			$header_section = $this->extractStr($out, '<head>', '</head>');
			if(!empty($header_section))
			{
				// grep css
				preg_match_all('/\<link(.*)\>/i', $header_section, $matches);

				$css_files = array();
				$init = false;
				$first_match = "";
				$matches[0] = array_unique($matches[0]);
				foreach($matches[0] as $match)
				{
					$href = $this->extractStr($match, 'href="', '"');
					if(!empty($href) && preg_match('#\.css$#i', $href) && preg_match('#^(/|'.WEBSITE_URL.')#i', $href) && !preg_match('#(ie6|ie7|ie8|ie9|print)\.css$#i', $href))
					{
						$css_files[] = $href;
						if(!$init)
							$first_match = $match;
						else
							$out = str_replace($match, '', $out);
						$init = true;
					}
				}
				$css_files = array_unique($css_files);

				// add to css packer cache
				if($css_cache_creation)
				{
					$str = "";
					foreach($css_files as $css_file)
					{
						$css_file = str_replace(WEBSITE_URL, '', $css_file);
						$css_file = WEBSITE_PATH.$css_file;
						$css_file_name = basename($css_file);

						$cur_path = str_replace(WEBSITE_PATH, '', $css_file);
						$cur_path = str_replace('/'.$css_file_name, '', $cur_path);

						// $str .= "\n/* $css_file_name */\n\n";
						$css_content = file_get_contents($css_file);
						$css_content = str_replace('url (', 'url(', $css_content);
						$css_content = str_replace('url(/', 'url (/', $css_content);
						$css_content = str_replace('url(', 'url ('.$cur_path.'/', $css_content);
						$css_content = str_replace('url (', 'url(', $css_content);

						// $css_content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_content);
						// $str .= trim($css_content);
						$str .= $css_content;

						$str .= "\n";
					}
				}

				if(!empty($first_match))
				{
					if($css_cache_creation)
					{
						file_put_contents($cacheCssFile, CssMin::minify($str));
					}
					$out = str_replace($first_match, '<link rel="stylesheet" type="text/css" href="/nuts_auto_compress.css" media="all" />', $out);
				}


				// grep js
				preg_match_all('/(<script.*>)(.*)(<\/script>)/isU', $header_section, $matches);

				$js_files = array();
				$init = false;
				$first_match = "";
				$matches[0] = array_unique($matches[0]);
				foreach($matches[0] as $match)
				{
					$src = $this->extractStr($match, 'src="', '"');
					if(!empty($src) && preg_match('#\.js$#i', $src) && preg_match('#^(/|'.WEBSITE_URL.')#i', $src) && !preg_match('#(ie6|ie7|ie8|ie9)\.js$#i', $src))
					{
						$js_files[] = $src;
						if(!$init)
							$first_match = $match;
						else
							$out = str_replace($match, '', $out);
						$init = true;
					}
				}
				$js_files = array_unique($js_files);

				// add to js packer cache
				if($js_cache_creation)
				{
					$str = "";
					foreach($js_files as $js_file)
					{
						$js_file = str_replace(WEBSITE_URL, '', $js_file);
						$js_file = WEBSITE_PATH.$js_file;
						$js_file_name = basename($js_file);

						$str .= "\n/* $js_file_name */\n\n";
						$js_content = file_get_contents($js_file);
						// $js_content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $js_content);
						$str .= $js_content;
						$str .= "\n";
					}
				}

				if(!empty($first_match))
				{
					if($js_cache_creation)file_put_contents($cacheJsFile, JSMin::minify($str));
					$out = str_replace($first_match, '<script type="text/javascript" src="/nuts_auto_compress.js"></script>', $out);
				}
			}




		}

		// Tidy correction *************************************************************************************************
		if(NUTS_TIDY)
		{
			// page exception
			if(!in_array($this->pageID, $GLOBALS['nuts_tidy_pageID_exceptions']))
			{
				$tidy = tidy_parse_string($out, $GLOBALS['nuts_tidy_config'], 'UTF8');
				$tidy->cleanRepair();
				$out = $tidy;
			}
		}

		// html compress final
		if(NUTS_HTML_COMPRESS)
		{
			$lines = explode("\n", $out);
			$lines = array_map('trim', $lines);
			$out = join("\n", $lines);
			$out = str_replace("\n\n", "\n", $out);
		}


		die($out);
	}



	/**
	 * Add chrono to the page
	 *
	 * @param string $out
	 * @return string
	 */
	private function chronoReplace($out)
	{
		$chrono_stop = microtime(true);
		$diff = $chrono_stop - $this->pageChronoStart;
		$diff = round($diff, 5);
      	$out = str_replace("[!PAGE_CHRONO]", $diff, $out);
		return $out;
	}


    /**
     * Return Navbar
     *
     * @param int $pageID
     * @param string $separator default is |
     */
    public function getNavBar($pageID, $separator='')
    {
		if($this->isHome)return "";

		$pageID = (int)$pageID;
		if($pageID == 0)return '';

		$sql_tpl = "SELECT
							ID,
							Language,
							ZoneID,
							NutsPageID,
							MenuName,
							VirtualPageName
					FROM
							NutsPage
					WHERE
							ID = %s AND
							Deleted = 'NO'";
		if($this->mode == 'view')
		{
			$sql_tpl .= " AND State = 'PUBLISHED'";
		}

		// first one
		$sql = sprintf($sql_tpl, $pageID);
		$this->doQuery($sql);
		$row = $this->dbFetch();

		$r2 = array();
		$r2[] = $row;

		do
		{
			$sql = sprintf($sql_tpl, (int)$r2[count($r2)-1]['NutsPageID']);
			$this->doQuery($sql);

			if($this->dbNumRows() == 0)break;
			$row = $this->dbFetch();
			$pageID = (int)$row['NutsPageID'];
			$r2[] = $row;
		}
		while($row['NutsPageID'] != 0);


		// has father
		$r2 = array_reverse($r2);

		// return HTML menu
		$str = '';
		// $separator = (empty($separator)) ? '| ' : $separator;
		for($i=0; $i < count($r2); $i++)
		{
			$m = $r2[$i];
			if(!empty($m['MenuName']))
			{
				$str .= $separator;
				$str .= ' <a href="';

				// last one no string
				if($i == count($r2)-1)
				{
					$uri = "";
				}
				else
				{
					$uri = $this->getUrl($m['ID'], $m['Language'], $m['VirtualPageName']);
				}

				$str .= $uri;

				$str .= '">';
				$str .= $m['MenuName'];
				$str .= '</a> ';
			}
		}

		// add zoneID first ?
		if(count($r2) > 0 && $r2[0]['ZoneID'] != 0)
		{
			$this->doQuery("SELECT Name, Url, Navbar FROM NutsZone WHERE ID = {$row['ZoneID']}");
			$row = $this->dbFetch();
			if($row['Navbar'] == 'YES')
				$str = $separator.' <a href="'.$row['Url'].'">'.$row['Name'].'</a> '.$str;
		}

		return $str;

    }



    /**
     *
     * Return html menu
     *
     * @param int $pageID
     * @param string $output
     * @param string $css = ""
     * @param string $attr = ""
     * @param int $level = 0
     * @param int $qID = '' do not touch
     *
     * @return string html_menu
     */
	public function getMenu($pageID, $output, $css='', $attr='', $level=0, $qID='', $include_parent_page=0)
	{
		static $r = array();
		$str = '';
		//static $level;
		$chars = str_pad("\t", $level+1);

		// check if parentID is oki
		$sql_access_restricted = $this->sqlAddedAccessRestricted();
		$this->doQuery("SELECT
								ID,
								Language,
								VirtualPagename,
								MenuName,
								NutsPageID,
								CustomVars
						FROM
								NutsPage
						WHERE
								ID = $pageID AND
								Language = '{$this->language}' AND
								MenuVisible = 'YES' AND

								$sql_access_restricted

								".$this->sqlAdded());
		if($this->dbNumRows() == 0)
		{
			return $str;
		}


		// include parent page ?
		if(count($r) == 0 && $include_parent_page)
		{
			$this->doQuery("SELECT
									ID,
									Language,
									VirtualPagename,
									MenuName,
									NutsPageID,
									CustomVars
							FROM
									NutsPage
							WHERE
									ID = $pageID AND
									Language = '{$this->language}' AND
									MenuVisible = 'YES' AND

									$sql_access_restricted

									".$this->sqlAdded());
			//if($output == 'LI' || $output == 'LI>UL')
			$pattern = $chars.'<li data-page-id="%d" id="%s%s" %s>'."\n".'<a href="%s" %s>%s</a>'."\n".$chars.'</li>'."\n";
			//else
				//$pattern = $output;

			$qID = $this->getQueryID();
			$r[$qID] = $this->dbFetch();
			$attr_tmp = $this->getMenuAttribute($r, $qID);
			$attr_tmp = trim($attr_tmp);

			$class_selected = ($this->pageID != $r[$qID]['ID']) ? '' : ' class="selected"';
			$str .= sprintf($pattern, $css,  $r[$qID]['ID'], $class_selected, $this->getUrl($r[$qID]['ID'], $r[$qID]['ID'], $r[$qID]['Language'], $r[$qID]['VirtualPagename']), $attr_tmp, $r[$qID]['MenuName']);
		}

		// parsing
		$sql = "SELECT
						ID,
						Language,
						VirtualPagename,
						MenuName,
						NutsPageID,
						CustomVars,
						_HasChildren
				FROM
						NutsPage
				WHERE
						NutsPageID = $pageID AND
						MenuVisible = 'YES' AND
						Language = '{$this->language}' AND

						$sql_access_restricted

						".$this->sqlAdded()."
				ORDER BY
						Position";
		$this->doQuery($sql);

		$qID = $this->getQueryID();

		while($r[$qID] = $this->dbFetch())
		{
			$attr_tmp = $this->getMenuAttribute($r, $qID);

			//if($output == 'LI' || $output == 'LI>UL')
				$pattern = $chars.'	<li data-page-id="%d" id="%s%s" %s><a href="%s" %s>%s</a>';
			//else
				//$pattern = $output;

			$class_selected = ($this->pageID != $r[$qID]['ID']) ? '' : ' class="selected"';
			$str .= sprintf($pattern, $r[$qID]['ID'], $css,  $r[$qID]['ID'], $class_selected, $this->getUrl($r[$qID]['ID'], $r[$qID]['Language'], $r[$qID]['VirtualPagename']), $attr_tmp, $r[$qID]['MenuName']);

			//echo str_repeat(' ', $level)."- {$r[$qID]['MenuName']}\n";

			// childrens found ?
			$c = 0;
			if($r[$qID]['_HasChildren'] == 'YES')
			{
				$this->doQuery("SELECT COUNT(*) FROM NutsPage WHERE	NutsPageID = {$r[$qID]['ID']} AND MenuVisible = 'YES' AND $sql_access_restricted ".$this->sqlAdded());
				$c = (int)$this->getOne();
			}

			if($c > 0)
			{
				//echo str_repeat(' ', $level)." - {$r[$qID]['MenuName']}: {$c} rows found\n";
				$str .= $this->getMenu($r[$qID]['ID'], $output, $css, $attr, $level++, $qID);
			}

			$this->setQueryID($qID);

			// if($output == 'LI' || $output == 'LI>UL')
				$str .= $chars."</li>\n";
		}


		$str = trim($str);

		//if($level >= 0 && in_array($output, array('LI', 'LI>UL')) && !empty($str))
		if($level >= 0 && !empty($str))
		{
			$ul = "<ul>";
			if($level == 0)$ul = '<ul id="'.$css.'">';

			$str = "\n".$chars.'	'.$ul."\n".$chars.$str."\n".$chars.'	</ul>'."\n";
		}

		return $str;
	}

	/**
	 * Add nuts comment tags
	 *
	 * @param string $label
	 * @param string $content
	 * @return string
	 */
	private function setNutsCommentMarkup($label, $content)
	{
		$label = trim($label);

		$str = "<!-- nuts $label -->\n";
		$str .= $content;
		$str .= "\n<!-- /nuts $label -->\n";

		return $str;

	}

	/**
	 * Add menu attribute
	 *
	 * @param string $r
	 * @param int $qID
	 * @return string
	 */
	private function getMenuAttribute($r, $qID)
	{
		$attr_tmp = '';
		if(!empty($attr))
		{
			// custom variable
			if(preg_match('/^cf/', $attr))
			{
				$n = $attr;
				$n[0] = ''; $n[1] = '';
				$n = trim($n);

				$r[$qID]['CustomVars'] = unserialize($r[$qID]['CustomVars']);
				if(is_array($r[$qID]['CustomVars']))
				{
					if(!isset($r[$qID]['CustomVars'][$n]))
						$attr_tmp = ' '.$n.'=""';
					else
						$attr_tmp = ' '.$n.'="'.$r[$qID]['CustomVars'][$n].'"';
				}
				else
				{
					$attr_tmp = ' '.$n.'=""';
				}
			}
		}

		return $attr_tmp;
	}



	/**
     *
     * Return formatted url
     *
     * @param int $ID
     * @param string $language
     * @param string $virtualPagename
     * @param array $args
     *
     * @return string
     */
	public function getUrl($ID, $language='', $virtualPagename='', $args='' )
	{
		// get page information
		if(empty($language))
		{
			$this->doQuery("SELECT Language, VirtualPageName FROM NutsPage WHERE ID = $ID");
			$row = $this->dbFetch();
			$language = $row['Language'];
			$virtualPagename = $row['VirtualPageName'];
		}

        if(preg_match('/^http/i', $virtualPagename) || (!empty($virtualPagename) && $virtualPagename[0] == '/'))
		{
			if(preg_match('/^http/i', $virtualPagename))
			{
				$virtualPagename = $virtualPagename.'" target="_blank';
			}

			return $virtualPagename;
		}

		if(preg_match('/^{@NUTS/i', $virtualPagename))
		{
			$url = $virtualPagename;
		}
		else
		{
			$url = '/'.$language.'/'.$ID;
            if(is_array($args))
            {
                $countArgs = 1;
                foreach($args as $val)
                {
                    $url .= ',';
                    $url .= $val;
                    $countArgs++;
                }
            }

			if(!empty($virtualPagename))
			{
                if(is_array($args)) $url .= ',';
                else $url .= '-';
				$url .= strtolower($virtualPagename);
			}

			$url .= '.html';

			if($this->mode == 'preview' && strpos($url, '?nuts_preview=1') === false)
				$url .= '?nuts_preview=1';
		}

		return $url;
	}


	/**
     *
     * Execute region treatment
     *
     * @param string $regionName
     * @return string
     */
	private function getShowRecords($regionName)
	{
		$this->dbSelect("SELECT * FROM NutsRegion WHERE Name = '%s' AND Deleted = 'NO'", array($regionName));
        if($this->dbNumRows() == 0)return "Error: `$regionName` not found";

		// construct virtual template
		$tpl = '<bloc::data>

					%s

					<bloc::loop>
					%s
					</bloc::loop>
					%s

					%s

					<bloc::norecord>
					%s
					</bloc::norecord>

</bloc::data>';

		$r = $this->dbFetch();

		$pager = '';
		if($r['Pager'] == 'YES')
		{
            $start = '';
            $end = '';
            if($r['PreviousStartEndVisible'] == 'YES')
            {
                $start = '<bloc::start>
					        <a class="arrow_start" href="{_Url}">'.$r['PagerStartText'].'</a>
					  </bloc::start>';

                $end = '<bloc::end>
					        <a class="arrow_end" href="{_Url}">'.$r['PagerEndText'].'</a>
					  </bloc::end>';
            }

			$pager = '<table cellspacing="0" class="nuts_pager">
						<tr>
							<td>

                                [[BTN_START]]

								<bloc::previous>
								<a class="arrow_previous" href="{_Url}">'.$r['PagerPreviousText'].'</a>
								</bloc::previous>

								<bloc::pager>
									<bloc::in><span class="pager_in">{_Page}</span></bloc::in>
									<bloc::out><a class="pager_out" href="{_Url}">{_Page}</a></bloc::out>
								</bloc::pager>

								<bloc::next>
									<a class="arrow_next" href="{_Url}">'.$r['PagerNextText'].'</a>
								</bloc::next>

								[[BTN_END]]

							</td>
						</tr>
					</table>';


            $pager = str_replace('[[BTN_START]]', $start, $pager);
            $pager = str_replace('[[BTN_END]]', $end, $pager);

		}


		$tpl = sprintf($tpl, $r['HtmlBefore'], $r['Html'], $r['HtmlAfter'], $pager, $r['HtmlNoRecord']);


		// rewrite page code !
		// caption code
		// previous + pager + next
		if(empty($r['HookData']))
		{
			$func_name = '';
		}
		else
		{
			$code = 'global $page, $nuts, $plugin;
					$qID = $page->getQueryID();
					'.$r['HookData'].'
					$page->setQueryID($qID);
					return $row;';

			$func_name = create_function('$row', $code);
		}

		// dynamic php code & query
		$sql = $r['Query'];
		if(!empty($r['PhpCode']))eval($r['PhpCode']);

        // repalce keyword in sql
        $sql = str_replace('{@PAGE_ID}', $this->vars['ID'], $sql);


		// output
		$this->createVirtualTemplate($tpl);
        if(!empty($r['SetUrl']))$this->setUrl($r['SetUrl']);
		$this->showRecords($sql, (int)$r['Result'], $func_name);
		$str = $this->output();

		return $str;
	}

    /**
     * Select plugin template
	 * @param string $template
     */
	public function openPluginTemplate($template = 'template.html')
	{
		$tpl = 'plugins/'.$this->plugin_real_name.'/www/'.$template;
		$user_tpl = NUTS_THEMES_PATH.'/'.$this->theme.'/_'.$this->plugin.'.html';

		if(file_exists($user_tpl))
			$this->open($user_tpl);
		else
			$this->open($tpl);
	}
	/**
     * include plugin file configuration
     */
	public function includeConfigurationFile()
	{
		$cf = file_get_contents($this->plugin_path.'/config.inc.php');
		$cf = str_replace('<?php' , '', $cf);
		$cf = str_replace('?>' , '', $cf);
		eval($cf);
	}

	/**
	 * Assign a global template
	 * @param string $template
	 */
	public function setMainTemplate($template)
	{
		$this->vars['Template'] = $template;
	}

	/**
	 * Parse Nuts variables like {@..}
	 *
	 * @param array $cmd
	 * @param string $out
	 * @return string output
	 */
	private function parseNutsVars($cmd, $out)
	{
		$cj = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		$cjx = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');

		$cm = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$cmx = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');


		switch($cmd)
		{
			case 'THEME_URL':
				$rep = NUTS_THEMES_URL.'/'.$this->theme;
			break;

			case 'PAGE_ID':
				$rep = @$this->vars['ID'];
			break;

			case 'PAGE_LANGUAGE':
				$rep = $this->vars['Language'];
			break;

			case 'HEADER_IMAGE':
				$rep = (empty($this->vars['HeaderImage'])) ? '' : NUTS_HEADER_IMAGES_URL.'/'.$this->vars['HeaderImage'];
			break;

			case 'PAGE_H1':
				$rep = $this->vars['H1'];
			break;

            case 'PAGE_CONTENT_RESUME':
                $rep = $this->vars['ContentResume'];
            break;

			case 'PAGE_CONTENT':
				$rep = $this->vars['Content'];
			break;

			case 'PAGE_TITLE':
				$rep = $this->vars['MetaTitle'];
				if($this->plugin == 'news')
					$rep = $this->vars['Title'];
			break;

			case 'PAGE_KEYWORDS':
				$rep = $this->vars['MetaKeywords'];
				if($this->plugin == 'news')
					$rep = str_replace("\n", ',', $this->vars['Tags']);
			break;

			case 'PAGE_DESCRIPTION':
				$rep = $this->vars['MetaDescription'];
			break;



			// page author ********************************************************
			case 'PAGE_AUTHOR_FIRSTNAME':
				$rep = $this->vars['AuthorFirstName'];
			break;

			case 'PAGE_AUTHOR_LASTNAME':
				$rep = $this->vars['AuthorLastName'];
			break;

			case 'PAGE_AUTHOR_EMAIL':
				$rep = $this->vars['AuthorEmail'];
			break;

			// page date **********************************************************
			case strpos($cmd, 'PAGE_DATE_CREATE::') !== false:
				$cmd2 = str_replace('PAGE_DATE_CREATE::', '', $cmd);
				$rep = date($cmd2, $this->vars['DateCreationStamp']);

				if($this->language == 'fr')
				{
					$rep = str_replace($cj, $cjx, $rep);
					$rep = str_replace($cm, $cmx, $rep);
				}

			break;

			case strpos($cmd, 'PAGE_DATE_UPDATE::') !== false:

				if(empty($this->vars['DateUpdateStamp']))
				{
					$rep = '';
				}
				else
				{
					$cmd2 = str_replace('PAGE_DATE_UPDATE::', '', $cmd);
					$rep = date($cmd2, $this->vars['DateUpdateStamp']);

					if($this->language == 'fr')
					{
						$rep = str_replace($cj, $cjx, $rep);
						$rep = str_replace($cm, $cmx, $rep);
					}
				}

			break;

			// pager ********************************************************
			case 'PAGER_PAGE':
				$rep = $this->PageNumber;
			break;

			case 'PAGER_COUNT':
				$rep = $this->PageCount;
			break;

            default:

				$rep = "Error: `$cmd`";
				$IP_long = (float)ip2long($this->getIP());

				if(NUTS_LOG_ERROR_TAGS == true)
				{
					$this->dbSelect("SELECT ID FROM NutsLog WHERE Application = '_fo-error' AND Action = '%s' AND Resume = '%s' LIMIT 1", array($rep, $_SERVER['REQUEST_URI']));
					if($this->dbNumRows() == 0)
					{
						$this->dbInsert('NutsLog', array(
														'DateGMT' => 'NOW()',
														'Application' => '_fo-error',
														'Action' => $rep,
														'Resume' => $_SERVER['REQUEST_URI'],
														'IP' => $IP_long));
					}
				}



            break;

		}

		$out = str_replace("{@$cmd}", $rep, $out);

		return $out;
	}

    /**
     * Set the $Globals NUTS_CONTENT
     */
    public function setNutsContent($output='NUTS_TEMPLATE')
    {
        if($output == 'NUTS_TEMPLATE') $output = $this->output();
        $GLOBALS['NUTS_CONTENT'] = $output;
    }

	/**
	 * get current pageID
	 * @return int pageID
	 */
    public function getPageID() {
        return $this->pageID;
    }


	/**
	 * Verify condition for caching file (no post, no bot)
	 * @return boolean
	 */
	private function cacheIsPossible()
	{
		if(!$_POST && $this->mode == 'view' && $this->pageID != 0 && (isset($_SERVER["HTTP_USER_AGENT"]) && stripos($_SERVER["HTTP_USER_AGENT"], "bot") === false && stripos($_SERVER["HTTP_USER_AGENT"], "crawler") === false))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get Cache file name
	 * @return file path
	 */
	 private function cacheGetFileName()
	 {
		$cache_filename = WEBSITE_PATH.'/cache/'.base64_encode($_SERVER['REQUEST_URI']).".html";
		return $cache_filename;
	 }


	/**
	 * Verify is cache file exists (language_base64encode uri)
	 * @return boolean
	 */
	private function cacheExists()
	{
		$cache_filename = $this->cacheGetFileName();
		if(!file_exists($cache_filename))
		{
			return false;
		}

		$this->cacheGetPageTime();
		if($this->pageCacheTime == 0)
		{
			return false;
		}

		if(!($stats = @stat($cache_filename)))
		{
			return false;
		}

		if(($stats['mtime'] + $this->pageCacheTime) < mktime())
		{
			@unlink($cache_filename);
			return false;
		}

		return true;
	}


	/**
	 * Get Cache Time in seconds
	 * @return int
	 */
	function cacheGetPageTime()
	{
		$this->doQuery("SELECT CacheTime FROM NutsPage WHERE ID = {$this->pageID}");
		$this->pageCacheTime = $this->dbGetOne();
	}


	/**
	 * Create the form
	 * @param string $formName
	 * @param string $form
	 */
	private function getForm($formName)
	{
		global $nuts;

		$sql = "SELECT
						NutsForm.*,
						NutsFormField.Name AS FieldName,
						NutsFormField.Label,
						NutsFormField.Type,
						NutsFormField.Required,
						NutsFormField.Attributes,
						NutsFormField.Email,
						NutsFormField.OtherValidation,
						NutsFormField.I18N,
						NutsFormField.Value,
						NutsFormField.PhpCode,
						NutsFormField.TextAfter,
						NutsFormField.FilePath,
						NutsFormField.FileAllowedExtensions,
						NutsFormField.FileAllowedMimes,
						NutsFormField.FileMaxSize,
						NutsFormField.HtmlCode
			FROM
						NutsForm,
						NutsFormField
			WHERE
						NutsForm.ID = NutsFormField.NutsFormID AND
						NutsForm.Deleted = 'NO' AND
						NutsFormField.Deleted = 'NO' AND
						NutsForm.Name = '%s'
			ORDER BY
						NutsFormField.Position";

		$this->dbSelect($sql, array($formName));
        if($this->dbNumRows() == 0)return "Error: `$formName` not found";

		// construct form
		$formData = $this->dbGetData();
		$form = $formData[0];

		// captcha
		if($form['Captcha'] == 'YES')
		{
			if(!session_id())@session_start();
			$this->setCaptchaMax(-1);
			$TPLN_CAPTCHA_FIELD = $this->getCaptcha();
		}

		// language
		if($form['Language'] != 'AUTO')
		{
			$this->formSetLang($form['Language']);
		}
		else
		{
			$this->formSetLang($this->language);
		}

		$this->formSetName($form['Name']);
		$this->formSetDisplayMode('T');

		// php code before
		$form['FormBeforePhp'] = trim($form['FormBeforePhp']);
		if(!empty($form['FormBeforePhp']))eval($form['FormBeforePhp']);


		// form object names
		$obj_names = array();
		foreach($formData as $field)
		{
			if(!in_array($field['Type'], array('SECTION', 'HTML')))
			{
				$lbl = $field['Label'];
				if($field['I18N'] == 'YES')
					$lbl = '<i18n>'.$lbl.'</i18n>';
				$obj_names[$field['FieldName']] =  $lbl;
			}
		}
		$this->formSetObjectNames($obj_names);

		// create virtual template fields
		$sectionNb = 0;
		$form['fields'] = '';
		foreach($formData as $field)
		{
			// label
			$label = $field['Label'];
			if($field['I18N'] == 'YES')
				$field['Label'] = '<i18n>'.$label.'</i18n>';

			// required
			$required = '';
			if($field['Required'] == 'YES')
				$required = '<span class="required">*</span>';

			$input = '';
			$field['Attributes'] = str_replace("`", '"', $field['Attributes']);
			// input & password & file
			if($field['Type'] == 'TEXT' || $field['Type'] == 'PASSWORD' || $field['Type'] == 'FILE')
			{
				$input = '<input type="'.strtolower($field['Type']).'" name="'.$field['FieldName'].'" id="'.$field['FieldName'].'" '.$field['Attributes'].' />';
				// $input .= ' '.$field['TextAfter'];
			}
			// textarea
			elseif($field['Type'] == 'TEXTAREA')
			{
				$input = '<textarea name="'.$field['FieldName'].'" id="'.$field['FieldName'].'" '.$field['Attributes'].' rows="5" cols=""></textarea>';
			}
			// select
			elseif($field['Type'] == 'SELECT')
			{
				$input = '<select name="'.$field['FieldName'].'" id="'.$field['FieldName'].'" '.$field['Attributes'].'>'."\n";

				// options generation
				$options = explode("\n", $field['Value']);
				foreach($options as $option)
				{
					// | detected
					if(strpos($option, '|') !== false)
					{
						list($option_val, $option) = explode('|', $option);
					}
					else
					{
						$option = $option;
						$option_val = $option;
					}

					$option = trim($option);

					if($field['I18N'] == 'YES')
						$option = '<i18n>'.$option.'</i18n>';

                   	$input .= '<option value="'.$option_val.'">'.$option.'</option>'."\n";
				}
				$input .= '</select>'."\n";
			}
			// select-multiple
			elseif($field['Type'] == 'SELECT-MULTIPLE')
			{
				$input = '<select multiple name="'.$field['FieldName'].'[]" id="'.$field['FieldName'].'" '.$field['Attributes'].'>'."\n";

				// options generation
				$options = explode("\n", $field['Value']);
				foreach($options as $option)
				{
					// | detected
					if(strpos($option, '|') !== false)
					{
						list($option_val, $option) = explode('|', $option);
					}
					else
					{
						$option = $option;
						$option_val = $option;
					}
					$option = trim($option);


					if($field['I18N'] == 'YES')
						$option = '<i18n>'.$option.'</i18n>';
                   	$input .= '<option value="'.$option_val.'">'.$option.'</option>'."\n";
				}
				$input .= '</select>'."\n";

			}
			// checkbox
			elseif($field['Type'] == 'CHECKBOX')
			{
				$input = '';
				// options generation
				$options = explode("\n", $field['Value']);

				$x = 0;
				foreach($options as $option)
				{
					$option = trim($option);
					$option_val = $option;
					if($field['I18N'] == 'YES')
						$option = '<i18n>'.$option.'</i18n>';
                   	$input .= ' <label><input type="checkbox" name="'.$field['FieldName'].'[]" id="'.$field['FieldName'].'_'.$x.'" value="'.$option_val.'" '.$field['Attributes'].' />'."\n".$option.'</label>'."\n";
					$x++;
				}
			}
			// radio
			elseif($field['Type'] == 'RADIO')
			{
				$input = '';
				// options generation
				$options = explode("\n", $field['Value']);

				$x = 0;
				foreach($options as $option)
				{
					$option = trim($option);
					$option_val = $option;
					if($field['I18N'] == 'YES')
						$option = '<i18n>'.$option.'</i18n>';
                   	$input .= ' <label><input type="radio" name="'.$field['FieldName'].'" id="'.$field['FieldName'].'_'.$x.'" value="'.$option_val.'" '.$field['Attributes'].' />'."\n".$option.'</label>'."\n";
					$x++;
				}
			}
			// PHP
			elseif($field['Type'] == 'PHP')
			{
				$field['PhpCode'] = trim($field['PhpCode']);
				if(!empty($field['PhpCode']))
					eval($field['PhpCode']);
			}

			// Section and html exception
			if(!in_array($field['Type'], array('SECTION', 'HTML')))
			{
				$form['fields'] .= "\n<div id=\"wrapper_{$field['FieldName']}\" class=\"wrapper\">\n";
				$form['fields'] .= "\n<span class=\"label\">{$field['Label']} $required  :</span>\n";
				$form['fields'] .= "\n{$input}\n";

				if(!empty($field['TextAfter']))
				{
					$form['fields'] .= "\n<span class=\"text_after\">{$field['TextAfter']}</span>\n";
				}

				$form['fields'] .= "\n</div>\n";
			}
			else
			{
				if($field['Type'] == 'SECTION')
				{
					$form['fields'] .= "\n<div id=\"wrapper_Section{$sectionNb}\" class=\"wrapper wrapper_section\">\n";
					$form['fields'] .= "\n{$field['Label']}\n";
					$form['fields'] .= "\n</div>\n";
					$sectionNb++;
				}
				elseif($field['Type'] == 'HTML')
				{
					$form['fields'] .= "\n{$field['HtmlCode']}\n";
				}
			}
		}
		// add captcha
		if($form['Captcha'] == 'YES')
		{
			$str = ($this->language == 'fr') ? "Code sécurité" : "<i18n>Security code</i18n>";

			$form['fields'] .= "\n<div class=\"wrapper form_captcha\">\n";
			$form['fields'] .= "\n<span class=\"label\">$str <span class=\"required\">*</span> :</span>\n";
			$form['fields'] .= "\n{$TPLN_CAPTCHA_FIELD}\n";
			$form['fields'] .= "\n</div>\n";

		}

		// required
		$req_label = ($this->language == 'fr') ? "Champs obligatoires" : "<i18n>Required fields</i18n>";

		$form['fields'] .= "\n<div class=\"wrapper form_required\">\n";
		$form['fields'] .= "\n<span class=\"required\">*</span> $req_label\n";
		$form['fields'] .= "\n</div>\n";


		// add final input
		$cancel_label = "";
		$submit_label = "";
		if($this->language == 'en')
		{
			$cancel_label = "Cancel";
			$submit_label = "Submit";
		}
		elseif($this->language == 'fr')
		{
			$cancel_label = "Annuler";
			$submit_label = "Envoyer";
		}




		$form['fields'] .= "\n<div class=\"wrapper form_bottom\">\n";
		$form['fields'] .= '<input type="reset" class="reset" value="'.$cancel_label.'" />';
		$form['fields'] .= '<input type="submit" class="submit" value="'.$submit_label.'" />';
		$form['fields'] .= "\n</div>\n";

		// dynamic validation
		foreach($formData as $field)
		{
			if($field['Required'] == 'YES')
			{
				if($field['Type'] == 'CHECKBOX' || $field['Type'] == 'SELECT-MULTIPLE')
				{
					$this->notEmpty($field['FieldName'].'[]');
				}
				else
				{
					$this->notEmpty($field['FieldName']);
				}
			}

			if($field['Email'] == 'YES')$this->email($field['FieldName']);
			$field['OtherValidation'] = trim($field['OtherValidation']);
			if(!empty($field['OtherValidation']))eval($field['OtherValidation']);

			// special for file
			if($field['Type'] == 'FILE')
			{
				$this->fileControl($field['FieldName'], false, $field['FileMaxSize'], $field['FileAllowedMimes'], $field['FileAllowedExtensions']);
			}
		}

		// generic error code
		$form['FormCustomError'] = trim($form['FormCustomError']);
		if(!empty($form['FormCustomError']))eval($form['FormCustomError']);

		// caption
		$form['Caption'] = trim($form['Caption']);
		if(!empty($form['Caption']))
		{
			$form['Caption'] = "<div class=\"caption\">{$form['Caption']}</div>\n";
		}

		// information
		$add_info = '';
		$info_text = trim(strip_tags($form['Information']));
		if(!empty($info_text))
		{
			$add_info = '<div class="nuts_form_information">'.$form['Information'].'</div>';
		}


		// create virtual template form
$template = <<<EOF
<form action="#{$form['Name']}" class="nuts_form" name="{$form['Name']}" id="{$form['Name']}" method="post" enctype="multipart/form-data">

	<div id="form_error">
		<bloc::form_error>
		<p>{msg}</p>
		</bloc::form_error>
	</div>

	<div id="layout_form_{$form['Name']}" class="layout_form">

		<div id="wrapper_form_{$form['Name']}" class="wrapper_form">
			{$form['Caption']}

			{$form['fields']}
		</div>

	</div>
</form>

<script type="text/javascript">
$('#tpln_captcha').attr('autocomplete','off');
if($('div#form_error p').length == 0)$('div#form_error').remove();

{$form['JsCode']}
</script>

<bloc::form_valid>
<a id="{$form['Name']}"></a>
<div id="form_valid">
	{$form['FormValidHtmlCode']}
</div>
</bloc::form_valid>

$add_info

EOF;


		$this->createVirtualTemplate($template);

		if($this->formIsValid())
		{
			$this->sanitizePost();

			// automatic file upload
			foreach($formData as $field)
			{
				if($field['Type'] == 'FILE' && isset($_FILES[$field['FieldName']]) && !$_FILES[$field['FieldName']]['error'] && is_uploaded_file($_FILES[$field['FieldName']]['tmp_name']))
				{
					$filename = uniqid('file').'.'.end(explode('.', $_FILES[$field['FieldName']]['name']));
					$target = WEBSITE_PATH.$field['FilePath'].'/'.$filename;
					if(move_uploaded_file($_FILES[$field['FieldName']]['tmp_name'], $target))
					{
						$_POST[$field['FieldName']] = str_replace(WEBSITE_PATH, WEBSITE_URL, $target);
						$_POST[$field['FieldName']] = '<a href="'.$_POST[$field['FieldName']].'">'.$_POST[$field['FieldName']].'</a>';
					}
				}
			}

			// execute custom php code
			if(!empty($field['FormValidPhpCode']))eval($field['FormValidPhpCode']);

			// execute mailer
			$datetime = ($this->language == 'fr') ? date('d/m/Y H:i:s') : date('Y/m/d H:i:s');
			$labels = array();
			foreach($formData as $field)
			{
				$labels[$field['FieldName']] = $field['Label'];
			}

			$body = '<table border="0" cellpadding="3" cellspacing="1" style="background-color:#ccc;">';
			$body .= '<tr>';
			$body .= '<td class="label"><b>Date</b></td>'."\n";
			$body .= '<td>'.$datetime.'</td>'."\n";
			$body .= '<tr>';

            $email_response = "";
			foreach($_POST as $key => $val)
			{
				$lbl = (!isset($labels[$key])) ? $key : $labels[$key];
				$vals = (is_array($val)) ? join('<br>', $val) : nl2br(ucfirst($val));

                // make email clickable
                if(!is_array($val))
                {
                    foreach($formData as $cur_form_field)
                    {
                        if($cur_form_field['FieldName'] == $key && $cur_form_field['Email'] == 'YES')
                        {
                            $vals = $this->clickable(strtolower($val));
                            $email_response = $val;
                            break;
                        }
                    }
                }



				// add after text
				foreach($formData as $field)
				{
					if($key == $field['FieldName'] && !empty($field['TextAfter']))
					{
						$vals .= ' ('.$field['TextAfter'].')';
						break;
					}
				}

				if($key != 'tpln_captcha')
				{
					$body .= "<tr>";
					$body .= '	<td style="background-color:#e5e5e5;"><b>'.$lbl.'</b></td>'."\n";
					$body .= '	<td style="background-color:#ffffff;">'.$vals.'</td>'."\n";
					$body .= "</tr>";
				}
			}

			$body .= "</table>";
			$body .= "<br>";
			$body .= "<br>";
			$body .= "<hr />";
			$body .= "<b>IP visitor :</b> ".$this->getIP()."<br>";

            // browser info
            $browser = $this->getBrowserInfo();
            $body .= "<b>Browser :</b> ".@$browser['name'].' '.@$browser['version']."<br>";
            $body .= "<b>System :</b> ".@$browser['platform']."<br>";
            $body .= "<b>Agent :</b> ".@$browser['userAgent']."<br>";
			$body .= "<b>Powered by Nuts CMS automatic forms</b>";

			include(WEBSITE_PATH.'/plugins/_email/config.inc.php');
			$body_table = $body;

			// execute mailer
			if($form['FormValidMailer'] == 'YES')
			{
				$body = str_replace('[BODY]', $body, $HTML_TEMPLATE);

				// send email
				$this->mailCharset('UTF-8');

				if(empty($form['FormValidMailerFrom']))$form['FormValidMailerFrom'] = NUTS_EMAIL_NO_REPLY;
				if(empty($form['FormValidMailerTo']))$form['FormValidMailerTo'] = NUTS_ADMIN_EMAIL;

				$this->mailFrom($form['FormValidMailerFrom']);
                if(!empty($email_response))
                {
                    $this->mailFrom($email_response);
                }

				$this->mailSubject($form['FormValidMailerSubject']);
				$this->mailBody($body, 'HTML');

				$tos = explode(';', $form['FormValidMailerTo']);
				foreach($tos as $to)
				{
					$to = trim($to);
					if(!empty($to))
					{
						$this->mailTo($to);
						$this->mailSend();
					}
				}
			}

			// save record
			if($form['FormStockData'] == "YES")
			{
				$csv = "";
				foreach($_POST as $key => $val)
				{
					if($key != 'tpln_captcha')
					{
						$vals = (is_array($val)) ? join(' ', $val) : $val;
						$csv .= str_replace(array(';', "\r", "\n"), array(",", ' ', ' '), $vals).";";
					}
				}

				$this->dbInsert('NutsFormData', array(
														'NutsFormID' => $form['ID'],
														'Date' => 'NOW()',
														'Data' => $body_table,
														'DataSerialize' => $csv));
			}
		}

		$str = $this->output();


		return $str;
	}


	/**
	 * Create the survey
	 * @param string $surveyID
	 * @return string
	 */
	private function getSurvey($surveyID)
	{
		$sql = "SELECT
						NutsSurvey.*,
						NutsSurveyOption.ID AS OptionID,
						NutsSurveyOption.Title AS OptionTitle,
						NutsSurveyOption.I18N AS  OptionI18N
				FROM
						NutsSurvey,
						NutsSurveyOption
				WHERE
						NutsSurvey.ID = NutsSurveyOption.NutsSurveyID AND
						NutsSurvey.Deleted = 'NO' AND
						NutsSurveyOption.Deleted = 'NO' AND
						NutsSurvey.ID = '%s'
				ORDER BY
						NutsSurveyOption.Position";

		$this->dbSelect($sql, array($surveyID));
        if($this->dbNumRows() == 0)return "Error: `survey #$surveyID` not found";

		// construct survey
		$surveyData = $this->dbGetData();
		$survey = $surveyData[0];

		$title = $survey['Title'];
		if($survey['I18N'] == 'YES')
			$title = "<i18n>$title</i18n>";

		$str = "<div class=\"nuts_survey\" id=\"nuts_survey_$surveyID\">\n";
		$str .= "	<p  class=\"nuts_survey_title\">$title</p>\n";

		$str .= "<div class=\"nuts_survey_options\">";

		// options generation
		$i = 0;
		foreach($surveyData as $option)
		{
			$label = $option['OptionTitle'];
			if($survey['I18N'] == 'YES')
				$label = "<i18n>{$survey['OptionTitle']}</i18n>";
			$str .= "	<label><input type=\"radio\" name=\"Option\" id=\"Option{$i}\" value=\"{$option['OptionID']}\" onclick=\"$('#nuts_survey_$surveyID #NutsSurveyOkButton').attr('disabled', false);\" /> $label</label><br />\n";
			$i++;
		}

		$result_lng = ($this->language == 'fr') ? 'Résultats' : 'Results';
		$result = '';
		if($survey['ViewResult'] == 'YES')
		{
			$result = "<a id=\"NutsSurveyA\" href=\"javascript:;\"><i18n>$result_lng</i18n></a>";
		}

		$str .= "	<div class=\"nuts_survey_bottom\">$result <input type=\"button\" id=\"NutsSurveyOkButton\" value=\"Ok\" disabled></div>\n";
		$str .= "	</div>\n";

		$str .= "</div>\n";


		$str .= <<<EOF

		<script type="text/javascript">
		$("#nuts_survey_$surveyID #NutsSurveyOkButton, #nuts_survey_$surveyID #NutsSurveyA").click(function(){
			padding_val = $('#nuts_survey_$surveyID .nuts_survey_options').css('padding');

			if($(this).attr('id') == 'NutsSurveyOkButton')
			{
				$(this).val('...');
				$(this).attr('disabled', true);
			}

			uri = '/plugins/_survey/vote.php';
			from = $(this).attr('id');

			if(from == 'NutsSurveyOkButton')
			{
				optionID = $("#nuts_survey_$surveyID input:checked").val();
			}
			else
			{
				optionID = -1;
			}

			$.post(uri, {ID: $surveyID, OptionID: optionID}, function(data){

				if(from == 'NutsSurveyOkButton')
					$('#nuts_survey_$surveyID #NutsSurveyOkButton').attr('disabled', true);

				resp = data.split('@@@');
				if(resp[0] != 'ok')
				{
					alert(data);
					$('#nuts_survey_$surveyID #NutsSurveyOkButton').val('Ok');
					$('#nuts_survey_$surveyID #NutsSurveyOkButton').attr('disabled', false);
				}
				else
				{
					$('#nuts_survey_$surveyID .nuts_survey_options').html(resp[1]);

				}
			});


		});
		</script>


EOF;

		return $str;


	}

	/**
	 * Sanityze input post data
	 */
	public function sanitizePost()
	{
		foreach($_POST as $key => $val)
		{
			if(!is_array($val))
			{
				$_POST[$key] = $this->sanitize($val);
			}
			else
			{
				for($i=0; $i < count($val); $i++)
				{
					$val[$i] = $this->sanitize($val[$i]);
				}
				$_POST[$key] = $val;
			}
		}
	}

	/**
	 * Sanityze value
	 *
	 * @param string $val
	 * @return string
	 */
	public function sanitize($val)
	{
		$val = str_replace('{XNUTS', '{ @NUTS', $val);
		$val = str_replace('{#', '{ #', $val);

		return $val;
	}

	/**
	 * Format output before
	 * @param string $out
	 * @return string
	 */
	private function formatOutput($out)
	{
		$out = str_replace(array('    ', '%20%20%20%20'), "\t", $out);
		return $out;
	}

	/**
	 * Add a pattern dynamically
	 * @param string $pattern
	 * @param string $replacement
	 */
	public function addPattern($pattern, $replacement)
	{
		$this->patterns[$pattern] = $replacement;
	}


	private $header_files_added = array();
	private $header_after_files_added = array();

	/**
	 *
	 * @param string $type
     *
	 */

    /**
     * Add dynamically a file after head meta
     *
     * @param $type must be css, js, custom
     * @param $url
     * @param bool $after_meta_head_start (true by default)
     */
    public function addHeaderFile($type, $url, $after_meta_head_start=true)
	{
        if($after_meta_head_start)
		    $this->header_files_added[] = array('type' => strtolower($type), 'url' => $url);
        else
            $this->header_after_files_added[] = array('type' => strtolower($type), 'url' => $url);
	}


	private function addNutsToolbar($out)
	{
		// connected and backoffice access ?
		if(!nutsUserIsLogon())
			return $out;

		// users access page and BackofficeAccess
		$sql = "SELECT
						ID
				FROM
						NutsGroup
				WHERE
						ID = {$_SESSION['NutsGroupID']} AND
						BackofficeAccess  = 'YES' AND
						FrontofficeAccess  = 'YES' AND

						(SELECT FrontOfficeToolbar FROM NutsUser WHERE ID = {$_SESSION['NutsUserID']}) = 'YES' AND

						ID IN (SELECT NutsGroupID FROM NutsMenuRight WHERE NutsMenuID IN
																						(SELECT ID FROM NutsMenu WHERE Deleted = 'NO' AND Name = '_page-manager')
								AND Name = 'exec')";
		$this->doQuery($sql);
		if($this->dbNumRows() == 0)
			return $out;

		// edit page
		$edit_lbl = ($_SESSION['Language'] == 'fr') ? 'Editer' : 'Edit';
		$edit_option = <<<EOF
&nbsp;&nbsp;|&nbsp;&nbsp;
			<img  alt="" src="/library/js/jquery-simpletree/images/page_edit.png" style="width:16px; vertical-align:middle;" /> <a id="nuts_page_link" style="color:black!important;text-decoration:none!important;" href="/nuts/index.php?mod=_page-manager&amp;do=exec&amp;pID={$this->pageID}&amp;popup=1&amp;parent_refresh=0&amp;from=iframe">$edit_lbl (#{$this->pageID})</a>
EOF;

		// page options
		$page_option_lbl = 'Options';
		$page_option = <<<EOF
&nbsp;&nbsp;|&nbsp;&nbsp;
			<img  alt="" src="/nuts/img/widget.png" style="vertical-align:middle;" /> <a id="nuts_page_link4" style="color:black!important;text-decoration:none!important;" href="/nuts/index.php?mod=_page-manager&amp;do=exec&amp;pID={$this->pageID}&amp;popup=1&amp;parent_refresh=0&amp;from=iframe&amp;tab_selected=options">{$page_option_lbl}</a>
EOF;

		// new page same level
		$add_lbl = ($_SESSION['Language'] == 'fr') ? 'Ajouter page' : 'Add page';
		$add_option = <<<EOF
&nbsp;&nbsp;|&nbsp;&nbsp;
			<img  alt="" src="/library/js/jquery-simpletree/images/page_add.png" style="width:16px; vertical-align:middle;" /> <a id="nuts_page_link2" style="color:black!important;text-decoration:none!important;" href="/nuts/index.php?mod=_page-manager&amp;do=exec&amp;pID={$this->pageID}&amp;popup=1&amp;parent_refresh=0&amp;from=iframe&amp;from_action=add_page">$add_lbl</a>
EOF;

		// new sub page
		$add_sub_lbl = ($_SESSION['Language'] == 'fr') ? 'Ajouter sous-page' : 'Add sub page';
		$add_sub_option = <<<EOF
&nbsp;&nbsp;|&nbsp;&nbsp;
			<img  alt="" src="/library/js/jquery-simpletree/images/page_add.png" style="width:16px; vertical-align:middle;" /> <a id="nuts_page_link3" style="color:black!important;text-decoration:none!important;" href="/nuts/index.php?mod=_page-manager&amp;do=exec&amp;pID={$this->pageID}&amp;popup=1&amp;parent_refresh=0&amp;from=iframe&amp;from_action=add_sub_page">$add_sub_lbl</a>
EOF;

		// show elements
		$se_sub_lbl = ($_SESSION['Language'] == 'fr') ? 'Afficher éléments' : 'Show elements';
		$se_sub_option = <<<EOF

			<label style="-webkit-user-select:none; -moz-user-select:none;"><input type="checkbox" id="nuts_elements" onclick="nutsToggleElements(this.checked);"> $se_sub_lbl</label>
EOF;

		// $out = preg_replace('/<!-- nuts (.*) -->/sU', '<!-- nuts $1 --><span class="nuts_elements" style="'.$nuts_elements_styles.'">$1</div>', $out);
		preg_match_all('/<!-- nuts (.*) -->/sU', $out, $matches);
		if(count($matches) >= 2)
		{
			$nuts_elements_styles = 'position:absolute; z-index:1100; display:none; font-size:8px!important; color:black!important; background-color:#FFDFEA!important; border:1px solid violet!important; box-shadow:0px 1px 10px #999; padding-left:5px!important; padding-right:5px!important;';
			foreach($matches[1] as $elem)
			{
				$sub = "<!-- nuts $elem -->";
				$rep = "$sub<span class=\"nuts_elements\" style=\"$nuts_elements_styles\">$elem</span>";
				$out = str_replace($sub, $rep, $out);
			}
		}

		$refresh_lbl = ($_SESSION['Language'] == 'fr') ? 'Rafraîchir' : 'Refresh';
        $open_close_lbl = ($_SESSION['Language'] == 'fr') ? 'Ouvrir / Fermer' : 'Open / Close';


		// no editable page
		if($this->pageID == 0)
		{
			$edit_option = '';
			$add_option = '';
			$add_sub_option = '';
			$page_option = '';
		}

		$toolbar = <<<EOF

		<div id="nuts_front_toolbar" style="width:99%; position:fixed; z-index:570; left: 0px; top:-30px; padding:5px; text-align:center; color:black; background:#e5e5e5; margin-top:-2px; border-bottom:1px solid #ccc;">
			<img alt="" src="/nuts/img/icon-user.gif" style="width:16px; vertical-align:middle;" /> <b>{$_SESSION['Login']} (#{$_SESSION['ID']})</b> &nbsp;&nbsp;&nbsp;|  &nbsp;&nbsp;&nbsp;
			<img alt="" src="/nuts/img/logon_password.png" style="width:16px; vertical-align:middle;" /> <a style="color:black!important;text-decoration:none!important;" href="/nuts/" target="_blank">Back-office</a>
			$edit_option
			$page_option
			$add_option
			$add_sub_option
			$se_sub_option

			&nbsp;&nbsp;|&nbsp;&nbsp;<img alt="" src="/nuts/img/icon-refresh.png" style="width:16px; vertical-align:middle;" /> <a style="color:black!important;text-decoration:none!important;" href="javascript:history.go(0);">$refresh_lbl</a>

		</div>
		<div id="nuts_front_toolbar_button" style="user-select:none; webkit-user-select:none; border:1px solid #ccc; border-top:0; width:auto; position:fixed; z-index:570; right:0px; top:2px; padding:5px; text-align:center; color:black; background-color:#e5e5e5; margin-right:20px; margin-top:-4px;"><a style="color:#0000; text-transform:uppercase; user-select:none; webkit-user-select:none; font-size:10px;" href="javascript:nutsFrontToolbarOpenClose();">$open_close_lbl</a></div>

		<script type="text/javascript">
		$('#nuts_elements').attr('checked', false);
		$('#nuts_page_link, #nuts_page_link2, #nuts_page_link3, #nuts_page_link4').fancybox({
											'width'				: '97%',
											'height'			: '91%',
											'autoScale'     	: true,
											'type'				: 'iframe',
											'showCloseButton'	: true,
											'hideOnContentClick': false,
											'enableEscapeButton': true,
											'hideOnOverlayClick': false,
											'centerOnScroll'    : true,
											'changeFade'		: 0,
											'margin'			: 5,
											'margin-top'		: 3
										});

		function nutsToggleElements(show)
		{
			$('.nuts_elements').toggle();
		}

		function nutsFrontToolbarOpenClose()
		{
		    posTop = $('#nuts_front_toolbar_button').css('top');
		    posTop = parseInt(posTop);

		    if(posTop == 2)
		    {
                $('#nuts_front_toolbar, #nuts_front_toolbar_button').animate({top: '+=32'}, 400);
		    }
		    else
		    {
                $('#nuts_front_toolbar, #nuts_front_toolbar_button').animate({top: '-=32'}, 400);
		    }

		}
		</script>
EOF;
		$toolbar = trim($toolbar);
		$out = str_replace('</body>', $toolbar.'</body>', $out);

		return $out;
	}

	/**
	 * Add Maintenance toolbar
	 * @param string $out
	 * @return string $out
	 */
	private function addNutsMaintenanceToolbar($out)
	{
		if(!WEBSITE_MAINTENANCE)
			return $out;

		$curIP = $this->getIP();
		$lng = ($this->language == 'fr') ? "Site en maintenance, votre ip `$curIP` est autorisée" : "Website is in maintenance, your ip `$curIP` is allowed";


		$toolbar = <<<EOF

		<div id="nuts_maintenance_toolbar" style="font-weight:bold; background-color:red; border-top:2px solid pink; color:white; text-align:center; padding:5px; width:99%; position: fixed; bottom:0; ">
			$lng
		</div>

EOF;

		$out = str_replace('</body>', $toolbar.'</body>', $out);
		return $out;
	}


	/**
	 * Return parent page ID or false
	 * @param int $pageID default current page
	 * @return int $parentPageID
	 */
	public function getParentPageID($pageID='')
	{
		$pageID = (int)$pageID;
		if(!$pageID)$pageID = $this->pageID;

		$sql = "SELECT NutsPageID FROM NutsPage WHERE ID = $pageID AND Deleted = 'NO' AND State = 'PUBLISHED'";
		$this->doQuery($sql);

		$parentPageID = 0;

		if($this->dbNumRows())
			$parentPageID = (int)$this->dbGetOne();

		return $parentPageID;
	}


	/**
	 * Return parent page Url or empty string
	 * @param int $pageID default current page
	 * @return string $parentPageUrl
	 */
	public function getParentPageUrl($pageID='')
	{
		$pageID = (int)$pageID;
		if(!$pageID)$pageID = $this->pageID;

		$parentPageUrl = "";

		if(($parentPageID = $this->getParentPageID()))
		{
			$parentPageUrl = $this->getUrl($parentPageID);
		}

		return $parentPageUrl;
	}

	/**
	 * Return children page ID or false
	 * @param int $pageID default current page
	 * @return int $childrenPageID
	 */
	public function getChildrenPageID($pageID='')
	{
		$pageID = (int)$pageID;
		if(!$pageID)$pageID = $this->pageID;

		$sql = "SELECT ID FROM NutsPage WHERE NutsPageID = $pageID AND Deleted = 'NO' AND State = 'PUBLISHED' ORDER BY Position LIMIT 1";
		$this->doQuery($sql);

		$childrenPageID = 0;

		if($this->dbNumRows())
			$childrenPageID = (int)$this->dbGetOne();

		return $childrenPageID;
	}

	/**
	 * Return children page Url or empty string
	 * @param int $pageID default current page
	 * @return string $parentPageUrl
	 */
	public function getChildrenPageUrl($pageID='')
	{
		$pageID = (int)$pageID;
		if(!$pageID)$pageID = $this->pageID;

		$childrenPageUrl = "";

		if(($childrenPageID = $this->getChildrenPageID()))
		{
			$childrenPageUrl = $this->getUrl($childrenPageID);
		}

		return $childrenPageUrl;
	}


	/**
	 * Return page parameter
	 * @param int $index
	 */
	public function getPageParameter($index){
		return @$this->args[$index];
	}

	/**
	 * Return page parameter count
 	 * @param int $index
	 */
	public function getPageParameterCount(){
		return @count($this->args);
	}


	/**
	 * Return current plugin parameter
	 * @param $index
	 */
	public function getPluginParameter($index){
		return @$this->plugin_embed[$this->plugin_real_name][$index];
	}

}




?>