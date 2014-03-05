<?php
/**
 * Nuts ORM - simple ORM factory for Nuts
 *
 * @package Nuts-Component
 * @version 3.0
 * @date 04/03/2014
 */

define('NutsORMVersion' , '1.0');

class NutsORM
{
	private $_columns;
	private $_DBLink;
	private $_tables_linked = array();
	private $_tables_linked_last;

	protected $table_name;

	# table fields
	# end of table fields


	/**
	 * Auto create class
	 *
	 * @param string $class_name
	 * @param bool $update_existed (false by default)
	 */
	static function createClass($class_name, $update_existed=false)
	{
		$path = WEBSITE_PATH.'/x_includes/orm';
		$file_name = $path.'/'.$class_name.'.db.class.php';
		if(file_exists($file_name) && !$update_existed)return;

		// get columns
		if(isset($GLOBALS['nuts']))$db_link = &$GLOBALS['nuts'];
		elseif(isset($GLOBALS['job']))$db_link = &$GLOBALS['job'];
		else {die("Error: DB link not found");}

		$sql = "SHOW FIELDS FROM `$class_name`";
		$db_link->doQuery($sql);

		$columns_str = '';
		while($field = $db_link->dbFetch()){
			$columns_str .= "\tpublic \${$field['Field']};\n";
		}
		$columns_str = trim($columns_str);

		// write file
		$date = date('d/m/Y');
		$NutsORMVersion = NutsORMVersion;
		$contents = <<<EOF
<?php
/**
 * $class_name class
 *
 * @date $date
 * @author NutsORM Generator {$NutsORMVersion}
 */

class $class_name extends NutsORM
{
	# table fields
	$columns_str
	# end of table fields
}

EOF;
		file_put_contents($file_name, $contents);

	}



	/**
	 * Use the NutsORM
	 */
	public function __construct() {

		$this->table_name = get_called_class();
		if(!$this->_DBLink)
		{
			if(isset($GLOBALS['nuts']))$this->_DBLink = $GLOBALS['nuts'];
			elseif(isset($GLOBALS['job']))$this->_DBLink = $GLOBALS['job'];
			else {die("Error: DB link not found");}

			// init data keys
			$var = array_keys(get_class_vars(get_class($this)));

			$this->_columns = array();
			foreach($var as $_col)
			{
				if($_col[0] != '_')
					$this->_columns[] = $_col;
			}
		}
	}

	/**
	 * Reset values
	 */
	private function reset()
	{
		foreach($this->_columns as $col)
		{
			if($col != 'table_name')
				$this->{$col} = null;
		}
	}

	/**
	 * Get values modified
	 * @return array
	 */
	private function getValuesModified()
	{
		$data = array();
		foreach($this->_columns as $col)
		{
			if(!is_null($this->{$col}) && $col != 'table_name')
				$data[$col] = $this->{$col};
		}

		if(!count($data))
		{
			trigger_error("No column modified", E_USER_NOTICE);
			return false;
		}

		return $data;
	}


	/**
	 * Insert row in table
	 *
	 * @param bool $log
	 * @param string $log_column
	 *
	 * @return int ID created
	 */
	public function insert()
	{
		$data = $this->getValuesModified();
		if(!count($data))return false;

		$ID = $this->_DBLink->dbInsert($this->table_name, $data, array(), true);
		$this->reset();

		return $ID;
	}


	/**
	 * Update row in table
	 *
	 * @param array|int $ID
	 */
	public function update($ID)
	{
		$data = $this->getValuesModified();
		if(!count($data))return false;

		if(!is_array($ID))
		{
			$ID = (int)$ID;
		}
		else
		{
			$ID = array_map('intval', $ID);
			$ID = join(', ', $ID);
		}

		$this->_DBLink->dbUpdate($this->table_name, $data, "ID IN($ID)");
		$this->reset();
	}

	/**
	 * Delete row in table
	 *
	 * @param array|int $ID
	 * @param array $has (tables linked)
	 */
	public function delete($ID, $has=array())
	{
		if(!is_array($ID))
		{
			$ID = (int)$ID;
		}
		else
		{
			$ID = array_map('intval', $ID);
			$ID = join(', ', $ID);
		}

		$this->_DBLink->dbUpdate($this->table_name, array('Deleted' => 'YES'), "ID IN($ID)");
		foreach($has as $linked_table)
		{
			$this->_DBLink->dbUpdate($linked_table, array('Deleted' => 'YES'), "{$this->table_name}ID IN($ID)");
		}

		$this->reset();
	}

	/**
	 * Get current row for ID
	 *
	 * @param int $ID
	 *
	 * @return mixed
	 */
	public function get($ID)
	{
		$ID = (int)$ID;
		$this->_DBLink->doQuery("SELECT * FROM {$this->table_name} WHERE  Deleted = 'NO' AND ID = $ID");
		$rec = $this->_DBLink->dbFetchObject();

		$has = array_keys($this->_tables_linked);
		foreach($has as $linked_table)
		{
			$sql = "SELECT * FROM $linked_table WHERE Deleted = 'NO' AND {$this->table_name}ID = $ID";
			if(!@empty($this->_tables_linked[$linked_table]['where']))$sql .= " AND\n{$this->_tables_linked[$linked_table]['where']}";
			if(!@empty($this->_tables_linked[$linked_table]['order_by']))$sql .= "\nORDER BY {$this->_tables_linked[$linked_table]['order_by']}";
			if(!@empty($this->_tables_linked[$linked_table]['limit']))$sql .= "\nLIMIT {$this->_tables_linked[$linked_table]['limit']}";

			$this->_DBLink->doQuery($sql);
			$rec->{$linked_table.'s'} = array();
			while($tmp = $this->_DBLink->dbFetchObject())
				$rec->{$linked_table.'s'}[] = $tmp;
		}

		// reset filters
		$this->_tables_linked = array();
		$this->_tables_linked_last = '';

		return $rec;
	}

	/**
	 * Add With for get or delete in cascade
	 * @param $table_name
	 *
	 * @return $this
	 */
	public function with($table_name)
	{
		$this->_tables_linked_last = $table_name;
		$this->_tables_linked[$table_name]['table'] = $table_name;
		return $this;
	}

	/**
	 * Add Where for get or delete in cascade
	 * @param $constraint
	 * @param $val
	 *
	 * @return $this
	 */
	public function where($constraint, $val='')
	{
		if(empty($val))
		{
			$where = $constraint;
		}
		else
		{
			$val = sqlX($val);
			$where = sprintf($constraint, $val);
		}

		$this->_tables_linked[$this->_tables_linked_last]['where'] = $where;
		return $this;
	}

	/**
	 * Add Order By for get or delete in cascade
	 * @param $col
	 *
	 * @return $this
	 */
	public function order_by($col)
	{
		$this->_tables_linked[$this->_tables_linked_last]['order_by'] = $col;
		return $this;
	}

	/**
	 * Add Limit for get in cascade
	 * @param $limit
	 *
	 * @return $this
	 */
	public function limit($limit)
	{
		$limit = (int)$limit;
		$this->_tables_linked[$this->_tables_linked_last]['limit'] = $limit;
		return $this;
	}



}