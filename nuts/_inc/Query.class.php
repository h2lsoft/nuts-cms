<?php
/**
 * Query simple query constructor
 *
 * @package Nuts-Component
 * @version 1.0
 * @date 30/11/2011
 */

class Query
{
	protected $_DBLink = false;

	private $_q = array();
	private $_last_query;

	/**
	 * Constructor
	 */
	protected function __construct() {
		if(!$this->_DBLink)
		{
			if(isset($GLOBALS['nuts']))$this->_DBLink = $GLOBALS['nuts'];
			elseif(isset($GLOBALS['job']))$this->_DBLink = $GLOBALS['job'];
			else {die("Error: DB link not found");}
		}
	}

	/**
	 * @static
	 * @return Query
	 */
	public static function factory(){
		return new self();
	}

	/**
	 * Add Select
	 * @param string $fields
	 */
	public function select($fields)
	{
		$this->_q['select'] = $fields;
		return $this;
	}

	/**
	 * Add From
	 * @param string $str
	 */
	public function from($tables)
	{
		$this->_q['from'] = $tables;
		return $this;
	}

	/**
	 * Add Where
	 * @param string $conditions
     * @param string $operator (default empty no operator take full confition)
     * @param string $str (default empty)
	 */
	public function where($conditions, $operator="", $str='')
	{

        if(in_array($operator, array('IN', 'NOT IN')))
        {
	        if(is_array($str))
	        {
		        $tmp = '';
		        foreach($str as $val)
		        {
			        if(!empty($tmp))$tmp .= ', ';
			        $tmp .= "'".sqlX($val)."'";
		        }

		        $conditions = $conditions.' '.$operator."(".$tmp.")";
	        }
	        else
	        {
		        $conditions = $conditions.' '.$operator.' '.$str;
	        }

        }
        elseif(!empty($operator))
        {
            $strX = sqlX($str);
            $conditions = $conditions.' '.$operator." '".$strX."' ";
        }

		$this->_q['where'][] = $conditions;
		return $this;
	}

    /**
     * Add where equal to (=)
     * @param int $value
     * @return Query
     */
    public function whereID($value){return $this->where('ID', '=', (int)$value);}


    /**
     * Add where equal to (=)
     * @param $column
     * @param $value
     * @return Query
     */
    public function whereEqualTo($column, $value){return $this->where($column, '=', $value);}

    /**
     * Add where not equal to (!=)
     * @param $column
     * @param $value
     * @return Query
     */
    public function whereNotEqualTo($column, $value){return $this->where($column, '!=', $value);}

    /**
     * Add where greater than (>)
     * @param $column
     * @param $value
     * @return Query
     */
    public function whereGreaterThan($column, $value){return $this->where($column, '>', $value);}

    /**
     * Add where greater than equal (>=)
     * @param $column
     * @param $value
     * @return Query
     */
    public function whereGreaterThanEqual($column, $value){return $this->where($column, '>=', $value);}

    /**
     * Add where less than (<)
     * @param $column
     * @param $value
     * @return Query
     */
    public function whereLessThan($column, $value){return $this->where($column, '<', $value);}

    /**
     * Add where less than (<=)
     * @param $column
     * @param $value
     * @return Query
     */
    public function whereLessThanEqual($column, $value){return $this->where($column, '<=', $value);}

    /**
     * Add where Like (LIKE)
     * @param $column
     * @param $value
     * @return Query
     */
    public function whereLike($column, $value){return $this->where($column, 'LIKE', $value);}

    /**
     * Add where not Like (NOT LIKE)
     * @param $column
     * @param $value
     * @return Query
     */
    public function whereNotLike($column, $value){return $this->where($column, 'NOT LIKE', $value);}

    /**
     * Add where in (IN)
     * @param $column
     * @param mixed string|array $value
     * @return Query
     */
    public function whereIn($column, $value){
        if(!is_array($value))trigger_error("value must be an array", E_USER_ERROR);
        return $this->where($column, 'IN', $value);
    }

    /**
     * Add where not in (NOT IN)
     * @param $column
     * @param mixed string|array $value
     * @return Query
     */
    public function whereNotIn($column, $value){
        if(!is_array($value))trigger_error("value must be an array", E_USER_ERROR);
        return $this->where($column, 'NOT IN', $value);
    }

	/**
	 * Add GroupBy
	 * @param string $str
	 */
	public function group_by($columns)
	{
		$this->_q['group by'] = $columns;
		return $this;
	}

	/**
	 * Add Having
	 * @param string $str
	 */
	public function having($str)
	{
		$this->_q['having'] = $str;
		return $this;
	}


	/**
	 * Add OrderBy
	 * @param string $columns
	 */
	public function order_by($columns)
	{
		$this->_q['order by'] = $columns;
		return $this;
	}


	/**
	 * Add Limit
	 * @param int $str
	 */
	public function limit($number)
	{
		$this->_q['limit'] = $number;
		return $this;
	}

	/**
	 * Get Sql query constructed
	 */
	public function get()
	{
		if(!isset($this->_q['select']))$this->_q['select'] = '*';
		if(!isset($this->_q['where']))$this->_q['where'] = array();
		if(!isset($this->_q['group by']))$this->_q['group by'] = "";
		if(!isset($this->_q['having']))$this->_q['having'] = "";
		if(!isset($this->_q['order by']))$this->_q['order by'] = "";
		if(!isset($this->_q['limit']))$this->_q['limit'] = "";


		$select = $this->_q['select'];

		$sql = "";
		$sql .= "SELECT"."\n";
		$sql .= "		{$select}"."\n";
		$sql .= "FROM"."\n";
		$sql .= "		{$this->_q['from']}"."\n";
		$sql .= "WHERE"."\n";

		if(count($this->_q['where']))
		{
			foreach($this->_q['where'] as $w)
			$sql .= "		$w AND"."\n";
		}

		if(empty($this->_q['from']))
		{
			$sql .= "		Deleted = 'NO'"."\n";
		}
		else
		{
			$froms = explode(",", $this->_q['from']);
			$froms = array_map('trim', $froms);

			$i = 0;
			foreach($froms as $from)
			{
				$from = @end(explode(' ', $from));

				if($i > 0)$sql .= " AND ";
				$sql .= "		$from.Deleted = 'NO'"."\n";
				$i++;
			}
		}

		if(!empty($this->_q['group by']))
		{
			$sql .= "GROUP BY "."\n";
			$sql .= "		{$this->_q['group by']}"."\n";
		}

		if(!empty($this->_q['having']))
		{
			$sql .= "HAVING "."\n";
			$sql .= "		{$this->_q['having']}"."\n";
		}

		if(!empty($this->_q['order by']))
		{
			$sql .= "ORDER BY "."\n";
			$sql .= "		{$this->_q['order by']}"."\n";
		}

		if(!empty($this->_q['limit']))
		{
			$sql .= "LIMIT "."\n";
			$sql .= "		{$this->_q['limit']}"."\n";
		}

		$this->_q = array();

		$sql = trim($sql);

		$this->_last_query = $sql;
		return $sql;
	}


	/**
	 * Execute query
     *
     * @param boolean $fb_debug Firebug debug (default = false)
	 */
	function execute($fb_debug=false){
		$sql = $this->get();
        if($fb_debug)FB::info($sql);

		$this->_DBLink->doQuery($sql);
	}

	/**
	 * Execute query and return one result
     *
     * @param boolean $fb_debug Firebug debug (default = false)
	 * @return $col
	 */
	function executeAndGetOne($fb_debug=false){
		$this->execute();
		return $this->_DBLink->dbGetOne();
	}

    /**
     * execute query and fetch
     *
     * @param boolean $fb_debug Firebug debug (default = false)
     * @return array $result
     */
    function executeAndFetch($fb_debug=false){
        $this->execute($fb_debug);

        return $this->_DBLink->dbFetch();
    }

	/**
	 * Execute query and return one resultset
     *
     * @param boolean $fb_debug Firebug debug (default = false)
     * @return array $data
	 */
	function executeAndGetAll($fb_debug=false){
		$this->execute($fb_debug);
		return $this->_DBLink->dbGetData();
	}


	/**
	 * Execute query and return one resultset
     *
     * @param boolean $fb_debug Firebug debug (default = false)
     * @return array $data
	 */
	function executeAndGetAllOne($fb_debug=false){

        $this->execute($fb_debug);
        return $this->_DBLink->dbGetOneData();
	}


}


?>