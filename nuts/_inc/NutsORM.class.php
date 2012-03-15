<?php
/**
 * Nuts ORM - simple ORM factory for Nuts
 * 
 * @package Nuts-Component
 * @version 1.0
 * @date 09/09/2011
 */
abstract class NutsORM
{
	/* @var $DBLinkVarName TPLN */
	private static $_instance = null;
	
	protected $_DBLink = false;
	protected $_ModelColumns = array();
	protected $_ModelMetaColumns = array();
	protected $_q = array(); // query parameters
	protected $_forbidden_keys = array('Deleted');
	
	
	// configuration table properties	
	protected $ClassName = "NutsORM";
	protected $TableName;
	protected $HasMany = array();
	protected $HasOne = array();
	
	private function __construct($ClassName)
	{		
		// init table name by file
		if(empty($this->TableName))
		{
			$this->ClassName = $ClassName;
			$this->TableName = $ClassName;
		}
		
		// init DB link
		if(!$this->_DBLink)
		{
			if(isset($GLOBALS['nuts']))$this->_DBLink = &$GLOBALS['nuts'];
			elseif(isset($GLOBALS['job']))$this->_DBLink = &$GLOBALS['job'];			
			else {die("Error: DB link not found");}
		}
	}
	
	public static function factory($ClassName) 
	{
		if(self::$_instance == null)
		{	
			self::$_instance = new $ClassName($ClassName);
		}		
		return self::$_instance;	
	}
		
	
	/**
	 * Get record
	 * 
	 * @param int $ID (put 0 for clean)
	 * @param boolean $lazy_loading (default: 0)
	 * 
	 * @return object 
	 */
	public static function get($ID, $lazy_loading=false)
	{
		$ID = (int)$ID;
		
		$obj = self::$_instance;		
		if(!count($obj->_ModelColumns))
		{			
			$sql = "SHOW COLUMNS FROM `".$obj->TableName."`";
			$obj->_DBLink->doQuery($sql);
			
			$columns = $obj->_DBLink->dbGetData();
			foreach($columns as $column)
			{				
				$obj->_ModelColumns[] = $column['Field'];
				$obj->_ModelMetaColumns[] = $column;				
			}
		}		
		
		// init object
		foreach($obj->_ModelColumns as $column)
		{
			$obj->{$column} = null;
		}
		
		if($ID)
		{
			// assignation
			$sql = "SELECT * FROM {$obj->TableName} WHERE ID = $ID AND Deleted = 'NO'";
			$obj->_DBLink->doQuery($sql);
			
			if($obj->_DBLink->dbNumRows() == 1)
			{
				$rec = $obj->_DBLink->dbFetch();
				foreach($obj->_ModelColumns as $column)
				{
					if(isset($rec[$column]) && !in_array($column, $obj->_forbidden_keys))
					{						
						// get parent record
						if($lazy_loading && $column != 'ID' && preg_match('/ID$/', $column))
						{
							$fatherColumnID = str_replace('ID', '', $column);
							$sql = "SELECT * FROM {$fatherColumnID} WHERE Deleted = 'NO' AND ID = {$rec['ID']}";
							$obj->_DBLink->doQuery($sql);
							while($rec2 = $obj->_DBLink->dbFetch())
							{
								$tmp = new stdClass();
								foreach($rec2 as $key2 => $val2)
								{
									if(!in_array($key2, $obj->_forbidden_keys))
									{
										$tmp->{$key2} = $val2;
									}
								}								
								
								$obj->{$fatherColumnID} = $tmp;								
							}
						}
						
						$obj->{$column} = $rec[$column];
						
					}
				}
				
				// add many records keys				
				if($lazy_loading)
				{
					foreach($obj->HasMany as $many2)
					{
						$many = array();
					
						// list all children of this
						$sql = "SELECT * FROM {$many2} WHERE Deleted = 'NO' AND {$obj->TableName}ID = {$obj->ID}";
						$obj->_DBLink->doQuery($sql);
						while($rec2 = $obj->_DBLink->dbFetch())
						{
							$tmp = new stdClass();
							foreach($rec2 as $key2 => $val2)
							{
								if(!in_array($key2, $obj->_forbidden_keys))
								{
									$tmp->{$key2} = $val2;
								}
							}
						
							$many[] = $tmp;						
						}
					
						$obj->{$many2} = $many;					
					}				
				}
			}
		}
		
		return $obj;		
	}
		
	/**
	 * Insert a new record
	 * @return int ID 
	 */
	public function insert()
	{
		$arr = array();
		foreach($this->_ModelColumns as $col)
		{
			if(!is_null($this->$col))
			{
				$arr[$col] = $this->$col;				
			}
		}
		
		$this->ID = $this->_DBLink->dbInsert($this->TableName, $arr, array(), true);		
		
		
		// insert many records
		foreach($this->HasMany as $many2)
		{
			if(isset($this->$many2))
			{
				foreach($this->$many2 as $many3)
				{
					$object_vars = get_object_vars($many3);
					$tmp = array();
					foreach($object_vars as $col => $val)
					{
						if(!in_array($col, $this->_forbidden_keys))
							$tmp[$col] = $val;
					}
					
					$tmp[$this->TableName.'ID'] = $this->ID;
					
					$this->_DBLink->dbInsert($many2, $tmp);
					
				}
			}
		}	
		
		return $this->ID;
		
	}
	
	/**
	 * Update a record
	 */
	public function update()
	{
		if(!isset($this->ID))die("Error: ID not found");
		
		$arr = array();
		foreach($this->_ModelColumns as $col)
		{
			if(!is_null($this->$col))
			{
				$arr[$col] = $this->$col;				
			}
		}
		
		$this->_DBLink->dbUpdate($this->TableName, $arr, "ID = {$this->ID}", array());	
	}	
	
	/**
	 * Delete a record
	 */
	public function delete()
	{
		if(!isset($this->ID))die("Error: ID not found");
		
		$arr = array();
		$arr['Deleted'] = 'YES';
		
		$this->_DBLink->dbUpdate($this->TableName, $arr, "ID = {$this->ID}", array());
		
		// kill childrens too
		foreach($this->HasMany as $many2)
		{
			$this->_DBLink->dbUpdate($many2, $arr, "{$this->TableName}ID = {$this->ID}", array());
		}			
		
	}
	

	/**
	 * Add Select
	 * @param string $str 
	 */
	public function select($str)
	{
		$this->_q['select'] = $str;
		return $this;
	}
	
	/**
	 * Add From
	 * @param string $str 
	 */
	public function from($str)
	{
		$this->_q['from'] = $str;
		return $this;
	}

	/**
	 * Add Where
	 * @param string $str
	 * @param string $params dynamic 
	 */
	public function where($str, $params1='', $params2='', $paramsN='')
	{
		$f_args = func_get_args(); // get all the arguments				
		if(count($f_args) >= 2)
		{			
			// protect args
			$vals = array();
			for($i=1; $i < count($f_args); $i++)
			{
				$vals[] = $this->_DBLink->xssProtect($f_args[$i]);
			}
			
			$str = vsprintf($str, $vals);
		}	
		
		$this->_q['where'] = $str;
		return $this;
	}
	
	/**
	 * Add GroupBy
	 * @param string $str 
	 */
	public function group_by($str)
	{
		$this->_q['group by'] = $str;
		return $this;
	}	

	/**
	 * Add OrderBy
	 * @param string $str 
	 */
	public function order_by($str)
	{
		$this->_q['order by'] = $str;
		return $this;
	}

	/**
	 * Add Limit
	 * @param string $str 
	 */
	public function limit($str)
	{
		$this->_q['limit'] = $str;
		return $this;
	}
	
	/**
	 * Get Sql query constructed	
	 */
	public function getSql()
	{
		if(!isset($this->_q['select']))$this->_q['select'] = '*';
		if(!isset($this->_q['from']))$this->_q['from'] = $this->TableName;
		if(!isset($this->_q['where']))$this->_q['where'] = "";
		if(!isset($this->_q['group by']))$this->_q['group by'] = "";
		if(!isset($this->_q['order by']))$this->_q['order by'] = "";
		if(!isset($this->_q['limit']))$this->_q['limit'] = "";
		
		
		$sql = CR;
		$sql .= "SELECT".CR;
		$sql .= "		{$this->_q['select']}".CR;
		$sql .= "FROM".CR;
		$sql .= "		{$this->_q['from']}".CR;
		$sql .= "WHERE".CR;	
		
		if(!empty($this->_q['where']))
			$sql .= "		{$this->_q['where']} AND".CR;
		
		
		if(empty($this->_q['from']))
		{
			$sql .= "		Deleted = 'NO'".CR;			
		}
		else
		{						
			$froms = explode(",", $this->_q['from']);
			$froms = array_map('trim', $froms);
			
			$i = 0;
			foreach($froms as $from)
			{
				if($i > 0)$sql .= " AND ";
				$sql .= "		$from.Deleted = 'NO'".CR;
				$i++;
			}
		}
		
		if(!empty($this->_q['group by']))
		{
			$sql .= "GROUP BY ".CR;
			$sql .= "			{$this->_q['group by']}".CR;
		}
		
		if(!empty($this->_q['order by']))
		{
			$sql .= "ORDER BY ".CR;
			$sql .= "			{$this->_q['order by']}".CR;
		}
		
		if(!empty($this->_q['limit']))
		{
			$sql .= "LIMIT ".CR;
			$sql .= "			{$this->_q['limit']}".CR;
		}
				
		return $sql;
	}
	
	/**
	 * Count query res
	 * @return int number of records found
	 */
	public function count()
	{
		return	$this->_DBLink->dbNumRows();
		
	}
	
	/**
	 * Get an associated object array after execute()
	 */
	public function getAll() 
	{
		$sql = $this->getSql();
		
		// reset query
		$this->_q = array();
		$this->_DBLink->doQuery($sql, PDO::FETCH_OBJ);
		
		
		$arr = array();
		
		while($rec = $this->_DBLink->dbFetch())
		{
			$obj = new stdClass();			
			foreach($rec as $key => $val)
			{
				if(!in_array($key, $this->_forbidden_keys))
					$obj->$key = $val;
			}
						
			$arr[] = $obj;
		}
		
		
		return $arr;		
	}
	
	/**
	 * Execute query
	 */
	public function doQuery($sql) 
	{
		$this->_DBLink->doQuery($sql);
	}
	
	
	/**
	 * Get associated fetch 
	 * @return array
	 */
	public function dbFetch()
	{
		return $this->_DBLink->dbFetch(PDO::FETCH_OBJ);
	}
	
	
	
}






?>