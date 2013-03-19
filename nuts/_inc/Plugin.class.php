<?php
/**
 * Use for backoffice plugin
 * @package Nuts
 */
class Plugin
{
	/**
	 * Plugin system name
	 * @var string
	 */
	public $name;

	/**
	 * Plugin description
	 * @var string
	 */
	public $description;

	/**
	 * Plugin name
	 * @var string
	 */
	public $real_name;

	/**
	 * Curent action
	 * @var string
	 */
	public $action;

	/**
	 * Plugin Category (index starts at 1)
	 * @var int
	 */
	public $Category;

	/**
	 * Get plugin record
	 * @var array
	 */
	public $record;

	/**
	 * get plugin configuration from info.yaml
	 * @var array
	 */
	public $configuration;

	/**
	 * Render html
	 * @var $render string
	 */
	public $render;

	/**
	 * Get generated SQL from list
	 * @var $listSQL string
	 */
    public $listSQL;

	/**
	 * @var TPLN
	 */
	private $nuts;


	/**
	 * Form is percent render (ex: Newsletter)
	 * @var boolean
	 */
	public $formPercentRender = false;

	/**
	 * Form is percent init end value
	 * @var int
	 */
	public $formPercentRenderEndValue = 0;


    /**
     * log plugin action in database
     *
     * @param string $resume action
     * @param int $recordID
     */
	public function trace($resume='post', $recordID=0)
	{
		nutsTrace($this->name, $this->action, $resume, $recordID);
	}


    /**
     * constructor
     */
	public function __construct()
	{
		$this->nuts = $GLOBALS['nuts'];

		$this->name = $_GET['mod'];
		$this->real_name = $GLOBALS['lang_msg'][0];
		$this->action = $_GET['do'];

		// get all info about menu
		$this->nuts->dbSelect("SELECT
											*
									FROM
											NutsMenu,
											NutsMenuRight
									WHERE
											NutsMenu.ID = NutsMenuRight.NutsMenuID AND
											NutsMenuRight.NutsGroupID = %d AND
											NutsMenu.Name = '%s' AND
											NutsMenuRight.Name = '%s'",
									array(
											$_SESSION['NutsGroupID'],
											$_GET['mod'],
											$_GET['do']
										));
		$this->record = $this->nuts->dbFetch();

		$this->Category = $this->record['Category'];
		$this->configuration = Spyc::YAMLLoad(WEBSITE_PATH.'/plugins/'.$this->name.'/info.yml');

		// dynamic description language
		$this->description = (isset($this->configuration["info_".$_SESSION['Language']])) ? $this->configuration["info_".$_SESSION['Language']] : $this->configuration["info"];
		$this->description = ucfirst($this->description);
	}
    /**
     *
     * Plugin validator (user rights or plugin not exists)
     *
     * @return bool
     */
	static function validator()
	{
		if(!isset($_GET['mod']) || $_GET['mod'] == '_home')
		{
			$_GET['mod'] = '_home';
			$_GET['do'] = 'exec';
			return true;
		}

		$_GET['mod'] = $GLOBALS['nuts']->xssProtect($_GET['mod']);

		// verify if plugin exists in user menu
		$GLOBALS['nuts']->dbSelect("SELECT
											*
									FROM
											NutsMenu,
											NutsMenuRight
									WHERE
											NutsMenu.ID = NutsMenuRight.NutsMenuID AND
											NutsMenuRight.NutsGroupID = %d AND
											NutsMenu.Name = '%s'",
									array(
											$_SESSION['NutsGroupID'],
											$_GET['mod']
										));

		$res = $GLOBALS['nuts']->getOne();
		if($res == 0)
		{
			$msg = "plugin `{$_GET['mod']}` not found";
			Plugin::setError($msg);
			nutsTrace('_system', 'none', $msg);

			return false;
		}

		// verify if plugin exists in dir
		if(!is_dir(WEBSITE_PATH.'/plugins/'.$_GET['mod']))
		{
			$msg = "plugin `{$_GET['mod']}` not installed correctly";
			Plugin::setError($msg);
			nutsTrace('_system', 'none', $msg);

			return false;
		}

		return true;
	}

    /**
     * Verify plugin (user right + right exists)
     *
     * @return bool
     */
	static function actValidator()
	{
		if($_GET['mod'] == '_home') return true;

		if(!isset($_GET['do']) || empty($_GET['do']))
		{
			// get first action by default
			$msg = "plugin `{$_GET['mod']}` no action defined";
			Plugin::setError($msg);
			nutsTrace('_system', 'none', $msg);

			return false;
		}

		$_GET['do'] = $GLOBALS['nuts']->xssProtect($_GET['do']);

		// verify if action exits for this mod
		$GLOBALS['nuts']->dbSelect("SELECT
											COUNT(*)
									FROM
											NutsMenu,
											NutsMenuRight
									WHERE
											NutsMenu.ID = NutsMenuRight.NutsMenuID AND
											NutsMenuRight.NutsGroupID = %d AND
											NutsMenu.Name = '%s' AND
											NutsMenuRight.Name = '%s'",
									array(
											$_SESSION['NutsGroupID'],
											$_GET['mod'],
											$_GET['do']
										)
								   );

		$res = $GLOBALS['nuts']->getOne();
		if($res == 0)
		{
			$msg = "plugin `{$_GET['mod']}` action not allowed";
			Plugin::setError($msg);
			nutsTrace('_system', 'none', $msg);

			return false;
		}

		// trace plugin action
        if(!$_POST && !isset($_GET['_action']))
        {
            if(in_array($_GET['mod'], array('_logs', '_edm-logs')) && $_GET['do'] == 'list')
            {

            }
            else
            {
                nutsTrace($_GET['mod'], $_GET['do']);
            }
        }

		return true;
	}

    /**
     * Define a constant error
     *
     * @param string $msg
     */
	private function setError($msg)
	{
		// save error message in database

		// create a CONST with error
		define('NUTS_ERROR_TMP', $msg);
	}

    /**
     * Display error template
     */
	public function errorRender()
	{
		$this->nuts->open(WEBSITE_PATH.'/nuts/_templates/error.html');
		$this->nuts->parse("error_msg", NUTS_ERROR_TMP);
		$this->render = $this->nuts->output();
	}

    private $dbtable;
	private $sql_added;
	private $sql_where_added;
	private $sql_after_where_added;
	/**
     * Obsolete, please choose listSetDbTable instead
     */
    public function setListDbTable($dbtable, $sql_added='', $sql_where_added='', $sql_after_where_added='')
	{
		$this->dbtable = $dbtable;
		$this->sql_added = $sql_added;
		$this->sql_where_added = $sql_where_added;
		$this->sql_after_where_added = $sql_after_where_added;
	}

	/**
     * Choose Table in list mode
     *
     * @param string $dbtable
     * @param string $sql_added
	 * @param string $sql_where_added
	 * @param string $sql_after_where_added like ORDER BY
     */
	public function listSetDbTable($dbtable, $sql_added='', $sql_where_added='', $sql_after_where_added='')
	{
        $sql_after_where_added = str_replace('ORDER BY ', "ORDER BY\n", $sql_after_where_added);
        $sql_after_where_added = " ".$sql_after_where_added;
		$this->setListDbTable($dbtable, $sql_added, $sql_where_added, $sql_after_where_added);
	}

	private $list_buttons = array();
    /**
     * Add a button in list mode
     *
     * @param string $name
     * @param string $label if empty $label = $name
     * @param string $onclick html event code
     */
	public function listAddButton($name, $label, $onclick)
	{
	    if(empty($label))$label = $name;
	    $this->list_buttons[] = array('name' => $name, 'label' => $label, 'onclick' => $onclick);
	}


	private $cols = array();
	private $colsLabel = array();
	private $colsStyle = array();
	private $colsOrderBy = array();
	private $colsNoClick = array();
	private $colsImg = array();
	private $colsClass = array();
    /**
     * Add column in list view
     *
     * @param string $col db field name
     * @param string $colLabel label displayed if empty $label = $col
     * @param string $colStyle html style to add
     * @param bool $colOrderBy allow order by
     * @param string $colImg image src to displayed instead of text
	 * @param string $colClass image src to displayed instead of text
     */
	public function listAddCol($col, $colLabel, $colStyle = '', $colOrderBy = false, $colImg = '', $colClass = '')
	{
		$this->cols[] = $col;

		if(empty($colLabel))$colLabel = $col;

		$this->colsLabel[] = $colLabel;
		$this->colsStyle[] = $colStyle;
		$this->colsOrderBy[] = $colOrderBy;
		$this->colsImg[] = $colImg;
		$this->colsClass[] = $colClass;
	}
    /**
     *
     * Add a column image in list view
     *
     * @param string $col db field name
     * @param string $colLabel labe displayed if empty $label = $col
     * @param string $colStyle html style to add
     * @param bool $colOrderBy allow order by
     * @param string $colImgName name or src or image
     * @param string $colImgHref url to execute when user clicks on image
     */
	public function listAddColImg($col, $colLabel='', $colStyle = '', $colOrderBy = false, $colImgName='', $colImgHref='')
	{
		if(empty($colLabel))$colLabel = $col;

		if(empty($colStyle))
		{
			$colStyle = 'center; width:30px';
		}

		if(empty($colImgName))
		{
			//if(!eregi('^http', $col))
			if(!preg_match('/^http/i', $col))
				$img = '<img src="img/{ '.$col.'}.gif" alt="'.$colLabel.': {'.$col.'}" align="absmiddle" />';
			else
				$img = '<img src="{ '.$col.'}" align="absmiddle" />';
		}
		else
		{
			//if(!eregi('^http', $colImgName))
			if(!preg_match('/^http/i', $colImgName))
				$colImgName = 'img/'.$colImgName;
			elseif($colImgName[strlen($colImgName)-1] == '/')
			{
				$colImgName .= '{'.$col.'}';
			}

			$img = '<img src="'.$colImgName.'" alt="'.$colLabel.': {'.$col.'}" align="absmiddle" />';
		}

		if(empty($colImgHref))
		{
			$str = '';
			//if(!eregi('^http', $col) && !ereg('Image$', $col))
			if(!preg_match('/^http/i', $col) && !preg_match('/Image$/', $col))
				$str = '<a class="tt" title="'.$colLabel.': '.'{'.$col.'}">';
		}
		else
		{
			//if(!eregi('^http', $col))
			if(!preg_match('/^http/i', $col))
				$str = '<a href="'.$colImgHref.'" class="tt" title="{'.$col.'}">';
		}

		// exception
		//if(!eregi('^http', $col) && !ereg('Image$', $col))
		if(!preg_match('/^http/i', $col) && !preg_match('/Image$/', $col))
		{
			$str .= $img;
			$str .= '</a>';
			$img = $str;
		}


		$this->listAddCol($col, $colLabel, $colStyle, $colOrderBy, $img);
	}

	/**
	 * @var bool open search engine on load
	 */
	public  $listSearchOpenOnload = false;

	private $list_search = array();
    /**
     *
     * Add a field to search engine - (please use direct object method instead listSearchAddField*)
     *
     * @param string $name db field
     * @param string $label label to display if empty $label = $name
     * @param string $type:<br>
	 *		&bull; text<br>
	 *		&bull; select<br>
	 *		&bull; boolean<br>
	 *		&bull; booleanX<br>
	 *		&bull; select-sql<br>
	 *		&bull; date<br>
	 *		&bull; datetime<br>
	 *		&bull; ajax_autocomplete
	 *
     * @param array $options optionnal:<br>
	 *		<br>
	 *		<b>&bull; class:</b> upper, lower, ucfirst
     *		<br>
	 *		<b>&bull; operator:</b> default operator
	 *		<br>
	 *		<b>&bull; help:</b> help message
	 *		<br>
	 *		<b>&bull; select-sql:</b> generate options from database (option: field, table, where, order_by)
	 *		<br><br>
	 *		<b>&bull; ajax_autocomplete:</b> generate auto-completion box, option :
	 *		<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_mode:</b> begins (default) or countains<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_column:</b> column to check<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_columnID:</b> column ID for autocompletion<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_table:</b> set table name<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_get:</b> get parameter name<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_sql_where:</b> add sql where clause<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_custom_sql:</b> set complete sql query, place [q] keyword
     */
	public function listSearchAddField($name, $label='', $type='text', $options=array())
	{
		if(empty($label))$label = $name;

		// special for ajax_autocomplete
		if($type == 'ajax_autocomplete')
		{
			$type = 'text';
			$options['class'] = (!isset($options['class'])) ? 'ajax_autocomplete': ' ajax_autocomplete';

			if(!isset($options['ac_columnID']))$options['ac_columnID'] = '';
			if(!isset($options['ac_column']))$options['ac_column'] = $name;
			if(!isset($options['ac_get']))$options['ac_get'] = $name;
			if(!isset($options['ac_table']))$options['ac_table'] = $this->dbtable;
			$options['ac_sql_where'] = (!isset($options['ac_sql_where'])) ? '' : ' AND '.$options['ac_sql_where'];
			if(!isset($options['ac_custom_sql']))$options['ac_custom_sql'] = '';
			if(!isset($options['ac_mode']))$options['ac_mode'] = 'begins';


            // default operator
            if(!isset($options['operator']))
            {
                $options['operator'] = ($options['ac_mode'] == 'begins') ? '^=' : '~=';

                // hack for ID column concatenation
                if(!empty($options['ac_columnID']) || !empty($options['ac_custom_sql']))
                    $options['operator'] = '=';
            }


			// direct render for ajax
			if(isset($_GET['ajax_ac']) && $_GET['ajax_ac_col'] == $options['ac_get'] && @strlen($_GET['q']) >= 2)
			{
				$q = addslashes($_GET['q']);
				$begin_percent = ($options['ac_mode'] == 'begins') ? '' : '%';

				if(empty($options['ac_custom_sql']))
				{
					if(empty($options['ac_columnID']))
					{
						$sql = "SELECT
										DISTINCT {$options['ac_column']} AS val
								FROM
										{$options['ac_table']}
								WHERE
										Deleted = 'NO'
										{$options['ac_sql_where']} AND
										{$options['ac_column']} LIKE '{$begin_percent}$q%'
								ORDER BY
										{$options['ac_column']}
								LIMIT 20";
					}
					else
					{
						$sql = "SELECT
										CONCAT({$options['ac_column']},' (',{$options['ac_columnID']},')') AS val
								FROM
										{$options['ac_table']}
								WHERE
										Deleted = 'NO'
										{$options['ac_sql_where']} AND
										{$options['ac_column']} LIKE '{$begin_percent}$q%'
								ORDER BY
										{$options['ac_column']}
								LIMIT 20";
					}
				}
				else
				{
					$sql = str_replace('[q]', $q, $options['ac_custom_sql']);
				}

				$this->nuts->doQuery($sql);
				$res = array();
				while($row = $this->nuts->dbFetch())
					$res[] = $row['val'];

				die(join("\n", $res));
			}
		}

		$this->list_search[] = array(
									'name' => $name,
									'label' => $label,
									'type' => $type,
									'options' => $options
								);
	}


	/**
	 * Add field type text in search engine
	 *
	 * @param string $name db column
	 * @param string $label label to display if empty $label = $name
	 * @param string $class css class to add (special: upper, lower, ucfirst)
	 * @param string $help help message
	 * @param string $operator operator selected (`=`, '!=', '>', '>=', '<', '<=', '^=', '!^=', '~=', '!~=')
	 */
	public function listSearchAddFieldText($name, $label='', $class='', $help='', $operator='')
	{
		$options = array();
		if(!empty($class))$options['class'] = $class;
		if(!empty($help))$options['help'] = $help;
		if(!empty($operator))$options['operator'] = $operator;

		$this->listSearchAddField($name, $label, 'text', $options);
	}


	/**
	 * Add field type select in search engine
	 *
	 * @param string $name db column
	 * @param string $label label to display if empty $label = $name
	 * @param array $opts array with values (if you want to custom value use key label and value in your array)
	 * @param string $help help message
     * @param string $operator operator selected (`=`, '!=', '>', '>=', '<', '<=', '^=', '!^=', '~=', '!~=')
	 */
	public function listSearchAddFieldSelect($name, $label='', $opts=array(), $help='', $operator='')
	{
		$options = array();
		$options['options'] = $opts;
		if(!empty($help))$options['help'] = $help;
        if(!empty($operator))$options['operator'] = $operator;

		$this->listSearchAddField($name, $label, 'select', $options);
	}


	/**
	 * Add field type select-sql in search engine
	 *
	 * @param string $name db column
	 * @param string $label label to display if empty $label = $name
	 * @param string $field force field name if empty use $name
	 * @param string $table force table name if empty use $table
	 * @param string $where add where clause
	 * @param string $order_by force order_by clause
	 * @param string $help help message
     * @param string $operator operator selected (`=`, '!=', '>', '>=', '<', '<=', '^=', '!^=', '~=', '!~=')
	 */
	public function listSearchAddFieldSelectSql($name, $label='', $field='', $table='', $where='', $order_by='', $help='', $operator='')
	{
		$options = array();
		if(!empty($field))$options['field'] = $field;
		if(!empty($table))$options['table'] = $table;
		if(!empty($where))$options['where'] = $where;
		if(!empty($order_by))$options['order_by'] = $order_by;
		if(!empty($help))$options['help'] = $help;
        if(!empty($operator))$options['operator'] = $operator;

		$this->listSearchAddField($name, $label, 'select-sql', $options);
	}


	/**
	 * Add field type text with ajax auto-completion in search engine
	 *
	 * @param string $name db column
	 * @param string $label label to display if empty $label = $name
	 * @param string $ac_mode auto completion mode begins (default) or countains
	 * @param string $ac_column column to check
	 * @param int $ac_columnID column ID for autocompletion
	 * @param string $ac_table set table name
	 * @param string $ac_get get parameter name
	 * @param string $ac_sql_where add sql where clause
	 * @param string $ac_custom_sql set complete sql query, place [q] keyword
	 * @param string $help help message
	 */
	public function listSearchAddFieldTextAjaxAutoComplete($name, $label='', $ac_mode='', $ac_column='', $ac_columnID='', $ac_table='', $ac_get='', $ac_sql_where='', $ac_custom_sql='', $help='')
	{
		$options = array();
		if(!empty($ac_mode))$options['ac_mode'] = $ac_mode;
		if(!empty($ac_column))$options['ac_column'] = $ac_column;
		if(!empty($ac_columnID))$options['ac_columnID'] = $ac_columnID;
		if(!empty($ac_table))$options['ac_table'] = $ac_table;
		if(!empty($ac_get))$options['ac_get'] = $ac_get;
		if(!empty($ac_sql_where))$options['ac_sql_where'] = $ac_sql_where;
		if(!empty($ac_custom_sql))$options['ac_custom_sql'] = $ac_custom_sql;
		if(!empty($help))$options['help'] = $help;
		$this->listSearchAddField($name, $label, 'ajax_autocomplete', $options);
	}


	/**
	 * Add field type boolean in search engine
	 *
	 * @param string $name db column
	 * @param string $label label to display if empty $label = $name
	 * @param string $help help message
	 */
	public function listSearchAddFieldBoolean($name, $label='', $help='')
	{
		$options = array();
		if(!empty($help))$options['help'] = $help;
		$this->listSearchAddField($name, $label, 'boolean', $options);
	}


	/**
	 * Add field type boolean inversed in search engine
	 *
	 * @param string $name db column
	 * @param string $label label to display if empty $label = $name
	 * @param string $help help message
	 */
	public function listSearchAddFieldBooleanX($name, $label='', $help='')
	{
		$options = array();
		if(!empty($help))$options['help'] = $help;
		$this->listSearchAddField($name, $label, 'booleanX', $options);
	}



	/**
	 * Add field type date in search engine
	 *
	 * @param string $name db column
	 * @param string $label label to display if empty $label = $name
	 * @param string $alias sql alias name
	 * @param string $help help message
     * @param string $operator operator selected (`=`, '!=', '>', '>=', '<', '<=', '^=', '!^=', '~=', '!~=')
	 */
	public function listSearchAddFieldDate($name, $label='', $alias="", $help='', $operator='')
	{
		$options = array();
		if(!empty($alias))$options['alias'] = $alias;
		if(!empty($help))$options['help'] = $help;
        if(!empty($operator))$options['operator'] = $operator;

		$this->listSearchAddField($name, $label, 'date', $options);
	}

	/**
	 * Add field type datetime in search engine
	 *
	 * @param string $name db column
	 * @param string $label label to display if empty $label = $name
	 * @param string $alias sql alias name
	 * @param string $help help message
     * @param string $operator operator selected (`=`, '!=', '>', '>=', '<', '<=', '^=', '!^=', '~=', '!~=')
	 */
	public function listSearchAddFieldDatetime($name, $label='', $alias="", $help='', $operator='')
	{
		$options = array();
		if(!empty($alias))$options['alias'] = $alias;
		if(!empty($help))$options['help'] = $help;
        if(!empty($operator))$options['operator'] = $operator;

		$this->listSearchAddField($name, $label, 'datetime', $options);
	}


	/**
	 * is user is launched the search engine
	 * @return bool
	 */
	public function listSearchAsked()
	{
		$b = false;

		if(isset($_GET['user_se']))
			$b = true;

		return $b;
	}

	/**
	 * is user searching
	 * @return bool
	 */
	public function listUserIsSearching()
	{
		return $this->listSearchAsked();
	}


    /**
     * Verify if action is allowed by user
     *
     * @param string $action
     * @return bool verify
     */
	public function rightAllowed($action)
	{
		$this->nuts->dbSelect("SELECT
											COUNT(*)
									FROM
											NutsMenu,
											NutsMenuRight
									WHERE
											NutsMenu.ID = NutsMenuRight.NutsMenuID AND
											NutsMenuRight.NutsGroupID = %d AND
											NutsMenu.Name = '%s' AND
											NutsMenuRight.Name = '%s'",
									array(
											$_SESSION['NutsGroupID'],
											$_GET['mod'],
											$action
										)
								   );

		$res = $this->nuts->getOne();
		if($res == 1)
			return true;

		return false;
	}

	/**
	 * Allow list add button
	 * @var bool
	 */
	public $listAddButton = true;
	/**
	 * Allow list view button
	 * @var bool
	 */
	public $listViewButton = true;
	/**
	 * Allow list edit button
	 * @var bool
	 */
	public $listEditButton = true;
	/**
	 * Allow list delete button
	 * @var bool
	 */
	public $listDeleteButton = true;
	/**
	 * Set list first order by
	 * @var bool
	 */
    public $listFirstOrderBy = '';

    /**
	 * Set list excel mode
	 * @var bool
	 */
    public $listExportExcelMode = false;

    /**
	 * Set list excel mode apply hookdata
	 * @var bool
	 */
    public $listExportExcelModeApplyHookData = false;


	/**
	 * Set the first column to order
	 * @param string $field
	 */
	public function listSetFirstOrderBy($field)
	{
		$this->listFirstOrderBy = $field;
	}

	/**
	 * Set list width in auto instead of 100%
	 * @var bool
	 */
	public $listWidth = '';

	/**
	 *
	 */
	public function listSetWidthAuto()
	{
		$this->listWidth = 'auto';
	}


	/**
	 * Set the first column to order sort
	 * @param string $sort asc or desc
	 */
	public function listSetFirstOrderBySort($sort)
	{
		if(!isset($_GET['tsens']))
			$_GET['tsens'] = strtolower($sort);
	}

	/**
	 * Allow list excel export button
	 * @var bool
	 */
	public $listAllowExcelExport = true;

	/**
	 * Display list pager
	 * @var bool display list pager
	 */
	public  $listPager = true;

	/**
	 * Display list pager records selection
	 * @var boolean display list pager record select
	 */
	public  $listPagerRecordSelect = true;

	/**
	 * Waiting for user searching to display data
	 * @var bool
	 */
	public $listWaitingForUserSearching = false;

	/**
	 * Waiting for user searching to display data message
	 * @var string
	 */
	public $listWaitingForUserSearchingMessage = '';



	private $listAddButtonUrlAdd = '';

	/**
	 * change button label
	 */
	public $listAddButtonLabel = '';


	/**
	 * Add string to add button url
	 * @param string query $uri
	 */
	public function listAddButtonUrlAdd($uri)
	{
		$this->listAddButtonUrlAdd = $uri;
	}


	/**
	 * Automatic list position number
	 * @var int
	 */
	private $listPositionIndex;

	/**
	 * Add position columns
	 *
	 * @param string $columnPositionName
	 * @param string $columnFather
	 * @param string $columFatherValue
	 * @param string $tableName
	 */
	public function listAddColPosition($columnPositionName, $columnFather='', $columFatherValue='', $tableName='')
	{
		$this->listPositionIndex = 1;

		$this->listAddCol($columnPositionName, '&nbsp;', "center; width:10px; text-align:center; white-space:nowrap;", false);
		if(empty($tableName))$tableName = $this->dbtable;

		// get action special
		if(isset($_GET['nuts_position']) && !empty($_GET['nuts_position']) && $_POST && isset($_POST['list']) && !empty($_POST['list']))
		{
			$current_positon = 1;
			$list = explode(';', $_POST['list']);
			foreach($list as $li)
			{
				$li = str_replace('ls_tr_', '', $li);
				$li = (int)$li;

				if($li)
				{
					$sql_added = "";
					if(!empty($columnFather) && !empty($columFatherValue))
						$sql_added = " AND $columnFather = $columFatherValue ";

					$sql = "UPDATE
									$tableName
							SET
									$columnPositionName = $current_positon
							WHERE
									ID = $li
									$sql_added";
					$this->nuts->doQuery($sql);
					$current_positon++;
				}
			}


			die("ok");

		}
	}

	/**
	 * Get position arrows image
	 *
	 * @param int $ID
	 * @param int $Position
	 * @return string
	 */
	public function listGetPositionContents($ID)
	{
		$Position = $this->listPositionIndex++;
		$uri = "index.php?".$_SERVER['QUERY_STRING'];
		$uri .= "&nuts_position=$ID&_im=0";

		/*$str = <<<EOF
							<span class="listDnd">
							<!--  <a href="javascript:system_position('{$uri}@up');"><img src="img/arrow_up.png" class="arrow_updown"  align="bottom" /></a> $Position <a href="javascript:system_position('{$uri}@down');"><img src="img/arrow_down.png" class="arrow_updown" align="bottom" /></a> -->
							</span>

EOF;*/

		$str = <<<EOF
							<span class="listDnd noprint" uri="{$uri}" ><img src="/nuts/css/draggable.gif"></span>

EOF;



		return $str;
	}


	/**
	 * @var bool add copy action
	 */
	public $listCopyButton = true;

	/**
	 * @var array sum row at end
	 */
	private $listSumRow = array();

	/**
	 * Add total count at end of the list
	 *
	 * @param string $title
	 * @param string $sql_col column
	 * @param string $sum_after text to add after
	 * @param string $sql_template sql query template dont' forget to include [[SQL_WHEREX]] in your custom query to add dynamic search and SQL added
	 * @param string $format_number apply format number as float
	 */
	public function listAddSumRow($title, $sql_col, $sum_after='', $sql_template='', $format_number=true)
	{
		$this->listSumRow[] = array(
										'title' => $title,
										'sql_col' => $sql_col,
										'sum_after' => $sum_after,
										'sql_template' => $sql_template,
										'format_number' => $format_number
									  );
	}

    /**
     *
     * Display the list view
     *
     * @param int $nbRec records by page (default = 10)
     * @param string $hookData function to call foreach rows before treatment
	 * @return int count found
     */
	public function listRender($nbRec=10, $hookData='')
	{
		global $nuts_lang_msg;


		if(!isset($_GET['pager_rec']) || !in_array($_GET['pager_rec'], array(10, 20, 50, 100, 250, 500)))
			$_GET['pager_rec'] = $nbRec;
		$_GET['pager_rec'] = (int)$_GET['pager_rec'];

		$this->nuts->open(WEBSITE_PATH.'/nuts/_templates/list.html');

		// excel
		if(!$this->listAllowExcelExport)
		{
			$this->nuts->eraseBloc('excel_export');
		}

		// pager
		if(!$this->listPager || $nbRec == 0)
		{
			$this->nuts->eraseBloc('pager');
			$this->listPagerRecordSelect = false;
		}

		// pager_rec
		if(!$this->listPagerRecordSelect)
		{
			$this->nuts->eraseBloc('pager_record_select');
		}

		// js list
		if(!file_exists(PLUGIN_PATH.'/list.js'))
			$this->nuts->eraseBloc('js');

		// search engine
		// <editor-fold defaultstate="collapsed" desc="search engine">
		if(count($this->list_search) == 0)
		{
			$this->nuts->eraseBloc('search');
		}
		else
		{
			if(!$this->listSearchOpenOnload)
				$this->nuts->eraseBloc('search_display');

            $operators_items = array('operator_equal', 'operator_not_equal', 'operator_gt', 'operator_gt_equal', 'operator_lt', 'operator_lt_equal', 'operator_begin', 'operator_not_begin', 'operator_countains', 'operator_not_countains');
            $operators_signs = array('=', '!=', '>', '>=', '<', '<=', '^=', '!^=', '~=', '!~=');

			foreach($this->list_search as $s)
			{
				$this->nuts->parse('fields.label', $s['label']);
				$this->nuts->parse('fields.name', $s['name']);
				if(!isset($s['options']['class']))$s['options']['class'] = '';
				if(!isset($s['options']['help']))$s['options']['help'] = '';
				if(!isset($s['options']['operator']))$s['options']['operator'] = '=';


                // operator assignation
                $z = 0;
                foreach($operators_items as $operators_item)
                {
                    $set_selected = '';
                    if($s['options']['operator'] == $operators_signs[$z])
                        $set_selected = ' selected="selected"';
                    $this->nuts->parse('fields.'.$operators_item, $set_selected);
                    $z++;
                }



				$this->nuts->parse('fields.help', $s['options']['help']);


				if($s['type'] == 'text' || $s['type'] == 'datetime' || $s['type'] == 'date')
				{
					$this->nuts->parse('fields.text.name', $s['name']);
					$this->nuts->parse('fields.text.class', $s['options']['class']);



					if($s['type'] == 'text')
					{
						$this->nuts->eraseBloc('fields.text.datetime');
					}
					else
					{
						$this->nuts->parse('fields.text.datetime.name', $s['name']);
						$this->nuts->parse('fields.text.datetime.type', $s['type']);
					}

					$this->nuts->loop('fields.text.datetime');
					$this->nuts->loop('fields.text');
					$this->nuts->eraseBloc(array('fields.select'));
				}
				elseif($s['type'] == 'select' || $s['type'] == 'select-sql' || $s['type'] == 'boolean' || $s['type'] == 'booleanX')
				{
					$this->nuts->parse('fields.select.name', $s['name']);
					$this->nuts->parse('fields.select.class', $s['options']['class']);

					// select
					if($s['type'] == 'select')
					{
						if(!is_array($s['options']['options']))
						{
							$this->nuts->parseBloc('fields.select.options', $s['options']['options']);
						}
						else
						{
							if(count($s['options']['options']) == 0)
							{
								$this->nuts->eraseBloc('fields.select.options');
								$this->nuts->loop('fields.select.options');
							}
							else
							{
								foreach($s['options']['options'] as $opts2)
								{
									if(is_string($opts2))
									{
										$value = strtoupper($opts2);
										$label = ucfirst(strtolower($value));
									}
									else
									{
										$value = $opts2['value'];
										$label = $opts2['label'];
									}

									$this->nuts->parse('fields.select.options.label', $label);
									$this->nuts->parse('fields.select.options.value', $value);
									$this->nuts->loop('fields.select.options');
								}
							}
						}

					}

					// sql auto
					elseif($s['type'] == 'select-sql')
					{
						if(!isset($s['options']['where']))$s['options']['where'] = '';
						if(!empty($s['options']['where']))$s['options']['where'] = " AND ".$s['options']['where'];


						// children table detected
						//if(ereg('ID$', $s['name']))
						if(preg_match('/ID$/', $s['name']))
						{
							$str = str_replace('ID', '', $s['name']);
							$tmp[0] = $str;
							$tmp[1] = 'Name';

							if(isset($s['options']['table']))$tmp[0] = $s['options']['table'];
							if(isset($s['options']['field']))$tmp[1] = $s['options']['field'];

							$tmp[2] = $tmp[1];
							if(isset($s['options']['order_by']))$tmp[2] = $s['options']['order_by'];

							$this->nuts->doQuery("SELECT
															ID AS value,
															{$tmp[1]} AS label
													FROM
															{$tmp[0]}
													WHERE
															Deleted = 'NO'
															{$s['options']['where']}
													ORDER BY
															{$tmp[2]}");
						}
						else
						{
							if(!isset($s['options']['table']))
								$s['options']['table'] = $this->dbtable;

							if(!isset($s['options']['field']))
								$s['options']['field'] = $s['name'];

							if(!isset($s['options']['order_by']))
								$s['options']['order_by'] = $s['name'];

							$this->nuts->doQuery("SELECT
															DISTINCT {$s['options']['field']}
													FROM
															{$s['options']['table']}
													WHERE
															{$s['name']} != '' AND
															Deleted = 'NO'
															{$s['options']['where']}
													ORDER BY
															{$s['options']['order_by']}");
						}

						$res = $this->nuts->dbNumRows();
						if($res == 0)
						{
							$this->nuts->eraseBloc('fields.select.options');
						}
						else
						{
							while($row = $this->nuts->dbFetch())
							{
								if(!isset($row['label']))$row['label'] = $row[$s['name']];
								if(!isset($row['value']))$row['value'] = $row[$s['name']];

								$this->nuts->parse('fields.select.options.label', $row['label']);
								$this->nuts->parse('fields.select.options.value', $row['value']);
								$this->nuts->loop('fields.select.options');
							}
						}
					}

					// boolean
					elseif($s['type'] == 'boolean')
					{
						// yes
						$this->nuts->parse('fields.select.options.label', $nuts_lang_msg[30]);
						$this->nuts->parse('fields.select.options.value', 'YES');
						$this->nuts->loop('fields.select.options');

						// no
						$this->nuts->parse('fields.select.options.label', $nuts_lang_msg[31]);
						$this->nuts->parse('fields.select.options.value', 'NO');
						$this->nuts->loop('fields.select.options');
					}
					// booleanX
					elseif($s['type'] == 'booleanX')
					{
						// no
						$this->nuts->parse('fields.select.options.label', $nuts_lang_msg[31]);
						$this->nuts->parse('fields.select.options.value', 'NO');
						$this->nuts->loop('fields.select.options');

						// yes
						$this->nuts->parse('fields.select.options.label', $nuts_lang_msg[30]);
						$this->nuts->parse('fields.select.options.value', 'YES');
						$this->nuts->loop('fields.select.options');
					}

					$this->nuts->loop('fields.select');
					$this->nuts->eraseBloc(array('fields.text'));
				}

				$this->nuts->loop('fields');
			}
		}
		// </editor-fold>


		// waiting for user seaching
		if($this->listWaitingForUserSearching && !isset($_GET['user_se']))
		{
			$this->nuts->parse('waiting_user_searching_message', $this->listWaitingForUserSearchingMessage);
		}
		else
		{
			$this->nuts->eraseBloc('waiting_user_searching');
			$this->listWaitingForUserSearching = false;
		}


		$nb_cols = count($this->cols);


		// set width
		if(!empty($this->listWidth))
			$this->listWidth = "width:{$this->listWidth};";
		$this->nuts->parse('list_style_width', $this->listWidth);


		// add special button add
		$ra = $this->rightAllowed("add");


		if((!$this->listAddButton || !$ra) && count($this->list_buttons) == 0)
		{
			$this->nuts->eraseBloc('add');
		}
		else
		{
		    // add button
		    if(!$this->listAddButton || !$ra)
		    {
		       $this->nuts->eraseBloc('add_button');
			}
			else
			{
				$this->nuts->parse('add_button.add_parameters', $this->listAddButtonUrlAdd);

				if(empty($this->listAddButtonLabel))
						$this->listAddButtonLabel = $nuts_lang_msg[7];

				$this->nuts->parse('add_button.add_button_label', $this->listAddButtonLabel);
			}

		    // buttons
		    if(count($this->list_buttons) == 0)
		    {
		        $this->nuts->eraseBloc('buttons');
			}
			else
			{
			    $this->nuts->loadArrayInBloc('buttons', $this->list_buttons);
			}
		}

		// view
        if(!$this->rightAllowed("view")) $this->listViewButton = false;
		if(!$this->listViewButton || !$this->rightAllowed("view"))
		{
			$this->nuts->eraseBloc('th_view');
			$this->nuts->eraseBloc('td_view');
		}
		else
		{
			$nb_cols++;
		}

		// special copy button
        if(!$this->rightAllowed("edit")) $this->listEditButton = false;
		if($this->listCopyButton && ($this->listAddButton && $this->rightAllowed("add")) && ($this->listEditButton && $this->rightAllowed("edit")))
		{
			$img = '<img src="img/list_duplicate.png">';
			$this->cols[] = '<a class="tt list_btn_duplicate" href="javascript:;" onclick="formIt(\''.$nuts_lang_msg[20].'\', \'?mod='.$this->name.'&do=add&cID={ID}\');" title="'.$nuts_lang_msg[74].'">'.$img.'</a>';
			$this->colsLabel[] = '';
			$this->colsStyle[] = 'center; width:10px';
			$this->colsOrderBy[] = false;
			$this->colsClass[] = 'noprint';
			$nb_cols++;
		}



		// edit
		if($this->listEditButton && $this->rightAllowed("edit"))
		{
			$img = '<img src="img/list_edit.png">';
			$this->cols[] = '<a class="tt list_btn_edit" href="javascript:;" onclick="formIt(\''.$nuts_lang_msg[27].' - #{ID}\', \'?mod='.$this->name.'&do=edit&ID={ID}\');" title="'.$nuts_lang_msg[9].'">'.$img.'</a>';
			$this->colsLabel[] = '';
			$this->colsStyle[] = 'center; width:10px';
			$this->colsOrderBy[] = false;
			$this->colsClass[] = 'noprint';
			$nb_cols++;
		}

		// delete
        if(!$this->rightAllowed("delete")) $this->listDeleteButton = false;
		if($this->listDeleteButton && $this->rightAllowed("delete"))
		{
			$img = '<img src="img/list_delete.png">';
			$this->cols[] = '<a class="tt list_btn_delete" href="javascript:;" onclick="formIt(\''.$nuts_lang_msg[29].' - #{ID}\', \'?mod='.$this->name.'&do=delete&ID={ID}\');" title="'.$nuts_lang_msg[10].'">'.$img.'</a>';
			$this->colsLabel[] = '';
			$this->colsStyle[] = 'center; width:10px';
			$this->colsOrderBy[] = false;
			$this->colsClass[] = 'noprint';
			$nb_cols++;
		}


		for($i=0; $i <  count($this->cols); $i++)
		{
			// thead
			$this->nuts->parse('th.name', $this->colsLabel[$i]);
			$this->nuts->parse('th.style', $this->colsStyle[$i]);
			$this->nuts->parse('th.class', $this->colsClass[$i]);

			$order_by = '';
			if($this->colsOrderBy[$i] == true)
			{
				$order_by = '{_order_by::'.$this->cols[$i].'}';
			}
			$this->nuts->parse('th.order_by', $order_by);
			$this->nuts->loop('th');

			// tbody
			$this->nuts->parse('td.class', $this->colsClass[$i]);
			$noclick = false;
			if(empty($this->colsLabel[$i]))$noclick = true;
			$this->nuts->parse('td.noclick', $noclick);

			if(empty($this->colsImg[$i]))
				$this->nuts->parse('td.name', $this->cols[$i]);
			else
				$this->nuts->parse('td.name', $this->colsImg[$i]);

			$this->nuts->parse('td.style', $this->colsStyle[$i]);
			$this->nuts->loop('td');
		}

		$this->nuts->parse('nb_cols', $nb_cols);

		$out = $this->nuts->output();

		$out = str_replace('{ ', '{', $out);
		$out = str_replace('{<', '<', $out);
		$out = str_replace(' }', '}', $out);
		$out = str_replace('>}', '>', $out);

		$out = str_replace('< bloc::', '<bloc::', $out);
		$out = str_replace('< /bloc::', '</bloc::', $out);

		// virtual parsing
		$this->nuts->setNavColor('#eee', '#fff');
		$this->nuts->createVirtualTemplate($out);

		// list all order by
		$orders = array();
		for($i=0; $i <  count($this->cols); $i++)
		{
			if($this->colsOrderBy[$i] == true)
			{
			     $orders[] = $this->cols[$i];
			}

		}
        if(!empty($this->listFirstOrderBy))
        {
        	$tmp = $orders;
            $orders = array();
        	$orders[] = $this->listFirstOrderBy;
            foreach($tmp as $tmp_o)
            {
                if(!in_array($tmp_o, $orders))
                    $orders[] = $tmp_o;
            }

		}

		if(count($orders) > 0)
			$this->nuts->showRecordsOrderBy($orders);

		// url reconstruction
		$uri = explode('&', $_SERVER['QUERY_STRING']);
		$tmp = '';
		foreach($uri as $u)
		{
			$tmp2 = explode('=', $u);
			if(count($tmp2) == 2 && !in_array($tmp2[0], array('tpg', 'torder_by', 'tsens', 'ajax', 'target', 't')))
				$tmp .= $u.'&';
		}
		$this->nuts->setUrl('?'.$tmp);

		if(!empty($this->sql_added))
			$this->sql_added = ", ".$this->sql_added;

		if(!empty($this->sql_where_added) && !preg_match("/(ORDER BY|GROUP BY)/", $this->sql_where_added))
			$this->sql_where_added = " AND ".$this->sql_where_added;

		// add WHERE clause
		$sql = "SELECT
						*
						$this->sql_added
				FROM
						".$this->dbtable."
				WHERE
						Deleted = 'NO'
						$this->sql_where_added";

		// dynamic search
		$x_sql = "";
		foreach($this->list_search as $s)
		{
			if(
				isset($_GET[$s['name']]) &&
			    strlen(trim($_GET[$s['name']])) &&
			    isset($_GET[$s['name'].'_operator']) &&
				in_array($_GET[$s['name'].'_operator'], array('_equal_', '_not_equal_', '_gt_', '_gtequal_', '_lt_', '_ltequal_', '_begin_', '_not_begin_', '_countains_', '_not_countains_'))
			  )
			 {
				if(isset($s['options']['alias']))
				{
					$x_sql .= ' AND '.$s['options']['alias'];
				}
				else
				{
			 		$x_sql .= ' AND '.$s['name'];
				}

				// hacks special for date with 10 chars 0000-00-00
				if(preg_match('/Date$/', $s['name']) &&  $_GET[$s['name'].'_operator'] == '_equal_' && strlen($_GET[$s['name']]) == 10)
				{
					$_GET[$s['name'].'_operator'] = '_begin_';
				}

				// operator
				if($_GET[$s['name'].'_operator'] == '_equal_')$x_sql .= ' = ';
				elseif($_GET[$s['name'].'_operator'] == '_not_equal_')$x_sql .= ' != ';
				elseif($_GET[$s['name'].'_operator'] == '_gt_')$x_sql .= ' > ';
				elseif($_GET[$s['name'].'_operator'] == '_gtequal_')$x_sql .= ' >= ';
				elseif($_GET[$s['name'].'_operator'] == '_lt_')$x_sql .= ' < ';

				elseif($_GET[$s['name'].'_operator'] == '_ltequal_')$x_sql .= ' <= ';
				elseif(in_array($_GET[$s['name'].'_operator'], array('_begin_', '_countains_')))$x_sql .= ' LIKE ';
				else $x_sql .= ' NOT LIKE ';

				// values
				if(in_array($_GET[$s['name'].'_operator'], array('_equal_', '_not_equal_', '_gt_', '_gtequal_', '_lt_', '_ltequal_')))
				{
					// automatic conversion for ID and foreign key
					//if(ereg('ID$', $s['name']))
					if(preg_match('/ID$/', $s['name']))
					{
						// special for ajax autocomplete
						if(strpos($_GET[$s['name']], ' (') !== false)
						{
							$v = explode(' (', $_GET[$s['name']]);
							$_GET[$s['name']] = end($v);
						}

						$_GET[$s['name']] = (int)$_GET[$s['name']];
						$x_sql .= addslashes($_GET[$s['name']])."\n";
					}
					elseif(in_array($s['type'], array('date', 'datetime')))
					{
						// convert user date to gmt date
						$v = addslashes($_GET[$s['name']]);

                        //if(ereg('GMT$',$s['name']))
						// if(preg_match('/GMT$/', $s['name']))
                            // $v = nutsGetGMTDateUser($v, '', 'gmt');

						// hacks special for date with 10 chars 00/00/0000
						if(preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#i", $_GET[$s['name']]))
						{
							list($d, $m, $Y) = explode('/', $_GET[$s['name']]);
							$v = "$Y-$m-$d";
						}

						// hacks special for datetime with 16 chars 00/00/0000 00:00
						if(preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2})#i", $_GET[$s['name']]))
						{
							list($d, $m, $Y) = explode('/', $_GET[$s['name']]);
							list($Y, $time) = explode(' ', $Y);
							$v = "$Y-$m-$d $time";
						}

						// hacks special for datetime without time XX/XX/XXXX XX:XX
						if($s['type'] == 'datetime' && strlen($_GET[$s['name']]) == 16)
						{
							$v .= ":00";
						}


                        $x_sql .= "'".$v."'\n";
					}
					else
					{
						$x_sql .= "'".addslashes($_GET[$s['name']])."'\n";
					}
				}
				else
				{
					$val = $_GET[$s['name'].'_operator'];
					$search = addslashes($_GET[$s['name']]);

					// hacks special for date with 10 chars 00/00/0000
					if(preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#i", $search))
					{
						list($d, $m, $Y) = explode('/', $search);
						$search = "$Y-$m-$d";
					}

					// hacks special for datetime without time XX/XX/XXXX XX:XX
					if($s['type'] == 'datetime' && strlen($search) == 16)
					{
						$search .= ":00";
					}



					if($val == '_begin_')$x_sql .= "'".$search."%'";
					elseif($val == '_not_begin_')$x_sql .= "'".$search."%'";
					elseif($val == '_countains_')$x_sql .= "'%".$search."%'";
					elseif($val == '_not_countains_')$x_sql .= "'%".$search."%'";
				}
			 }
		}

		$sql .= $x_sql;

		// add after where
		if(!empty($this->sql_after_where_added))
		{
			$sql .= $this->sql_after_where_added;
		}


		// excel export
		if($this->listAllowExcelExport && isset($_GET['nuts_export']) && $_GET['nuts_export'] == 'excel')
		{
            $this->listExportExcelMode = true;

			$file_name = $this->name.'_'.date('ymd_His');
			if($file_name[0] == '_')$file_name[0] = '';
			$file_name =  trim($file_name);

			// header("Content-type: application/vnd.ms-excel, charset=UTF-8; encoding=UTF-8");
			header('Content-Type: application/csv');
			header("Content-disposition: attachment; filename=\"$file_name.csv\"");
			// echo "\xEF\xBB\xBF"; // force UTF8

			// add ORDER BY clause
			if(isset($_GET['torder_by'])){
				// clause of the end ?
				if(preg_match("#(.*)ORDER BY\n(.*)$#", $sql))
					$sql .= " ,{$_GET['torder_by']} {$_GET['tsens']}";
				else
					$sql .= " ORDER BY\n\t{$_GET['torder_by']} {$_GET['tsens']}";
			}

			$this->nuts->doQuery($sql);

			// create keys
			$del = 0;
			if($this->listEditButton)$del++;
			if($this->listDeleteButton)$del++;

			for($i=0; $i <  count($this->cols)-$del; $i++)
				echo '"'.utf8_decode($this->colsLabel[$i]).'";';
			echo "\n";
			while($row = $this->nuts->dbFetch())
			{
                if($this->listExportExcelModeApplyHookData && function_exists('hookData'))
                    $row = hookData($row);

				for($i=0; $i <  count($this->cols)-$del; $i++)
				{
					$txt = str_replace('"', '\"', @$row[$this->cols[$i]]);
					$txt = str_replace("\r", '\r', $txt);
					$txt = str_replace("\n", '\n', $txt);
					echo '"'.utf8_decode($txt).'";';
				}
				echo "\n";
			}
			exit();
		}


		// add or remove total_count
		// $this->listAddSumRow('Total 1', 'DateGMT', '€');
		if(count($this->listSumRow) == 0 || $this->listWaitingForUserSearching)
		{
			$this->nuts->eraseBloc('total_count');
		}
		else
		{
			foreach($this->listSumRow as $sum_row)
			{
				$this->nuts->parse('data.total_count_row.total', $sum_row['title']);
				$this->nuts->parse('data.total_count_row.sum_after', $sum_row['sum_after']);

				// add dynamic searching
				if(empty($sum_row['sql_template']))
				{
					$sql_sum = "SELECT SUM({$sum_row['sql_col']}) FROM {$this->dbtable} WHERE Deleted = 'NO' {$this->sql_where_added}";
					$sql_sum .= ' '.$x_sql;
				}
				else
				{
					$sql_sum = $sum_row['sql_template'];
					$sql_sum = str_replace('[[SQL_WHEREX]]', $this->sql_where_added." ".$x_sql, $sql_sum);
				}

				$this->nuts->doQuery($sql_sum);
				$sum_number = $this->nuts->dbGetOne();

				// format number
				if($sum_row['format_number'])
					$sum_number = number_formatX($sum_number);

				$this->nuts->parse('data.total_count_row.sum', $sum_number);
				$this->nuts->loop('data.total_count_row');
			}
		}


		$count = -1;
        $this->listSQL     = $sql;
		if($this->listWaitingForUserSearching)
		{
			$this->nuts->eraseBloc('data');
		}
		else
		{
			$count = $this->nuts->showRecords($sql, $_GET['pager_rec'], $hookData);
		}

		$this->render = $this->nuts->output();
		return $count;

	}


    /**
     * Display a template directly
     *
     * @param string $template path of the template
     */
	public function directRender($template)
	{
		$this->nuts->open($template);
		$this->render = $this->nuts->output();
	}

    /**
     *
     * Get data for one column
     *
     * @param string $sql
     * @return string data
     */
	public function getQuery($sql)
	{
		$this->nuts->doQuery($sql);
		return $this->nuts->getOne();
	}
    /**
     * Get data from a query
     *
     * @param string $sql
     * @return array data
     */
	public function getData($sql)
	{
		$this->nuts->doQuery($sql);
		return $this->nuts->getData();
	}


	private $formParameters;

	/**
	 * Add parameter sting to form action
	 *
	 * @param string $params separated by &
	 */
	public function formActionAddParameter($params)
	{
		$this->formParameters = $params;
	}

	/**
	 * Get max position
	 *
	 * @param string $colomnPositionName
	 * @param string $columnFather
	 * @param int $columFatherValue
	 * @param string $tableName
	 * @return int
	 */
	public function formGetMaxPosition($colomnPositionName, $columnFather='', $columFatherValue='', $tableName='')
	{
		if(empty($tableName))$tableName = $this->formDBTable[0];
		$sql = "SELECT MAX($colomnPositionName) FROM $tableName WHERE Deleted = 'NO'";
		if(!empty($columnFather))
		{
			$sql .= " AND $columnFather = '".addslashes($columFatherValue)."'";
		}

		$this->nuts->doQuery($sql);
		$pos = (int)$this->nuts->dbGetOne();

		return $pos;
	}


    /**
     * Get mode used in form ADD or EDIT
     *
     * @return ADD or EDIT
     */
    public function formGetMode()
    {
        $form_mode = '';
        if(@$_GET['ID'] == 0 || (@$_GET['cID'] != 0 && $this->listCopyButton == true))
        {
            $form_mode = 'ADD';
        }
        else
        {
            $form_mode = 'EDIT';
        }

        return $form_mode;
    }


    /**
     * Is form mode in adding
     * @return bool
     */
    public function formModeIsAdding()
    {
        return  ($this->formGetMode() == 'ADD');
    }

    /**
     * Is form mode in editing
     * @return bool
     */
    public function formModeIsEditing()
    {
        return ($this->formGetMode() == 'EDIT');
    }




	private $formFields = array();
    /**
     * Add field in form - please use direct form method instead formAddField*
     *
     * @param string $name
     * @param string $label if empty label = $name
     * @param string $type:
	 * <b>&bull; text (default)</b> : input text you can use these option: ucfirst, upper, lower, integer, float<br><br>
	 * <b>&bull; colorpicker </b> : colorpicker widget<br><br>
	 * <b>&bull; select</b> : select element<br><br>
	 * <b>&bull; select-sql</b> : select element with automatic fetching options values<br><br>
	 * <b>&bull; select-html</b> : select element with html values<br><br>
	 * <b>&bull; ajax_autocomplete</b> : ajax auto-completion<br><br>
	 * <b>&bull; boolean</b> : boolean select<br><br>
	 * <b>&bull; boolean and booleanX</b> : boolean select YES, NO<br><br>
	 * <b>&bull; date</b> : date picker element<br><br>
	 * <b>&bull; datetime</b> : date with time picker element<br><br>
	 * <b>&bull; hidden</b> : hidden html element<br><br>
	 * <b>&bull; file</b> : file upload<br><br>
	 * <b>&bull; image</b> : file image upload<br><br>
	 * <b>&bull; filemanager</b> : generic file manager access<br><br>
	 * <b>&bull; filemanager_image</b> : image file manager access<br><br>
	 * <b>&bull; filemanager_media</b> : Media file manager access<br><br>
	 *
     * @param bool $required
	 *
     * @param array $options:
	 *
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; help:</b> add a help tooltip on element
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; attributes:</b> add your html attributes (use class=checkbox-list with selec multiple to transform to radio checklist)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; class:</b> add your class (use tabby for tabulation, upper, lower)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; after:</b> add text after element<br><br>
	 *		<br>
	 *		<br>
	 *			<b>&bull; option for select-sql:</b> ,
	 *		<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; field:</b> field name<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; table:</b> table name<br><br>
	 *		<br><br>
	 *		<b>&bull; option for ajax_autocomplete:</b>
	 *		<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_mode:</b> begins (default) or countains<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_column:</b> column to check<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_columnID:</b> column ID for autocompletion<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_table:</b> set table name<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_get:</b> get parameter name<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_sql_where:</b> add sql where clause<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; ac_custom_sql:</b> set complete sql query place [q] keyword
	 *		<br><br>
	 *		<b>&bull; option for filemanager, filemanager_image, filemanager_media:</b>
	 *		<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; folder:</b> open in specific folder (create if not exists)<br><br>
	 *		<br><br>
	 *		<br><br>
	 *		<b>&bull; option for image and file:</b>
	 *		<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; path:</b> path to folder (no slash at end)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; url:</b> url to folder image (use path rewritten if omitted no slash at end)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; size:</b> maximum size in Ko or Mo (ex: 1 Mo)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; exts:</b> allowed image extension (ex: jpg,gif)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; virtual_url:</b> relative url for file if your folder is protected<br><br>
	 *
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; parent_suffix:</b> add a suffix to your image (thumbnail name inherit automatically)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; parent_resize:</b> allow image to be resized<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; parent_width:</b> width of image (orginal)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; parent_height:</b> height of image (orginal)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; parent_constraint:</b> force image dimension or use adaptive algorithm<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; parent_background_color (array):</b> apply a background color in rgb defaul black: array(255,255,255) only in constraint mode<br><br>
	 *
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; thumbnail_new (bool):</b> create a new image from original<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; thumbnail_width :</b> width of image (orginal or new)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; thumbnail_height :</b> height of image (orginal or new)<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; thumbnail_constraint (bool):</b> force image dimension or use adaptive algorithm<br><br>
	 *		&nbsp; &nbsp; &nbsp; &nbsp;<b>&bull; thumbnail_background_color (array):</b> apply a background color in rgb defaul black: array(255,255,255) only in constraint mode<br><br>
     */
	public function formAddField($name, $label='', $type='text', $required=false, $options=array())
	{
		if(empty($label))$label = $name;
		if($type == 'hidden')$label = ' ';

		// image & file
		if($type == 'image' || $type == 'file')
		{
			// assign default path nuts_uploads/plugin_name
			if(!isset($options['path']))$options['path'] = WEBSITE_PATH.'/nuts_uploads/'.$this->name;

			// assign default url
			if(!isset($options['url']))$options['url'] = str_replace(WEBSITE_PATH, '', $options['path']);
		}

		// ajax_autocomplete
		if($type == 'ajax_autocomplete')
		{
			$options['class'] = (!isset($options['class'])) ? 'ajax_autocomplete': ' ajax_autocomplete';

			if(!isset($options['ac_columnID']))$options['ac_columnID'] = '';
			if(!isset($options['ac_column']))$options['ac_column'] = $name;
			if(!isset($options['ac_get']))$options['ac_get'] = $name;
			if(!isset($options['ac_table']))$options['ac_table'] = $this->formDBTable[0];
			$options['ac_sql_where'] = (!isset($options['ac_sql_where'])) ? '' : ' AND '.$options['ac_sql_where'];
			if(!isset($options['ac_custom_sql']))$options['ac_custom_sql'] = '';
			if(!isset($options['ac_mode']))$options['ac_mode'] = 'begins';


			// direct render for ajax
			if((isset($_GET['form_ajax_ac']) && $_GET['form_ajax_ac_col'] == $options['ac_get'] && @strlen($_GET['q']) >= 2))
			{
				$q = addslashes($_GET['q']);
				$begin_percent = ($options['ac_mode'] == 'begins') ? '' : '%';

				$limit = "LIMIT 200";

				if(empty($options['ac_custom_sql']))
				{
					if(empty($options['ac_columnID']))
					{
						$sql = "SELECT
										DISTINCT {$options['ac_column']} AS val
								FROM
										{$options['ac_table']}
								WHERE
										Deleted = 'NO'
										{$options['ac_sql_where']} AND
										{$options['ac_column']} LIKE '{$begin_percent}$q%'
								ORDER BY
										{$options['ac_column']}
								$limit";
					}
					else
					{
						$sql = "SELECT
										CONCAT({$options['ac_column']},' (',{$options['ac_columnID']},')') AS val
								FROM
										{$options['ac_table']}
								WHERE
										Deleted = 'NO'
										{$options['ac_sql_where']} AND
										{$options['ac_column']} LIKE '{$begin_percent}$q%'
								ORDER BY
										{$options['ac_column']}
								$limit";
					}
				}
				else
				{
					$sql = str_replace('[q]', $q, $options['ac_custom_sql']);
				}

				$this->nuts->doQuery($sql);
				$res = array();
				while($row = $this->nuts->dbFetch())
					$res[] = $row['val'];

				die(join("\n", $res));

			}
		}

		$this->formFields[] = array('name'  => $name,
									'label' => $label,
									'type'  => $type,
									'required' => $required,
									'opts' => $options);

	}

	/**
	 * Add field type textarea in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $class add special class like ucfirst, upper, lower
	 * @param string $style css style to add
	 * @param string $attributes add custom attributes in html input
	 * @param string $help help message
	 * @param string $value value
	 */
	public function formAddFieldTextArea($name, $label='', $required, $class='', $style='', $attributes='', $help='', $value='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($class))$options['class'] = $class;
		if(!empty($style))$options['style'] = $style;
		if(!empty($attributes))$options['attributes'] = $attributes;
		if(!empty($help))$options['help'] = $help;
		if(strlen($value) > 0)$options['value'] = $value;

		$this->formAddField($name, $label, 'textarea', $required, $options);
	}

	/**
	 * Add field type text in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean|string $required field is required
	 * @param string $class add special class like ucfirst, upper, lower, int, integer, float, number, money
	 * @param string $style css style to add
	 * @param string $after add string after field
	 * @param string $attributes add custom attributes in html input
	 * @param string $help help message
	 * @param string $value assign a value
	 */
	public function formAddFieldText($name, $label='', $required, $class='', $style='', $after='', $attributes='', $help='', $value='')
	{

		// type interception
        if(!$this->formFieldIsForbidden($name))
        {
            if($class == 'int' || $class == 'integer' || $class == 'number')
            {
                $style = 'width:60px; text-align:center; '.$style;
                if(is_bool($required))
                {
                    $required =  (!$required) ? 'onlyDigit' : 'notEmpty|onlyDigit';
                }
            }
            elseif($class == 'float' || $class == 'money')
            {
                $style = 'width:60px; text-align:center; '.$style;
                if(is_bool($required))
                {
                    $required =  (!$required) ? 'onlyDigit(,1)' : 'notEmpty|onlyDigit(,1)';
                }
            }
        }


		$options = array();
		$options['required'] = $required;
		if(!empty($class))$options['class'] = $class;
		if(!empty($style))$options['style'] = $style;
		if(!empty($after))$options['after'] = $after;
		if(!empty($attributes))$options['attributes'] = $attributes;
		if(!empty($help))$options['help'] = $help;
		if(strlen($value) > 0)$options['value'] = $value;

		$this->formAddField($name, $label, 'text', $required, $options);
	}


	/**
	 * Add field type color picker in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean|string $required field is required
	 * @param string $after add string after field
	 * @param string $attributes add custom attributes in html input
	 * @param string $help help message
	 * @param string $value assign a value
	 */
	public function formAddFieldColorPicker($name, $label='', $required, $after='', $attributes='', $help='', $value='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($after))$options['after'] = $after;
		if(!empty($attributes))$options['attributes'] = $attributes;
		if(!empty($help))$options['help'] = $help;
		if(strlen($value) > 0)$options['value'] = $value;

		$this->formAddField($name, $label, 'colorpicker', $required, $options);
	}

	/**
	 * Add field type hidden in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean|string $required field is required
	 */
	public function formAddFieldHidden($name, $label='', $required)
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		$this->formAddField($name, $label, 'hidden', $required, $options);
	}

	/**
	 * Add field type text with ajax autocompletion in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $ac_mode begins (default) or countains
	 * @param string $ac_column column to check
	 * @param int $ac_columnID column ID for autocompletion
	 * @param string $ac_table set table name
	 * @param string $ac_get get parameter name
	 * @param string $ac_sql_where add sql where clause
	 * @param string $ac_custom_sql set complete sql query place [q] keyword
	 * @param string $style css style to add
	 * @param string $after add string after field
	 * @param string $help help message
	 */
	public function formAddFieldTextAjaxAutoComplete($name, $label='', $required, $ac_mode='', $ac_column='', $ac_columnID='', $ac_table='', $ac_get='', $ac_sql_where='', $ac_custom_sql='', $style='', $after='', $help='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;

		if(!empty($ac_mode))$options['ac_mode'] = $ac_mode;
		if(!empty($ac_column))$options['ac_column'] = $ac_column;
		if(!empty($ac_columnID))$options['ac_columnID'] = $ac_columnID;
		if(!empty($ac_table))$options['ac_table'] = $ac_table;
		if(!empty($ac_get))$options['ac_table'] = $ac_table;
		if(!empty($ac_sql_where))$options['ac_sql_where'] = $ac_sql_where;
		if(!empty($ac_custom_sql))$options['ac_custom_sql'] = $ac_custom_sql;

		if(!empty($style))$options['style'] = $style;
		if(!empty($after))$options['after'] = $after;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'ajax_autocomplete', $required, $options);
	}


	/**
	 * Add field type file in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $path path to folder (no slash at end) if empty generate current plugin path
	 * @param string $url url to folder image (use path rewritten if omitted no slash at end)
	 * @param string $size maximum size in Ko or Mo (ex: 1 Mo)
	 * @param string $exts allowed image extension (ex: jpg,gif)
	 * @param string $mimes allowed mimes type
	 * @param string $virtual_url relative url for file if your folder is protected
	 * @param string $style css style to add
	 * @param string $after add string after field
	 * @param string $help help message
	 */
	public function formAddFieldFile($name, $label='', $required, $path='', $url='', $size='', $exts='', $mimes='', $virtual_url='', $style='', $after='', $help='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;

		if(!empty($path))$options['path'] = $path;
		if(!empty($url))$options['url'] = $url;
		if(!empty($size))$options['size'] = $size;
		if(!empty($exts))$options['exts'] = $exts;
		if(!empty($mimes))$options['mimes'] = $mimes;
		if(!empty($virtual_url))$options['virtual_url'] = $virtual_url;

		if(!empty($style))$options['style'] = $style;
		if(!empty($after))$options['after'] = $after;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'file', $required, $options);
	}


	/**
	 * Add field type image in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $path path to folder (no slash at end) if empty generate current plugin path
	 * @param string $url url to folder image (use path rewritten if omitted no slash at end)
	 * @param string $size maximum size in Ko or Mo (ex: 1 Mo)
	 * @param string $exts allowed image extension (ex: jpg,gif)
	 * @param string $mimes allowed mimes type
	 * @param string $virtual_url relative url for file if your folder is protected
	 * @param string $parent_suffix add a suffix to image
	 * @param boolean $parent_resize resize origina image
	 * @param int $parent_width image resize width
	 * @param int $parent_height image resize height
	 * @param boolean $parent_constraint form resize original to width and height
	 * @param array $parent_background_color apply a background color in rgb default white: array(255,255,255) only in constraint mode
	 * @param boolean $thumbnail_new create a thumbnail image
	 * @param int $thumbnail_width width of the thumbnail
	 * @param int $thumbnail_height height of the thumbnail
	 * @param boolean $thumbnail_constraint form resize thumbnail to width and height
	 * @param array $thumbnail_background_color apply a background color in rgb default white: array(255,255,255) only in constraint mode
	 * @param string $style css style to add
	 * @param string $after add string after field
	 * @param string $help help message
	 */
	public function formAddFieldImage($name, $label='', $required, $path='', $url='', $size='', $exts='', $mimes='', $virtual_url='',
									  $parent_suffix='', $parent_resize='', $parent_width='', $parent_height='', $parent_constraint='', $parent_background_color=array(),
									  $thumbnail_new='', $thumbnail_width='', $thumbnail_height='', $thumbnail_constraint='', $thumbnail_background_color=array(),
									  $style='', $after='', $help='')
	{
		$options = array();

		if(!empty($required))$options['required'] = $required;
		if(!empty($path))$options['path'] = $path;
		if(!empty($url))$options['url'] = $url;
		if(!empty($size))$options['size'] = $size;
		if(!empty($exts))$options['exts'] = $exts;
		if(!empty($mimes))$options['mimes'] = $mimes;
		if(!empty($virtual_url))$options['virtual_url'] = $virtual_url;

		if(!empty($parent_suffix))$options['parent_suffix'] = $parent_suffix;
		if(!empty($parent_resize))$options['parent_resize'] = $parent_resize;
		if(!empty($parent_width))$options['parent_width'] = $parent_width;
		if(!empty($parent_height))$options['parent_height'] = $parent_height;
		if(!empty($parent_constraint))$options['parent_constraint'] = $parent_constraint;
		if(!empty($parent_background_color))$options['parent_background_color'] = $parent_background_color;

		if(!empty($thumbnail_new))$options['thumbnail_new'] = $thumbnail_new;
		if(!empty($thumbnail_width))$options['thumbnail_width'] = $thumbnail_width;
		if(!empty($thumbnail_height))$options['thumbnail_height'] = $thumbnail_height;
		if(!empty($thumbnail_constraint))$options['thumbnail_constraint'] = $thumbnail_constraint;
		if(!empty($thumbnail_background_color))$options['thumbnail_background_color'] = $thumbnail_background_color;

		if(!empty($style))$options['style'] = $style;
		if(!empty($after))$options['after'] = $after;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'image', $required, $options);
	}


	/**
	 * Add field type htmlarea in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $style css style to add
	 * @param string $help help message
	 */
	public function formAddFieldHtmlArea($name, $label='', $required, $style='', $help='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($style))$options['style'] = $style;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'htmlarea', $required, $options);
	}

	/**
	 * Add field type file browser in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $folder default folder to open
	 * @param string $help help message
	 */
	public function formAddFieldFileBrowser($name, $label='', $required, $folder='', $help='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($folder))$options['folder'] = $folder;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'filemanager', $required, $options);
	}

	/**
	 * Add field type image browser in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $folder default folder to open
	 * @param string $help help message
	 */
	public function formAddFieldImageBrowser($name, $label='', $required, $folder='', $help='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($folder))$options['folder'] = $folder;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'filemanager_image', $required, $options);
	}


	/**
	 * Add field type media browser in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $folder default folder to open
	 * @param string $help help message
	 */
	public function formAddFieldMediaBrowser($name, $label='', $required, $folder='', $help='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($folder))$options['folder'] = $folder;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'filemanager_media', $required, $options);
	}

	/**
	 * Add field type boolean in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $help help message
	 */
	public function formAddFieldBoolean($name, $label='', $required, $help='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'boolean', $required, $options);
	}

	/**
	 * Add field type boolean inversed in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $help help message
	 */
	public function formAddFieldBooleanX($name, $label='', $required, $help='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'booleanX', $required, $options);
	}


	/**
	 * Add field type select in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param array $opts options
	 * @param string $style css style to add
	 * @param string $after add string after field
	 * @param string $attributes add custom attributes in html input
	 * @param string $help help message
	 */
	public function formAddFieldSelect($name, $label='', $required, $opts=array(), $style='', $after='', $attributes='', $help='')
	{
		$options = array();
		$options['options'] = $opts;
		if(!empty($required))$options['required'] = $required;
		if(!empty($style))$options['style'] = $style;
		if(!empty($after))$options['after'] = $after;
		if(!empty($attributes))$options['attributes'] = $attributes;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'select', $required, $options);
	}

	/**
	 * Add field type select with html options directly in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $opts options in html
	 * @param string $style css style to add
	 * @param string $after add string after field
	 * @param string $attributes add custom attributes in html input
	 * @param string $help help message
	 */
	public function formAddFieldSelectHtml($name, $label='', $required, $opts='', $style='', $after='', $attributes='', $help='')
	{
		$options = array();
		$options['options'] = $opts;
		if(!empty($required))$options['required'] = $required;
		if(!empty($style))$options['style'] = $style;
		if(!empty($after))$options['after'] = $after;
		if(!empty($attributes))$options['attributes'] = $attributes;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'select-html', $required, $options);
	}

	/**
	 * Add field type select multiple radio in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param array $opts options
	 * @param string $style css style to add
	 * @param string $after add string after field
	 * @param string $attributes add custom attributes in html input
	 * @param boolean $convert_checkbox_list convert select to a checkbox list
	 * @param string $help help message
	 */
	public function formAddFieldSelectMultiple($name, $label='', $required, $opts=array(), $style='', $after='', $attributes='', $convert_checkbox_list=false, $help='')
	{
		$options = array();

		$attributes .= ' multiple="" size="5" ';

		if($convert_checkbox_list)$options['class'] = 'checkbox-list';

		$options['options'] = $opts;
		if(!empty($required))$options['required'] = $required;
		if(!empty($style))$options['style'] = $style;
		if(!empty($after))$options['after'] = $after;
		if(!empty($attributes))$options['attributes'] = $attributes;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'select', $required, $options);
	}

	/**
	 * Add field type select sql in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $style css style to add
	 * @param string $field force field name if empty use $name
	 * @param string $table force table name if empty use $table
	 * @param string $where add where clause
	 * @param string $order_by force order_by clause
	 * @param string $after add string after field
	 * @param string $attributes add custom attributes in html input
	 * @param string $help help message
	 */
	public function formAddFieldSelectSql($name, $label='', $required, $field='', $table='', $where='', $order_by='',  $style='', $after='', $attributes='', $help='')
	{
		$options = array();

		if(!empty($required))$options['required'] = $required;
		if(!empty($field))$options['field'] = $field;
		if(!empty($table))$options['table'] = $table;
		if(!empty($where))$options['where'] = $where;
		if(!empty($order_by))$options['order_by'] = $order_by;
		if(!empty($style))$options['style'] = $style;
		if(!empty($after))$options['after'] = $after;
		if(!empty($attributes))$options['attributes'] = $attributes;
		if(!empty($help))$options['help'] = $help;

		$this->formAddField($name, $label, 'select-sql', $required, $options);
	}


	/**
	 * Add field type date in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $help help message
	 * @param string $value default value
	 */
	public function formAddFieldDate($name, $label='', $required, $help='', $value='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($help))$options['help'] = $help;
        if(strlen($value) > 0)$options['value'] = $value;

		$this->formAddField($name, $label, 'date', $required, $options);
	}


	/**
	 * Add field type datetime in form
	 *
	 * @param string $name db column name
	 * @param string $label label to display if empty $label = $name
	 * @param boolean $required field is required
	 * @param string $help help message
	 * @param string $value default value
	 */
	public function formAddFieldDateTime($name, $label='', $required, $help='', $value='')
	{
		$options = array();
		if(!empty($required))$options['required'] = $required;
		if(!empty($help))$options['help'] = $help;
        if(strlen($value) > 0)$options['value'] = $value;

		$this->formAddField($name, $label, 'datetime', $required, $options);
	}







	private $formErrors = array();
    /**
     * Add a custom error validation in form
     *
     * @param string $field
     * @param string $msg
     */
	public function formAddError($field, $msg)
	{
		$this->formErrors[] = array('field' => $field, 'msg' => $msg);
	}

    /**
     *
     * Add fieldset separator in field
     *
     * @param string $name
     * @param string $label if empty $label = $name
     * @param array $options html initialise html content
     */
	public function formAddFieldsetStart($name, $label='', $options=array())
	{
		$this->formAddField($name, $label, 'fieldset_start', false, $options);
	}
    /**
     * Close a form fieldset
     */
	public function formAddFieldsetEnd()
	{
		$this->formAddField('-', '', 'fieldset_end');
	}

	private $formJS = array();
    /**
     * Add javascript file at end of form
     *
     * @param string $file_name path of the file to merge
     */
	public function formMergeFile($file_name)
	{
		$this->formJS[] = $file_name;
	}

	private $formEndText;
    /**
     * Add text at end of the form
     *
     * @param string $text
     */
	public function formAddEndText($text)
	{
		$this->formEndText = $text;
	}



	/**
	 * Apply special format for the edit row
	 */
	private function formInitFormat()
	{

		// gmt to user conversion
		foreach($this->formFields as $f)
		{
			// hacks GMT
			if(($f['type'] == 'datetime' || $f['type'] == 'date') && preg_match("/GMT$/", $f['name']))
			{
				$this->edit_row[$f['name']] = nutsGetGMTDateUser($this->edit_row[$f['name']], '', 'user');
			}

			// hack date french
			if($f['type'] == 'date' && $_SESSION['Language'] == 'fr' && preg_match("#([0-9]{4})-([0-9]{2})-([0-9]{2})#", $this->edit_row[$f['name']]))
			{
				$this->edit_row[$f['name']] = $this->nuts->db2Date($this->edit_row[$f['name']]);
			}

			// hack datetime no second
			if($f['type'] == 'datetime' && $_SESSION['Language'] == 'fr' && preg_match("#([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})#", $this->edit_row[$f['name']]))
			{
				$this->edit_row[$f['name']] = $this->nuts->db2Date($this->edit_row[$f['name']]);
			}


			// special for ajax_autocomplete
			if($f['type'] == 'ajax_autocomplete')
			{
				// form init update form ajax
				$options = $f['opts'];

				if(!empty($options['ac_columnID']))
				{
					$v = $this->edit_row[$f['name']];
					$vs = explode(' (', $v);
					if(count($vs) >= 2)
						$v = (int)end($vs);
					$v = (int)$v;

					$sql = "SELECT
									CONCAT({$options['ac_column']},' (',{$options['ac_columnID']},')') AS val
							FROM
									{$options['ac_table']}
							WHERE
									Deleted = 'NO' AND
									{$options['ac_columnID']} = $v";
					$this->nuts->doQuery($sql);
					$this->edit_row[$f['name']] = $this->nuts->getOne();
				}
			}

		}
	}


    /**
     * Verify if field is forbidden
     *
     * @param $field_name
     * @return bool
     */
    public function formFieldIsForbidden($field_name)
    {
        // forbidden with joker
        $forbidden_field = false;
        foreach($this->formFieldsForbidden as $ff)
        {
            $joker = substr($ff, 0, -1);
            if($field_name == $ff || ($ff[strlen($ff)-1] == '*' && strpos($field_name, $joker) !== false))
            {
                $forbidden_field = true;
                break;
            }
        }

        return $forbidden_field;
    }



    /**
     * Validate and display a form
     * @return bool Valid
     */
	public function formValid()
	{
		global $nuts_lang_msg;

		// error detected ?
		if(defined('NUTS_ERROR_TMP'))
		{
			$this->errorRender();
			return false;
		}

		// controller
		if(!isset($_GET['ID']))$_GET['ID'] = 0;
		$_GET['ID'] = (int)$_GET['ID'];

		// copy
		if(!isset($_GET['cID']))$_GET['cID'] = 0;
		$_GET['cID'] = (int)$_GET['cID'];

		$this->nuts->open(WEBSITE_PATH.'/nuts/_templates/form.html');

		// percent treatment ?
		$this->nuts->parse('form_percent', $this->formPercentRender);

		// additional parameters
		$this->nuts->parse('formParameters', $this->formParameters);

		// end text
		$this->nuts->parse('end_text', $this->formEndText);

		// dynamic js
		if(file_exists(PLUGIN_PATH.'/form.js'))
			$this->formJS[] = PLUGIN_URL.'/form.js';
		$this->formJS = array_unique($this->formJS);
		if(count($this->formJS) == 0)
		{
			$this->nuts->eraseBloc('js');
		}
		else
		{
			foreach($this->formJS as $js)
			{
				$this->nuts->parse('js.js_file', $js);
				$this->nuts->loop('js');
			}
		}

		// js form valid
		if(!file_exists(PLUGIN_PATH.'/form_valid.js'))
			$this->nuts->eraseBloc('js_form_valid');


		// types
		$types = array('text', 'filemanager', 'filemanager_image', 'filemanager_media', 'hidden', 'date', 'datetime', 'textarea', 'select', 'image', 'file', 'colorpicker');
		foreach($this->formFields as $f)
		{
			if($f['type'] == 'fieldset_start' || $f['type'] == 'fieldset_end')
			{
				$this->nuts->eraseBloc('f.widgets');

				if($f['type'] == 'fieldset_start')
				{
					$this->nuts->parse('f.fieldset_start.label', $f['label']);
					$this->nuts->parse('f.fieldset_start.name', $f['name']);

					// help
					if(!isset($f['opts']['help']))$f['opts']['help'] = '';
					$this->nuts->parse('f.fieldset_start.help', $f['opts']['help']);


					// html
					if(!isset($f['opts']['html']))$f['opts']['html'] = '';
					$this->nuts->parse('f.fieldset_start.html', $f['opts']['html']);

					$this->nuts->eraseBloc('f.fieldset_end');
				}
				else
				{
					$this->nuts->eraseBloc('f.fieldset_start');
				}
			}
			else
			{
				$this->nuts->eraseBloc('f.fieldset_start');
				$this->nuts->eraseBloc('f.fieldset_end');

				$exclude = '';

				//$this->nuts->parse('f.widgets.label', $f['label']);

				// help
				if(!isset($f['opts']['help']))$f['opts']['help'] = '';
				$this->nuts->parse('f.widgets.help', $f['opts']['help']);

				// required
                if((is_bool($f['required']) && !$f['required']) || (is_string($f['required']) && !preg_match('/notEmpty/i', $f['required'])) || $f['type'] == 'hidden')
                    $this->nuts->eraseBloc('f.widgets.required');

				// class
				if(!isset($f['opts']['class']))$f['opts']['class'] = '';

				// after
				if(!isset($f['opts']['after']))$f['opts']['after'] = '';

				$this->nuts->parse('f.widgets.label', $f['label']);


				// text && hidden ***************************************************************************************
				if($f['type'] == 'colorpicker' || $f['type'] == 'filemanager' || $f['type'] == 'filemanager_image' || $f['type'] == 'filemanager_media' || $f['type'] == 'text' || $f['type'] == 'hidden' || $f['type'] == 'date' || $f['type'] == 'datetime' || $f['type'] == 'ajax_autocomplete')
				{
					if($f['type'] == 'ajax_autocomplete')$f['type'] = 'text';

					$exclude = $f['type'];

					$this->nuts->parse('f.widgets.'.$f['type'].'.name', $f['name']);
					$val = (isset($f['opts']['value'])) ? $f['opts']['value'] : '';
					$this->nuts->parse('f.widgets.'.$f['type'].'.value', $val);

					$style = (isset($f['opts']['style'])) ? $f['opts']['style'] : '';
					$this->nuts->parse('f.widgets.'.$f['type'].'.style', $style);
					$this->nuts->parse('f.widgets.'.$f['type'].'.class', $f['opts']['class']);
					$this->nuts->parse('f.widgets.'.$f['type'].'.after', $f['opts']['after']);

					// atributes
					$attributes = '';
					if(isset($f['opts']['attributes']))$attributes .= $f['opts']['attributes'];
					$this->nuts->parse('f.widgets.'.$f['type'].'.attributes', $attributes);

					// special filemanager: folder
					if(in_array($f['type'], array('filemanager', 'filemanager_image', 'filemanager_media')))
					{
						$folder = (!isset($f['opts']['folder'])) ? '' :  $f['opts']['folder'];
						$this->nuts->parse('f.widgets.'.$f['type'].'.folder', $folder);
					}

				}

				// image **************************************************************************************************
				if($f['type'] == 'image')
				{
					$exclude = $f['type'];

					$this->nuts->parse('f.widgets.image.name', $f['name']);

					$style = (isset($f['opts']['style'])) ? $f['opts']['style'] : '';
					$this->nuts->parse('f.widgets.image.style', $style);

					$this->nuts->parse('f.widgets.'.$f['type'].'.class', $f['opts']['class']);
					$this->nuts->parse('f.widgets.'.$f['type'].'.after', $f['opts']['after']);

					// atributes
					$attributes = '';
					if(isset($f['opts']['attributes']))$attributes .= $f['opts']['attributes'];
					$this->nuts->parse('f.widgets.image.attributes', $attributes);

					// no image
					$image_display = '';
					if($_GET['ID'] == 0)
					{
						$image_display = 'none';
                        $this->nuts->parse('f.widgets.image.image_path', '');
                        $this->nuts->parse('f.widgets.image.image_name', 'nuts/img/NO.gif');
                        $this->nuts->parse('f.widgets.image.time', '');
					}
					else
					{
						$image_display = 'none';

						$sql = "SELECT {$f['name']}Image FROM {$this->formDBTable[0]} WHERE ID = {$_GET['ID']}";
						$this->nuts->doQuery($sql);

						if(!$this->nuts->dbNumRows())
                        {
                            $this->nuts->parse('f.widgets.image.image_path', '');
                            $this->nuts->parse('f.widgets.image.image_name', 'nuts/img/NO.gif');
                            $this->nuts->parse('f.widgets.image.time', '');
                        }
                        else
						{
							$img = $this->nuts->getOne();
							if(!empty($img))
							{
								$image_display = '';
								$this->nuts->parse('f.widgets.image.image_path', $f['opts']['url']);
								$this->nuts->parse('f.widgets.image.image_name', $img);
								$this->nuts->parse('f.widgets.image.time', time());
							}
						}
					}

					$this->nuts->parse('f.widgets.image.image_display', $image_display);

				}
				// file **************************************************************************************************
				if($f['type'] == 'file')
				{
					$exclude = $f['type'];

					$this->nuts->parse('f.widgets.file.name', $f['name']);

					$style = (isset($f['opts']['style'])) ? $f['opts']['style'] : '';
					$this->nuts->parse('f.widgets.file.style', $style);

					$this->nuts->parse('f.widgets.'.$f['type'].'.class', $f['opts']['class']);
					$this->nuts->parse('f.widgets.'.$f['type'].'.after', $f['opts']['after']);

					// atributes
					$attributes = '';
					if(isset($f['opts']['attributes']))$attributes .= $f['opts']['attributes'];
					$this->nuts->parse('f.widgets.file.attributes', $attributes);

					// no image
					$file_display = '';
                    $file_image_extension = 'file';
					if($_GET['ID'] == 0)
					{
						$file_display = 'none';
					}
					else
					{
						$file_display = 'none';

						$sql = "SELECT {$f['name']}File FROM {$this->formDBTable[0]} WHERE ID = {$_GET['ID']}";
						$this->nuts->doQuery($sql);

						if($this->nuts->dbNumRows() == 1)
						{
							$file = $this->nuts->getOne();
							if(!empty($file))
							{
								$file_display = '';

								if(isset($f['opts']['virtual_url']))
								{
									$this->nuts->parse('f.widgets.file.file_path', "");
									$this->nuts->parse('f.widgets.file.file_name', $f['opts']['virtual_url']);


								}
								else
								{
									$this->nuts->parse('f.widgets.file.file_path', $f['opts']['url']);
									$this->nuts->parse('f.widgets.file.file_name', $file);
								}

                                // extension image
                                $ext = strtolower(end(explode('.', $file)));
                                $ext2 = (strlen($ext) >= 4) ? substr($ext, 0, 3) : $ext;
                                if(file_exists(WEBSITE_PATH."/nuts/img/icon_extension/$ext.png"))
                                    $file_image_extension = $ext;
                                elseif(file_exists(WEBSITE_PATH."/nuts/img/icon_extension/$ext2.png"))
                                    $file_image_extension = $ext2;

							}
						}
					}

					$this->nuts->parse('f.widgets.file.file_display', $file_display);
					$this->nuts->parse('f.widgets.file.file_image_extension', $file_image_extension);
				}

				// textarea *******************************************************************************************
				if($f['type'] == 'textarea' || $f['type'] == 'htmlarea')
				{
					$exclude = 'textarea';

					$this->nuts->parse('f.widgets.textarea.name', $f['name']);
					$val = (isset($f['opts']['value'])) ? $f['opts']['value'] : '';
					$this->nuts->parse('f.widgets.textarea.value', $val);

					$style = (isset($f['opts']['style'])) ? $f['opts']['style'] : '';
					$class = (isset($f['opts']['class'])) ? $f['opts']['class'] : '';

					$this->nuts->parse('f.widgets.textarea.style', $style);

					// resizable
					if(in_array($class, array('php', 'sql', 'html')))
					{
						// $class = 'processed codepress '.$class.' autocomplete-off';
						$class = 'resizable';
					}
					else
					{
						if($f['type'] == 'htmlarea')
							$class = 'mceEditor';
						elseif(empty($class))
							$class = 'resizable';
					}
					$this->nuts->parse('f.widgets.textarea.class', $class);
					$this->nuts->parse('f.widgets.textarea.after', $f['opts']['after']);


					// atributes
					$attributes = '';
					if(isset($f['opts']['attributes']))$attributes .= $f['opts']['attributes'];
					$this->nuts->parse('f.widgets.textarea.attributes', $attributes);
				}

				// select **********************************************************************************************
				if($f['type'] == 'select' || $f['type'] == 'select-html' || $f['type'] == 'select-sql' || $f['type'] == 'boolean' || $f['type'] == 'booleanX')
				{
					$exclude = 'select';

					$this->nuts->parse('f.widgets.select.name', $f['name']);
					$this->nuts->parse('f.widgets.select.nameID', str_replace('[]', '', $f['name']));

					// atributes
					$attributes = '';
					if(isset($f['opts']['attributes']))$attributes .= $f['opts']['attributes'];
					$this->nuts->parse('f.widgets.select.attributes', $attributes);

					$style = (isset($f['opts']['style'])) ? $f['opts']['style'] : '';
					$this->nuts->parse('f.widgets.select.style', $style);

					$this->nuts->parse('f.widgets.select.class', $f['opts']['class']);
					$this->nuts->parse('f.widgets.select.after', $f['opts']['after']);

					if($f['type'] == 'select-sql')
					{
						$table = (!isset($f['opts']['table'])) ? str_replace('ID', '', $f['name']) : $f['opts']['table'];
						if(!isset($f['opts']['field']))$f['opts']['field'] = 'Name';

						if(!isset($f['opts']['where']))$f['opts']['where'] = '';
						if(!empty($f['opts']['where']))$f['opts']['where'] = ' AND '.$f['opts']['where'];

						$sql = "SELECT
										ID AS value,
										{$f['opts']['field']} AS label
								FROM
										$table
								WHERE
										Deleted = 'NO'
										{$f['opts']['where']}
								ORDER BY
										{$f['opts']['field']}";

						$this->nuts->doQuery($sql);
						$f['opts']['options'] = array();

						while($row = $this->nuts->dbFetch())
						{
							$f['opts']['options'][] = $row;
						}
					}
					elseif($f['type'] == 'boolean')
					{
						$f['opts']['options'][] = array('label' => $nuts_lang_msg[30], 'value' => 'YES');
						$f['opts']['options'][] = array('label' => $nuts_lang_msg[31], 'value' => 'NO');
					}
					elseif($f['type'] == 'booleanX')
					{
						$f['opts']['options'][] = array('label' => $nuts_lang_msg[31], 'value' => 'NO');
						$f['opts']['options'][] = array('label' => $nuts_lang_msg[30], 'value' => 'YES');
					}
					elseif($f['type'] == 'select-html')
					{

					}

					if(!is_array($f['opts']['options']))
					{
						$this->nuts->parseBloc('f.widgets.select.options', $f['opts']['options']);
					}
					else
					{
						if(count($f['opts']['options']) == 0)
						{
							$this->nuts->eraseBloc('f.widgets.select.options');
						}
						else
						{

							//if(!$f['required'] || $f['type'] == 'select-sql')
							if($f['type'] == 'select-sql')
								array_unshift($f['opts']['options'], array('label' => '', 'value' => ''));

							foreach($f['opts']['options'] as $zopt)
							{
								$opt = array();
								if(is_string($zopt))
								{
									$opt['value'] = strtoupper($zopt);
									$opt['label'] = ucfirst(strtolower($zopt));
								}
								else
								{
									$opt['value'] = (!isset($zopt['value'])) ? $zopt : $zopt['value'];
									$opt['label'] = (!isset($zopt['value'])) ? $zopt : $zopt['label'];
								}

								$this->nuts->parse('f.widgets.select.options.label', $opt['label']);
								$this->nuts->parse('f.widgets.select.options.value', $opt['value']);
								$this->nuts->loop('f.widgets.select.options');
							}
						}
					}
				}

				// erase other type && loop
				foreach($types as $t)
				{
					if($t != $exclude)$this->nuts->eraseBloc('f.widgets.'.$t);
					$this->nuts->loop('f.widgets.'.$t);
				}
			}

			$this->nuts->loop('f.fieldset_start');
			$this->nuts->loop('f.fieldset_end');
			$this->nuts->loop('f.widgets');
			$this->nuts->loop('f');
		}

		$out = $this->nuts->output();

		$out = str_replace('{ ', '{', $out);
		$out = str_replace('{<', '<', $out);
		$out = str_replace(' }', '}', $out);
		$out = str_replace('>}', '>', $out);

		$out = str_replace('< bloc::', '<bloc::', $out);
		$out = str_replace('< /bloc::', '</bloc::', $out);

		// validation
		$this->nuts->formSetDisplayMode('T');
		$this->nuts->formSetName('former'); // specify a form name
		$this->nuts->createVirtualTemplate($out);

		// change display names
		$form_fields = array();
		foreach($this->formFields as $f)
			$form_fields[$f['name']] = $f['label'];
		$form_fields_str = json_encode($form_fields);

		if(count($form_fields) > 0)
			$this->nuts->formSetObjectNames($form_fields);

		// initialization
		if(!$_GET['ID'] && $_GET['cID'])
		{
			$sql = "SELECT * FROM {$this->formDBTable[0]} WHERE ID = {$_GET['cID']} AND Deleted = 'NO'";
			$this->nuts->doQuery($sql);
			if($this->nuts->dbNumRows() == 1)
				$this->edit_row = $this->nuts->dbFetch();
		}

		if(count($this->edit_row) > 0)
		{
			$this->formInitFormat();
			$this->nuts->formInit($this->edit_row);
		}

		foreach($this->formFields as $f)
		{
			// special for aucomplete
			if($f['type'] == 'ajax_autcomplete' && $_POST && !empty($f['opts']['ac_columnID']))
			{
				$tmp = explode(' (', $_POST[$f['name']]);
				$_POST[$f['name']] = end($tmp);
				$_POST[$f['name']] = (int)$_POST[$f['name']];
				if($_POST[$f['name']] == 0)$_POST[$f['name']] = '';
			}

            // add special class
            if(!@empty($f['opts']['class']) && $_POST && isset($_POST[$f['name']]))
            {
                if(preg_match('/upper/', $f['opts']['class']))$_POST[$f['name']] = mb_strtoupper($_POST[$f['name']], 'UTF-8');
                if(preg_match('/lower/', $f['opts']['class']))$_POST[$f['name']] = mb_strtolower($_POST[$f['name']], 'UTF-8');
                if(preg_match('/ucfirst/', $f['opts']['class']))$_POST[$f['name']] = @mb_strtoupper(@mb_substr($_POST[$f['name']], 0, 1, 'UTF-8')).@mb_substr($_POST[$f['name']], 1);
            }

			if($f['required'])
			{
				if(is_bool($f['required']))
				{
                    if(in_array($f['type'], array('image', 'file')))
                    {

                    }
                    else
                    {
                        $this->nuts->notEmpty($f['name']);
                    }
				}
				else
				{
					$rules = explode('|', $f['required']);

					foreach($rules as $rule)
					{
						$rule = str_replace(array('(', ')'), '', $rule);

						// unique in database login
						if($rule == 'unique' && $_POST)
						{
							$sql = "SELECT * FROM {$this->formDBTable[0]} WHERE {$f['name']} = '%s'";
							if(!isset($_GET['ID']))$_GET['ID'] = 0;
							$_GET['ID'] = (int)$_GET['ID'];
							$sql .= " AND ID != {$_GET['ID']}";

							$this->nuts->dbSelect($sql, array($_POST[$f['name']]));
							$res = $this->nuts->dbNumRows();
							if($res != 0)
							{
								$msg = sprintf($nuts_lang_msg[35], $f['name']);
								$this->nuts->addError($f['name'], $msg);
							}
						}

						// minLength
						elseif(preg_match('/minLength/i', $rule))
						{
							$params = explode(',', $rule);
							$this->nuts->minLength($f['name'], $params[1]);
						}

						// onlydigit
						elseif(preg_match('/onlyDigit/i', $rule))
						{
							$params = explode(',', $rule);
							$this->nuts->onlyDigit($f['name'], @$params[1]);
						}

						// alphaNumeric
						elseif(preg_match('/alphaNumeric/i', $rule))
						{
							$params = explode(',', $rule);
							$this->nuts->alphaNumeric($f['name'], @$params[1]);
						}

						// notEmpty
						elseif(preg_match('/notEmpty/i', $rule))
						{
							$this->nuts->notEmpty($f['name']);
						}
					}
				}
			}

			// special types
			$file_force_required = false;

			if(($f['type'] == 'image' || $f['type'] == 'file') && $f['required'])
			{
				// force required for add or edit
				if($_GET['ID'] == 0)
					$file_force_required = true;
				else
				{
					$checkbox_name = $f['name'].ucfirst($f['type']).'Delete';
					if(isset($_POST[$checkbox_name]) && $_POST[$checkbox_name] == 1)
					{
						$file_force_required = true;
					}
				}
			}

			if($f['type'] == 'image')
			{
				if(!isset($f['opts']['size']))$f['opts']['size'] = '';
				if(!isset($f['opts']['mimes']))$f['opts']['mimes'] = 'image/jpeg, image/gif, image/png';
				if(!isset($f['opts']['exts']))$f['opts']['exts'] = 'jpg, gif, png';

				$this->nuts->fileControl($f['name'], $file_force_required, $f['opts']['size'], $f['opts']['mimes'], $f['opts']['exts']);
			}
			elseif($f['type'] == 'file')
			{
				if(!isset($f['opts']['size']))$f['opts']['size'] = '';
				if(!isset($f['opts']['mimes']))$f['opts']['mimes'] = '';
				if(!isset($f['opts']['exts']))$f['opts']['exts'] = '';

				$this->nuts->fileControl($f['name'], $file_force_required, $f['opts']['size'], $f['opts']['mimes'], $f['opts']['exts']);
			}
			elseif($f['type'] == 'date')
			{
				$format = ($_SESSION['Language'] == 'fr') ? 'd/m/Y' : 'Y-m-d';
				$this->nuts->date($f['name'], $format);
			}
			elseif($f['type'] == 'datetime')
			{
				$format = ($_SESSION['Language'] == 'fr') ? 'd/m/Y H:i' : 'Y-m-d H:i';
				$this->nuts->date($f['name'], $format);
			}

			// verify folder rights
			if($f['type'] == 'image' || $f['type'] == 'file')
			{
                if(!is_dir($f['opts']['path']))@mkdir($f['opts']['path']);

				if(!is_writable($f['opts']['path']))
				{
					$tmp = str_replace(WEBSITE_PATH, '', $f['opts']['path']);
					$this->nuts->addError($f['name'], "Folder not writable `$tmp`");
				}
			}
		}

		// add lock error if someone else update the record
		if($_POST && $_GET['ID'] != 0)
		{
			// 2 minutes lock
			$sql = "SELECT
							NutsUserID
					FROM
							NutsLog
					WHERE
							Application = '{$this->name}' AND
							RecordID = {$_GET['ID']} AND
							NutsUserID != {$_SESSION['NutsUserID']} AND
							DateGMT >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 MINUTE)
					ORDER BY
							DateGMT DESC
					LIMIT 1";
			$this->nuts->doQuery($sql);
			if($this->nuts->dbNumRows() == 1)
			{
				$nutsUserAuthorID = $this->nuts->getOne();
				$this->nuts->doQuery("SELECT FirstName, LastName FROM NutsUser WHERE ID = {$nutsUserAuthorID}");

				$r = $this->nuts->dbFetch();
				$user_name = $r['FirstName'].' '.$r['LastName'];
				$this->nuts->addError('', sprintf($nuts_lang_msg[54], $user_name));
			}
		}


		// special errors
		if(count($this->formErrors) > 0)
		{
			foreach($this->formErrors as $err)
			{
				$this->nuts->addError($err['field'], $err['msg']);
			}
		}

		$result = $this->nuts->formIsValid();

		// protect data mode
		if($_POST)
		{
			// forbidden special instruction
			foreach($_POST as $key => $val)
			{
				$val = str_replace('{#', '{ #', $val);
				$val = str_replace('{CONST::', '{ CONST::', $val);
				$_POST[$key] = $val;
			}

			// smart copy for htmlarea
			foreach($this->formFields as $f)
			{
				if($f['type'] == 'htmlarea' && isset($_POST[$f['name']]))
					$_POST[$f['name']] = smartImageResizer($_POST[$f['name']]);
			}
		}


		$msg = ($this->action == 'add') ? $nuts_lang_msg[24] : $nuts_lang_msg[25];
		$this->nuts->parse('form_fields', $form_fields_str);
		$this->nuts->parse('msg', $msg);
		$this->render = $this->nuts->output();

		// datetime auto conversion, " text if form is valid
		if($_POST && $result)
		{
			foreach($this->formFields as $f)
			{
				// auto add conversion for french format date
				if($f['type'] == "date" && preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#i", $_POST[$f['name']]))
				{
					list($d, $m, $Y) = explode('/', $_POST[$f['name']]);
					$_POST[$f['name']] = "$Y-$m-$d";
				}

				// auto add conversion for french format datetime
				if($f['type'] == "datetime" && preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#i", $_POST[$f['name']]))
				{
					list($d, $m, $Y) = explode('/', $_POST[$f['name']]);
					list($Y, $time) = explode(' ', $Y);
					$_POST[$f['name']] = "$Y-$m-$d $time";
				}


				// add seconds if missing XXXX-XX-XX XX:XX
				if($f['type'] == 'datetime' && strlen($_POST[$f['name']]) == 16)
				{
					$_POST[$f['name']] .= ':00';
				}

				if(($f['type'] == 'datetime' || $f['type'] == 'date') && preg_match("/GMT$/", $f['name']) && !empty($_POST[$f['name']]))
				{
					$_POST[$f['name']] = nutsGetGMTDateUser($_POST[$f['name']], '', 'gmt');
				}

                // transform " by `
                if($f['type'] == "text")
                {
                    if(isset($_POST[$f['name']]))
                        $_POST[$f['name']] = str_replace('"', '`', $_POST[$f['name']]);
                }


			}
		}


		// remove percent protection
		if(!$_POST && $this->formPercentRender && @$_GET['form_ajax_treatment'] != 1)
		{
			$sql = "DELETE FROM NutsTreatmentPercent WHERE NutsUserID = {$_SESSION['NutsUserID']} AND Plugin = '{$this->name}'";
			$this->nuts->doQuery($sql);
			$_SESSION['FormPercentParams'] = array();

		}


		return $result;
	}


	private $formInitRes = array();

	/**
	 *
	 * @param array $arr array for form initialization
	 */
	public function formInitAdd($arr)
	{
		$this->formInitRes = $arr;
	}


	private $formDBTable = array();
    /**
     * Select a database table to map
     * @param array $formDBTable
     */
	public function formDbTable($formDBTable)
	{
		if(!isset($_GET['ID']))$_GET['ID'] = 0;
		$_GET['ID'] = (int)$_GET['ID'];

		$this->formDBTable = $formDBTable;
	}
    /**
     * Insert the record in the defined table
	 * @param int ID
	 * @return lastInsertID
     */
	public function formInsert()
	{
		$excepts = array('btn_submit');
		foreach($this->formFields as $f)
		{
			if($f['type'] == 'image' || $f['type'] == 'file')
			{
				$excepts[] = $f['name'];

				if($f['type'] == 'image')
					$excepts[] = $f['name'].'ImageDelete';
				elseif($f['type'] == 'file')
					$excepts[] = $f['name'].'FileDelete';
			}
		}

		foreach($this->formFieldsForbidden as $ex)
            $excepts[] = $ex;

		$this->nuts->dbInsert($this->formDBTable[0], $_POST, $excepts);
		$lastID = $this->nuts->getMaxID($this->formDBTable[0], 'ID');

		$this->formLogDbColumns('INSERT', $lastID);

		$this->trace('add', $lastID);

		// uploads ?
		$this->uploadImages($lastID);

		// add curID
		$input = '<input type="hidden" id="NUTS_REC_CUR_ID" value="'.$lastID.'">';
		$this->render = str_replace('<div id="form">', '<div id="form">'.$input, $this->render);


		// percent treatment -> initialisation ?
		if($this->formPercentRender)
		{
			$f = array();
			$f['NutsUserID'] = $_SESSION['NutsUserID'];
			$f['Plugin'] = $this->name;
			$f['Start'] = 0;
			$f['End'] = (int)$this->formPercentRenderEndValue;
			$f['RecordID'] = $lastID;
			$f['POST'] = base64_encode(serialize($_POST));
			$this->nuts->dbInsert('NutsTreatmentPercent', $f);

			$_SESSION['FormPercentRecordID'] = $lastID;
			$_SESSION['FormPercentParams'] = array(0, $f['End']);
		}

		return $lastID;

	}

	/**
	 * Generate insert and log columns
	 */
	private function formLogDbColumns($act, $ID)
	{
		if($act == 'DELETE')$this->formDBTable[0] = $this->deleteDbTable[0];

		$this->nuts->doQuery("SHOW COLUMNS FROM `{$this->formDBTable[0]}`");

		if($act == 'INSERT')$name = 'Create';
		elseif($act == 'UPDATE')$name = 'Update';
		elseif($act == 'DELETE')$name = 'Delete';

		$LogActionNutsUserID = false;
		$LogActionNutsGroupID = false;
		$LogActionDateGMT = false;

		while($r = $this->nuts->dbFetch())
		{
			// LogActionCreateNutsUserID
			if($r['Field'] == 'LogAction'.$name.'NutsUserID')
				$LogActionNutsUserID = true;

			// LogActionCreateNutsGroupID
			if($r['Field'] == 'LogAction'.$name.'NutsGroupID')
				$LogActionNutsGroupID = true;

			// LogActionCreateDateGMT
			if($r['Field'] == 'LogAction'.$name.'DateGMT')
				$LogActionDateGMT = true;
		}

		// log action
		if(!$LogActionNutsUserID)$this->formLogGeneratorAddDbColumn('LogAction'.$name.'NutsUserID');
		if(!$LogActionNutsGroupID)$this->formLogGeneratorAddDbColumn('LogAction'.$name.'NutsGroupID');
		if(!$LogActionDateGMT)$this->formLogGeneratorAddDbColumn('LogAction'.$name.'DateGMT');

		// log in database
		$this->nuts->dbUpdate($this->formDBTable[0], array(
															'LogAction'.$name.'NutsUserID' => $_SESSION['NutsUserID'],
															'LogAction'.$name.'NutsGroupID' => $_SESSION['NutsGroupID'],
															'LogAction'.$name.'DateGMT' => gmdate('Y-m-d H:i:s')
														   ), "ID = $ID");
	}

	/**
	 *  Add column to db directly
	 */
	private function formLogGeneratorAddDbColumn($name)
	{
		// ID
		if(preg_match('/ID$/', $name))
			$sql = "ALTER TABLE `{$this->formDBTable[0]}` ADD $name INT NOT NULL";

		// DateGMT
		else
			$sql = "ALTER TABLE `{$this->formDBTable[0]}` ADD $name DATETIME NOT NULL";
		$this->nuts->doQuery($sql);

		// generate index
		// $this->nuts->doQuery("ALTER TABLE `{$this->formDBTable[0]}` ADD INDEX `$name` (`$name`)");
	}


	/**
     * Upload Image in defined path
     * @param int $lastID
     */
	private function uploadImages($lastID)
	{
		foreach($this->formFields as $f)
		{
			if(!$this->formFieldIsForbidden($f['name']) && ($f['type'] == 'image' || $f['type'] == 'file') && isset($_FILES[$f['name']]) && !$_FILES[$f['name']]['error'])
			{
				$exts = explode('.', $_FILES[$f['name']]['name']);

				if(!isset($f['opts']['parent_suffix']))$f['opts']['parent_suffix'] = '';

				$f_name = $lastID.$f['opts']['parent_suffix'].'.'.strtolower($exts[count($exts)-1]);
				move_uploaded_file($_FILES[$f['name']]['tmp_name'], $f['opts']['path'].'/'.$f_name);

				if($f['type'] == 'image')
					$this->nuts->dbUpdate($this->formDBTable[0], array("{$f['name']}Image" => $f_name), "ID=$lastID");
				else
					$this->nuts->dbUpdate($this->formDBTable[0], array("{$f['name']}File" => $f_name), "ID=$lastID");

				$this->trace("upload file `{$_FILES[$f['name']]['name']}`", $lastID);

				// parent resize
				if(!isset($f['opts']['parent_resize']))$f['opts']['parent_resize'] = false;
				if(!isset($f['opts']['parent_width']))$f['opts']['parent_width'] = '';
				if(!isset($f['opts']['parent_height']))$f['opts']['parent_height'] = '';
				if(!isset($f['opts']['parent_constraint']))$f['opts']['parent_constraint'] = false;
				if(!isset($f['opts']['parent_background_color']))$f['opts']['parent_background_color'] = array(255, 255, 255);

				if($f['opts']['parent_resize'] && (!empty($f['opts']['parent_width']) || !empty($f['opts']['parent_height'])))
				{
					$this->nuts->imgThumbnailSetOriginal($f['opts']['path'].'/'.$f_name);
					$this->nuts->imgThumbnail($f['opts']['parent_width'],
											  $f['opts']['parent_height'],
											  $f['opts']['parent_constraint'],
											  $f['opts']['parent_background_color']);

					$this->trace("resize parent file `{$_FILES[$f['name']]['name']}`", $lastID);
				}

				// thumbnails ?
				if(isset($f['opts']['thumbnail_with']) && !isset($f['opts']['thumbnail_width']))
					$f['opts']['thumbnail_width'] = $f['opts']['thumbnail_with'];


				if(!isset($f['opts']['thumbnail_height']))$f['opts']['thumbnail_height'] = '';
				if(!isset($f['opts']['thumbnail_constraint']))$f['opts']['thumbnail_constraint'] = false;
				if(!isset($f['opts']['thumbnail_background_color']))$f['opts']['thumbnail_background_color'] = array(255, 255, 255);

				if(!empty($f['opts']['thumbnail_width']) || !empty($f['opts']['thumbnail_height']))
				{
					if(!isset($f['opts']['thumbnail_new']))$f['opts']['thumbnail_new'] = false;
					// createThumb($f['opts']['path'].'/'.$f_name, $f['opts']['thumbnail_width'], $f['opts']['thumbnail_new']);

					// copy original to thumb_
					if(!$f['opts']['thumbnail_new'])
					{
						$this->nuts->imgThumbnailSetOriginal($f['opts']['path'].'/'.$f_name);
					}
					else
					{
						copy($f['opts']['path'].'/'.$f_name, $f['opts']['path'].'/thumb_'.$f_name);
						$this->nuts->imgThumbnailSetOriginal($f['opts']['path'].'/thumb_'.$f_name);
					}

					$this->nuts->imgThumbnail($f['opts']['thumbnail_width'],
											  $f['opts']['thumbnail_height'],
											  $f['opts']['thumbnail_constraint'],
											  $f['opts']['thumbnail_background_color']);

					$this->trace("thumbnail file `{$_FILES[$f['name']]['name']}`", $lastID);
				}
			}
		}
	}


	/**
	 * Array form exception, you can use also formAddException()
	 * @var array
	 */
	public $formFieldsForbidden = array();

    /**
     * add a Form field forbidden name put * to enclose generic (example Field*)
     *
     * @param string $name
     */
    public function formAddException($name)
    {
        $this->formFieldsForbidden[] = $name;
    }


	/**
	 * Execute Update record treatment in defined table
	 *
	 * @return ID
	 */
	public function formUpdate()
	{
		$excepts = array('btn_submit');
		foreach($this->formFields as $f)
		{
			if($f['type'] == 'image' || $f['type'] == 'file')
			{
				$excepts[] = $f['name'];

				if($f['type'] == 'image')
					$excepts[] = $f['name'].'ImageDelete';
				elseif($f['type'] == 'file')
					$excepts[] = $f['name'].'FileDelete';
			}
		}

        foreach($this->formFieldsForbidden as $ex)
            $excepts[] = $ex;


		$this->nuts->dbUpdate($this->formDBTable[0], $_POST, "ID={$_GET['ID']}", $excepts);
		$this->trace('update', $_GET['ID']);

		$this->formLogDbColumns('UPDATE', $_GET['ID']);

		foreach($this->formFields as $f)
		{
			// delete file
			if(
					($f['type'] == 'image' && isset($_POST[$f['name'].'ImageDelete']) && $_POST[$f['name'].'ImageDelete'] == '1') ||
					($f['type'] == 'file' && isset($_POST[$f['name'].'FileDelete']) && $_POST[$f['name'].'FileDelete'] == '1')
			)
			{
				if($f['type'] == 'image')
					$sql = "SELECT {$f['name']}Image FROM {$this->formDBTable[0]} WHERE ID = {$_GET['ID']}";
				elseif($f['type'] == 'file')
					$sql = "SELECT {$f['name']}File FROM {$this->formDBTable[0]} WHERE ID = {$_GET['ID']}";

				$this->nuts->doQuery($sql);
				$f_name = $this->nuts->getOne();
				@unlink($f['opts']['path'].'/'.$f_name);

				if($f['type'] == 'image')
					$this->nuts->doQuery("UPDATE {$this->formDBTable[0]} SET {$f['name']}Image = '' WHERE ID = {$_GET['ID']}");
			    elseif($f['type'] == 'file')
					$this->nuts->doQuery("UPDATE {$this->formDBTable[0]} SET {$f['name']}File = '' WHERE ID = {$_GET['ID']}");

				$this->trace("delete file", $_GET['ID']);
			}
		}


		// upload file
		$this->uploadImages($_GET['ID']);


		return $_GET['ID'];
	}


	private $edit_row = array();


	/**
	 * Return initial row generated, use formInit instead !
	 * @deprecated true
	 */
	public function formInitGetRow()
	{
		return $this->edit_row;
	}

	/**
	 * Assign initial row generated
	 */
	public function formInitSetRow($r)
	{
		$this->edit_row = $r;
	}

    /**
     * Form initialization for a record ID
	 *
	 * @param string $sql_added
	 * @return array edit records
     */
	public function formInit($sql_added='')
	{
		if(!isset($_GET['ID']))$_GET['ID'] = 0;
		$_GET['ID'] = (int)$_GET['ID'];

		// record verification !
		if(!$this->isRecordExists($this->formDBTable[0]))
		{
			$msg = "Record {$_GET['ID']} doesn't exist";
			$this->trace($msg, $_GET['ID']);
			$this->setError($msg);
		}
		else
		{
			$row = $this->getData("SELECT * $sql_added FROM {$this->formDBTable[0]} WHERE ID = {$_GET['ID']}");
			$this->edit_row = $row[0];

			foreach($this->formInitRes as $key => $val)
				$this->edit_row[$key] = $val;

			$this->formInitFormat();

		}

		return $this->edit_row;

	}

	/**
	 * form percent last POST
	 *
	 * @var array
	 */
	private $formPercentPOST = array();


	/**
	 * Verify if form is rendering access allowed
	 *
	 * @return boolean
	 */
	public function formPercentRenderExecute()
	{
		if($this->formPercentRender && @$_GET['form_ajax_treatment'] == 1 && isset($_SESSION['FormPercentRecordID']))
		{
			// verify if record exists
			$sql = "SELECT POST, Start, End FROM NutsTreatmentPercent WHERE NutsUserID = {$_SESSION['NutsUserID']} AND Plugin = '{$this->name}' AND RecordID = {$_SESSION['FormPercentRecordID']} LIMIT 1";
			$this->nuts->doQuery($sql);
			if($this->nuts->dbNumRows())
			{
				$rec = $this->nuts->dbFetch();
				$arr = $rec['POST'];
				$this->formPercentPOST = unserialize(base64_decode($arr));

				return true;
			}
		}

		return false;
	}

	/**
	 * Get last saved POST
	 *
	 * @return array
	 */
	public function formPercentRenderGetPost()
	{
		return $this->formPercentPOST;
	}

	/**
	 * Set start value
	 */
	public function formPercentRenderSetStartValue($start)
	{
		$_SESSION['FormPercentParams'][0] = $start;
	}





    /**
     * Verify if a record exists in table
     *
     * @param string $table
     * @param string $colID default value: ID
     * @return boolean
     */
	public function isRecordExists($table, $colID = 'ID')
	{
		$res = $this->getQuery("SELECT COUNT(*) FROM $table WHERE Deleted = 'NO' AND ID = {$_GET[$colID]}");
		$res = (int)$res;

		if($res == 1)return true;

		return false;
	}

	private $viewDBTable = array();
    /**
     * Assign a database table
     * @param string $viewDBTable
     */
	public function viewDbTable($viewDBTable)
	{
		$this->viewDBTable = $viewDBTable;
	}

	private $viewCols = array();
    /**
     * Add a line in view mode
     *
     * @param string $name database field
     * @param string $label if empty $label = $name
     * @param string $path image url
     */
	public function viewAddVar($name, $label='', $path='')
	{
		if(empty($label))$label = $name;
		$this->viewCols[] = array('name' => $name, 'label' => $label, 'path' => $path);
	}


	private $viewSQLAddField = array();
    /**
     * Add Dynamic field in view
     *
     * @param string $sql
     */
	public function viewAddSQLField($sql)
	{
		$this->viewSQLAddField[] = $sql;
	}

	/**
     * Render view
	 *
     * @param string $hookData function with row as parameter and return
     * @return mixed|boolean
     */
	public function viewRender($hookData='')
	{
		if(!isset($_GET['ID']))$_GET['ID'] = 0;
		$_GET['ID'] = (int)$_GET['ID'];
		if(!$this->isRecordExists($this->viewDBTable[0]))
		{
			$msg = "Record {$_GET['ID']} doesn't exist";
			$this->trace($msg, $_GET['ID']);
			$this->setError($msg);
			$this->errorRender();
			return false;
		}
		else
		{
			$this->nuts->open(WEBSITE_PATH.'/nuts/_templates/view.html');

			// dynamic js
			$js  = array();
			if(file_exists(PLUGIN_PATH.'/view.js'))
				$js[] = PLUGIN_URL.'/view.js';
			$js = array_unique($js);
			if(count($js) == 0)
			{
				$this->nuts->eraseBloc('js');
			}
			else
			{
				foreach($js as $js1)
				{
					$this->nuts->parse('js.js_file', $js1);
					$this->nuts->loop('js');
				}
			}

			$this->nuts->eraseBloc('fieldset_start');
			$this->nuts->eraseBloc('fieldset_end');

			foreach($this->viewCols as $c)
			{
				$this->nuts->parse('f.label', $c['label']);
				$this->nuts->parse('f.name', $c['name']);
				$this->nuts->loop('f');
			}

			$out = $this->nuts->output();

			$out = str_replace('{ ', '{', $out);
			$out = str_replace('{<', '<', $out);
			$out = str_replace(' }', '}', $out);
			$out = str_replace('>}', '>', $out);
			$out = str_replace('< bloc::', '<bloc::', $out);
			$out = str_replace('< /bloc::', '</bloc::', $out);

			$this->nuts->createVirtualTemplate($out);

			$sql_added = '';
			if(count($this->viewSQLAddField) > 0)$sql_added = ', '.join(', ', $this->viewSQLAddField);
			$row = $this->getData("SELECT * $sql_added FROM {$this->viewDBTable[0]} WHERE ID = {$_GET['ID']}");
			$row = $row[0];

			if(!empty($hookData))
			{
				$row = $hookData($row);
			}


			foreach($this->viewCols as $c)
			{
				// auto conversion for dateGMT
				if(preg_match('/^DateGMT/', $c['name']))
				{
					if($row[$c['name']] == '0000-00-00 00:00:00')
						$row[$c['name']] = '';
					else
						$row[$c['name']] = nutsGetGMTDateUser($row[$c['name']]);
				}

				// date french
				if(preg_match('/^Date/', $c['name']) && $_SESSION['Language'] == "fr")
				{
					$row[$c['name']] = $this->nuts->db2date($row[$c['name']]);
				}

				if($c['name'] == 'Language')
				{
					$row[$c['name']] = strtolower($row[$c['name']]);
					$row[$c['name']] = sprintf('<img src="%s/%s.gif" align="absmiddle" /> %s', NUTS_IMAGES_URL.'/flag', $row[$c['name']], strtoupper($row[$c['name']]));
				}

				// empty
				$row[$c['name']] = trim($row[$c['name']]);
				if(empty($row[$c['name']]))$row[$c['name']] = '-';


				// image
				if(!empty($c['path']))
				{
					$p = $c['path'].'/'.$row[$c['name']];
					$p_tmp = str_replace(WEBSITE_URL, '..', $p);

					if(!file_exists($p_tmp))
						$row[$c['name']] = '&nbsp;';
					else
						$row[$c['name']] = '<img src="'.$p.'" align="absmiddle" />';
				}

				// boolean
				if($row[$c['name']] == 'YES')
					$row[$c['name']] = '<img src="img/YES.gif" align="absmiddle" />';
				elseif($row[$c['name']] == 'NO')
					$row[$c['name']] = '<img src="img/NO.gif" width="16px" align="absmiddle" />';




				$this->nuts->parse($c['name'], $row[$c['name']]);
			}

			$this->render = $this->nuts->output();
		}
	}


	private $deleteDbTable = array();
    /**
     *
     * Delete record from table
     *
     * @param array $deleteDbTable table and cascade linked table
     * @param string $control_mode add special restriction superadmin, restricted, usernotme
     */
	public function deleteDbTable($deleteDbTable, $control_mode='')
	{
		global $nuts_lang_msg;

		$this->deleteDbTable = $deleteDbTable;

		// error control
		if(!isset($_GET['ID']))$_GET['ID'] = 0;
		$_GET['ID'] = (int)$_GET['ID'];
		if(!$this->isRecordExists($this->deleteDbTable[0]))
		{
			$msg = "Record {$_GET['ID']} doesn't exist";
			$this->trace($msg, $_GET['ID']);
			$this->setError($msg);

			return false;
		}

		// control modes
		if(is_array($control_mode))
		{
			foreach($control_mode as $cm)
			{
				// superadmin
				if($cm == 'groupNotSuperAdmin' && $_GET['ID'] == $_SESSION['NutsGroupID'])
				{
					$msg = $nuts_lang_msg[39];
					$this->setError($msg);
					return false;
				}

				// restricted
				elseif($cm == 'mustBeEmpty' && count($this->deleteDbTable) >= 2)
				{
					$res = $this->getQuery("SELECT COUNT(*) FROM {$this->deleteDbTable[1]} WHERE {$this->deleteDbTable[0]}ID = {$_GET['ID']} AND Deleted = 'NO'");
					if($res >= 1)
					{
						$msg = sprintf($nuts_lang_msg[32], $this->deleteDbTable[1], $this->deleteDbTable[0], $_GET['ID']);
						$this->setError($msg);
						return false;
					}
				}

				// usernotme
				elseif($cm == 'userNotMe' && $_GET['ID'] == $_SESSION['ID'])
				{
					$msg = $nuts_lang_msg[36];
					$this->setError($msg);
					return false;
				}



			}
		}
	}

	/**
	 * Create a custom error before delete
	 * @param string $error_msg
	 * @return boolean
	 */
	public function deleteSetError($error_msg)
	{
		if(defined('NUTS_ERROR_TMP'))return false;

		define('NUTS_ERROR_TMP', $error_msg);

		return true;
	}

	/**
	 * User has confirmed popup ?
	 * @return boolean
	 */
	public function deleteUserHasConfirmed()
	{
		if($_POST && isset($_POST['submitted']) && $_POST['submitted'] == 1)
			return true;

		return false;
	}

    /**
     * @var bool Delete for real in the table
     */
    public  $deleteRealMode = false;


    /**
     * Render delete record treatment
     */
	public function deleteRender()
	{
		global $nuts_lang_msg;

		// error detected ?
		if(defined('NUTS_ERROR_TMP'))
		{
			$this->errorRender();
			return false;
		}

		$this->nuts->open(WEBSITE_PATH.'/nuts/_templates/delete.html');

		if(!$_POST)
		{
			$this->nuts->eraseBloc('after_confirm_ok');
		}
		else
		{
		 	$this->nuts->eraseBloc('before_confirm');

            if(!$this->deleteRealMode)
			    $this->nuts->doQuery("UPDATE {$this->deleteDbTable[0]} SET Deleted = 'YES' WHERE ID = {$_GET['ID']}");
            elseif(!$this->deleteRealMode)
                $this->nuts->doQuery("DELETE FROM {$this->deleteDbTable[0]} WHERE ID = {$_GET['ID']} LIMIT 1");

			if(count($this->deleteDbTable) >= 2)
			{
				$tableKeyID = $this->deleteDbTable[0].'ID';
				for($i=1; $i < count($this->deleteDbTable); $i++)
				{
                    if(!$this->deleteRealMode)
					    $this->nuts->doQuery("UPDATE {$this->deleteDbTable[$i]} SET Deleted = 'YES' WHERE $tableKeyID = {$_GET['ID']}");
                    else
                        $this->nuts->doQuery("DELETE FROM  {$this->deleteDbTable[$i]} WHERE $tableKeyID = {$_GET['ID']}");
				}
			}

			$this->formLogDbColumns('DELETE', $_GET['ID']);
			$this->trace('delete', $_GET['ID']);
		}

		$this->render = $this->nuts->output();
	}


    /**
     *
     *
     * @param $counter
     * @param $background_color optionnal
     * @param string $plugin_name
     */


    /**
     * Add notification on plugin in home
     *
     * @param $counter
     * @param $plugin_name
     * @param string $bull_background_color (default=#FF0000)
     * @param string $bull_border_color (default=#C93F9E)
     * @param string $plugin_background_color (default=#FFCCCC)
     */
    function addSystemNotification($counter, $plugin_name, $bull_background_color='#FF0000', $bull_border_color='#C93F9E', $plugin_background_color='#FFCCCC')
    {
        $GLOBALS['system_notifications'][$plugin_name] = array(
                                                                'counter' => $counter,
                                                                'bull_background_color' => $bull_background_color,
                                                                'bull_border_color' => $bull_border_color,
                                                                'plugin_background_color' => $plugin_background_color
                                                               );
    }



}


?>