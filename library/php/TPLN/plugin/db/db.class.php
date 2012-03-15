<?php
/**
 * TPLN Database Plugin
 * @package Template Engine
 */
class Db extends Form
{
	// DataBase
	public  $db_index = -1;
	public  $req_index = -1; // index the results in progress
	public  $db = array(); // object which contains the connection informations
	protected  $req = array(); // storage of results
	protected  $DBLastError = ''; // the last SQL error
	protected  $query_count = 0; // number of executed requests
	// Xtra
	protected  $cons_query = array(); // the dislocated request is stored here
	protected  $NavColorFirst = ''; // color for the navigation
	protected  $NavColorSecond = '';
	protected  $url = ''; // contient les variables
	protected  $url_var = ''; // contains the parameters of the url
	protected  $UrlRgxPatterns = ''; // contains the regex for formatting the urls
	protected  $UrlRgxReplace = '';
	protected  $user_resulset = array(); // contains the user results

	// owner
	protected  $NbRecordPerPage = 0; // number of wished results per page
	protected  $NbResults = 0; // the results
	protected  $Count = 0;
	protected  $T_first = 0; // which used to determinate the id
	protected  $First = 0;
	protected  $Last = 0;
	protected  $PageNumber = 0;
	protected  $PageCount = 0;
	protected  $NavColor = '';
	protected  $OrderByFields = array();
	protected  $OrderByImgsPath = ''; // path per defect des icone asc-desc

	protected $dbProtectionMode = true; // enable or disable protection mode


// DataBase ******************************************************************************************************************* @author H2LSOFT */
	/**
	 * this method allows you to connect to your MySQL database.
	 *
	 * @param string $db_type
	 * @param string $host
	 * @param string $login
	 * @param string $password
	 * @param string $base
	 * @param string $port
	 * @param boolean $new_connection
	 *
	 * @author H2LSOFT
	 */
	public function dbConnect($db_type = '', $host = '', $login = '', $password = '', $base = '', $port = '', $new_connection = '')
	{
		// initialisation of variable
		if(empty($db_type))$db_type = TPLN_DB_TYPE_DEFAULT;
		if(empty($host))$host = TPLN_DB_HOST_DEFAULT;
		if(empty($login))$login = TPLN_DB_LOGIN_DEFAULT;
		if(empty($password))$password = TPLN_DB_PASSWORD_DEFAULT;
		if(empty($base))$base = TPLN_DB_BASE_DEFAULT;
		if(empty($port))$port = TPLN_DB_PORT;
		if(empty($new_connection))$new_connection = TPLN_DB_NEW_CONNECTION;

		$this->db_index++;
		if(!empty($port))$this->db[$this->db_index]->port = $port;

		try {
			$persistant = ($new_connection) ? false : true;
			$this->db[$this->db_index] = @new PDO("$db_type:host=$host;dbname=$base;port=$port", $login, $password, array(PDO::ATTR_PERSISTENT => $new_connection));
		}
		catch(PDOException $e){
			$this->dbError(0, $e->getMessage());
		}
		
		// init queries
		if(TPLN_DB_INIT_QUERIES != '')
		{
			$t = explode(';', TPLN_DB_INIT_QUERIES);
			foreach($t as $sql_n)
			{
				$sql_n = trim($sql_n);
				if(!empty($sql_n))
					$this->doQuery($sql_n);
			}
		}
	}

	/**
	 * this method allows to change connection.
	 *
	 * @param int $db_index
	 *
	 * @return boolean
	 *
	 * @deprecated
	 * @since 2.9
	 * @see setConnectionID()
	 * @author H2LSOFT */
	public function changeConnection($db_index)
	{
		if($db_index < 0 || $db_index >= count($this->db))
		{
			$this->dbError('2.1', $db_index);
			return;
		}

		$this->db_index = $db_index;
	}

	/**
	 * return db connection object to share with framework
	 * @param int dbIndex
	 */
	public function dbGetConnectionObject($db_index='')
	{
		if($db_index == '')$db_index = $this->db_index;
		return $this->db[$db_index];
	}



	/**
	 * @deprecated
	 * @since 2.9
	 * @see DbSetConnectionId().
	 * @author H2LSOFT */
	public function setConnectionID($db_index)
	{
		$this->changeConnection($db_index);
	}

	/**
	 * method allows you to change connection.
	 *
	 * @param int $db_index
	 * @since 2.9
	 * @see dbConnect()
	 * @author H2LSOFT */
	public function dbSetConnectionID($db_index)
	{
		$this->setConnectionId($db_index);
	}

	/**
	 * @deprecated
	 * @since 2.9
	 * @see dbGetConnectionId
	 * @author H2LSOFT */
	public function getConnectionID()
	{
		return $this->db_index;
	}

	/**
	 * this method allows to change connection.
	 *
	 * @return int
	 * @since 2.9
	 * @see setConnectionId
	 * @author H2LSOFT */
	public function dbGetConnectionID()
	{
		return $this->getConnectionID();
	}

	/**
	 * @deprecated
	 * @since 2.2.5
	 * @see dbsetQueryId
	 * @author H2LSOFT */
	public function changeQuery($req_index)
	{
		if($req_index < 0 || $req_index >= count($this->req))
		{
			$this->dbError('2.11', $req_index);
			return;
		}

		$this->req_index = $req_index;
	}

	/**
	 * @deprecated
	 * @since 2.9
	 * @see dbSetQueryId
	 * @author H2LSOFT */
	public function setQueryID($req_index)
	{
		$this->changeQuery($req_index);
	}

	/**
	 * This method allows to change queries resultset.
	 *
	 * @param int $req_index
	 * @since 2.2.5
	 * @see getQueryId
	 * @author H2LSOFT */
	public function dbSetQueryID($req_index)
	{
		$this->setQueryID($req_index);
	}

	/**
	 * @deprecated
	 * @since 2.2.5
	 * @see dbGetQueryNb
	 * @author H2LSOFT */
	public function getQueryNb()
	{
		return $this->req_index;
	}

	/**
	 * This method allows to retriece the index of the current query.
	 *
	 * @return int
	 *
	 * @since 2.2.5
	 * @see setQueryID
	 * @author H2LSOFT */
	public function dbGetQueryNb()
	{
		return $this->getQueryNb();
	}

	/**
	 * @deprecated
	 * @since 2.9
	 * @see dbGetQueryId
	 * @author H2LSOFT */
	public function getQueryID()
	{
		return $this->getQueryNb();
	}

	/**
	 * This method allows you to retriece the index of the current query.
	 *
	 * @return int
	 *
	 * @since 2.9
	 * @see setQueryId
	 * @author H2LSOFT */
	public function dbGetQueryID()
	{
		return $this->getQueryId();
	}

	/**
	 * method allows you to close database connection.
	 *
	 * @see DbConnect()
	 * @author H2LSOFT */
	public function dbClose()
	{
		$this->db[$this->db_index] = null;
	}

	/**
	 * verify quotes
	 * @param int $value
	 *
	 * @return int
	 * @author H2LSOFT */
	public function checkQuotes($value)
	{
		if(is_array($value))
		{
			foreach($value as $key => $val)
				$value[$key] = $this->checkQuotes($val);

			return $value;
		}
		else
		{
			// return addslashes($value);
			return strtr($value, array("\x00" => '\x00', "\n" => '\n', "\r" => '\r', '\\' => '\\\\', "'" => "\'", '"' => '\"', "\x1a" => '\x1a'));

		}
			// return mysql_escape_string($value);
		//return ((!get_magic_quotes_runtime() && !get_magic_quotes_gpc()) ? mysql_real_escape_string($value) : $value);

	}

	/**
	 * strip slashes
	 * @param int $value
	 *
	 * @return int
	 * @author H2LSOFT */
	public function stripQuotes($value)
	{
		if(is_array($value))
		{
			foreach($value as $key => $val)
				$value[$key] = $this->stripQuotes($val);

			return $value;
		}
		else
			return stripslashes($value);
	}

	/**
	 * This method returns the maximum value for a ID, it is useful to know the value of an id after an insertion in a table.
	 *
	 * @param string $table
	 * @param string $ID
	 *
	 * @return int
	 *
	 * @since 2.5
	 * @author H2LSOFT */
	public function getMaxID($table, $ID='ID')
	{
		$sql = "SELECT MAX($ID) FROM $table";
		$this->doQuery($sql);
		return $this->getOne();
	}

	/**
	 * This method returns the maximum value for a ID, it is useful to know the value of an id after an insertion in a table.
	 *
	 * @param string $table
	 * @param string $ID default ID
	 *
	 * @return int
	 * @author H2LSOFT */
	public function dbGetMaxID($table, $ID='ID')
	{

		return $this->getMaxId($table, $ID);
	}

	/**
	 * this method allows to execute database query.
	 *
	 * If second parameter is filled, the array structure for data is different
	 *
	 * @param string $query
	 * @param int $fetch_mode
	 *
	 * @return boolean
	 *
	 * @since 2.4, 2.8
	 * @author H2LSOFT */
	public function doQuery($sql, $FETCH_MODE=PDO::FETCH_BOTH)
	{		
		if(!is_object($this->db[$this->db_index]))
		{
			$this->dbError(0.1);
			return;
		}

		$this->query_count++;
		$this->req_index++;		
		
		$this->req[$this->req_index] = $this->db[$this->db_index]->query($sql);

		if(!$this->req[$this->req_index])
		{		
			// without debug ?
			$msgs = $this->db[$this->db_index]->errorInfo();
			$msg = "{$msgs[2]} (code #{$msgs[1]})";

			if(TPLN_SQL_QUERY_DEBUG)
				$msg .= " <br><br>\n<pre><i><strong>`".$sql.'`</strong></i></pre>';
			$this->DBLastError = $msg;
			$this->dbError(2, $msg , TPLN_SQL_QUERY_DEBUG);
			return false;
		}

		return true;
	}

	/**
	 * This method allows to generate and execute insert query from an associative array.
	 *
	 * You can exlude some fields from your insert query
	 *
	 * @param string $table
	 * @param array $arr
	 * @param array $exlude_fields
	 * @param bool $return_last_id
	 * @param string $pk_key_name default ID
	 *
	 * @return boolean
	 *
	 * @since 2.8
	 * @author H2LSOFT */
	public function dbInsert($table, $arr, $exlude_fields=array(), $return_last_id=false, $pk_key_name='ID')
	{
		// protection
		$arr = $this->dbProtection($arr);

		$fields = array();
		$vals = array();
		foreach($arr as $key => $val)
		{
			// jokers
			$joker = false;
			foreach($exlude_fields as $ef)
			{
				$tmp = explode('*', $ef);
				if(count($tmp) == 2)
				{
					$str = $tmp[0];
					//if(eregi("^$str", $key))
					if(stripos($key, $str) !== false && stripos($key, $str) == 0)
					{
						$joker = true;
						break;
					}
				}
			}

			if(!in_array($key, $exlude_fields) && !$joker)
			{
				$fields[] = $key;
				$vals[] = $val;
			}
		}



		$sql = "INSERT INTO $table\n ";
		$sql .= "\t(\n\t\t".join(",\n\t\t", $fields)."\n\t)\n";
		$sql .= "VALUES\n";
		$sql .= "\t(\n";
		for($i=0; $i < count($vals); $i++)
		{
			if(!isset($vals[$i]))
				$s = 'NULL';
			elseif(is_int($vals[$i]) || is_float($vals[$i]) || in_array($vals[$i], array('NOW()', 'NULL')))
				$s = $vals[$i];
			else
				$s = "'".mysql_escape_string($vals[$i])."'";
			if($i < count($vals)-1)$s .= ',';
			$sql .= " \t\t$s\n";
		}
		$sql .= "\t)";

		$b = $this->doQuery($sql);

		// return last insert id
		if($return_last_id)
		{
			return $this->dbGetMaxId($table, $pk_key_name);
		}



		return $b;
	}

	/**
	 * This method allows you to generate and execute update query from an associative array.
	 *
	 * You can exlude some fields from your update query
	 *
	 * @param string $table
	 * @param array $arr
	 * @param string $where
	 * @param array $exlude_fields
	 *
	 * @return boolean
	 *
	 * @since 2.8, 2.9
	 * @author H2LSOFT */
	public function dbUpdate($table, $arr, $where='', $exlude_fields=array())
	{
		// protection
		$arr = $this->dbProtection($arr);

		$fields = array();
		$vals = array();
		foreach($arr as $key => $val)
		{
			// jokers
			$joker = false;
			foreach($exlude_fields as $ef)
			{
				$tmp = explode('*', $ef);
				if(count($tmp) == 2)
				{
					$str = $tmp[0];
					//if(eregi("^$str", $key))
					if(stripos($key, $str) !== false && stripos($key, $str) == 0)
					{
						$joker = true;
						break;
					}
				}
			}

			if(!in_array($key, $exlude_fields) && !$joker)
			{
				$fields[] = $key;
				$vals[] = $val;
			}
		}

		$sql = "UPDATE $table SET\n";
		for($i=0; $i < count($vals); $i++)
		{
			if(!isset($vals[$i]))
				$s = 'NULL';
			elseif(is_int($vals[$i]) || is_float($vals[$i]) || in_array($vals[$i], array('NOW()', 'NULL')))
				$s = $vals[$i];
			else
				$s = "'".mysql_escape_string($vals[$i])."'";
			if($i < count($vals)-1)$s .= ',';
			$sql .= " \t\t{$fields[$i]} = $s\n";
		}

		if(!empty($where))
			$where = "WHERE\n\t\t".$where;
		$sql .= $where;

		return $this->doQuery($sql);
	}

	/**
	 * This method allows to generate and execute REPALCE query from an associative array.
	 *
	 * You can exlude some fields from your update query, use * character to exclude a name generically mce*
	 *
	 * will exclude all name in the array that begins with mce.
	 *
	 * @param string $table
	 * @param array $arr
	 * @param  array $exlude_fields
	 *
	 * @return boolean
	 *
	 * @since 2.9.1
	 * @author H2LSOFT */
	public function dbReplace($table, $arr, $exlude_fields=array())
	{
		// protection
		$arr = $this->dbProtection($arr);
		
		$fields = array();
		$vals = array();
		foreach($arr as $key => $val)
		{
			// jokers
			$joker = false;
			foreach($exlude_fields as $ef)
			{
				$tmp = explode('*', $ef);
				if(count($tmp) == 2)
				{
					$str = $tmp[0];
					//if(eregi("^$str", $key))
					if(stripos($key, $str) !== false && stripos($key, $str) == 0)
					{
						$joker = true;
						break;
					}
				}
			}

			if(!in_array($key, $exlude_fields) && !$joker)
			{
				$fields[] = $key;
				$vals[] = $val;
			}
		}

		$sql = "REPLACE $table SET\n";
		for($i=0; $i < count($vals); $i++)
		{
			if(!isset($vals[$i]))
				$s = 'NULL';
			elseif(is_int($vals[$i]) || is_float($vals[$i]) || in_array($vals[$i], array('NOW()', 'NULL')))
				$s = $vals[$i];
			else
				$s = "'".mysql_escape_string($vals[$i])."'";
			if($i < count($vals)-1)$s .= ',';
			$sql .= " \t\t{$fields[$i]} = $s\n";
		}

	/*if(!empty($where))
		$where = "WHERE\n\t\t".$where;
	$sql .= $where;*/

		return $this->doQuery($sql);
	}

	/**
	 * This method allows you to execute a query SELECT with parameters,
	 *
	 * each parameter is protected. This method uses like the php function vsprintf
	 *
	 * @param string $sql
	 * @param array $values
	 *
	 * @author H2LSOFT */
	public function dbSelect($sql, $values)
	{
		$values = $this->checkQuotes($values);
		$sql = vsprintf ($sql, $values);
		$this->doQuery($sql);
	}

	/**
	 * This method allows to know if the database connection is active
	 *
	 * @return boolean
	 *
	 * @since 2.9.2
	 * @author H2LSOFT */
	public function dbIsConnected()
	{
		return ($this->db[$this->db_index]);
	}

	/**
	 * This method allows you to generate and execute delete query from an associative array.
	 *
	 * @param string $table
	 * @param string $where
	 *
	 * @return boolean
	 *
	 * @since 2.8
	 * @author H2LSOFT */
	public function dbDelete($table, $where='')
	{
		if(!empty($where))
			$where = 'WHERE '.$where;

		$sql = "DELETE FROM $table $where";

		return $this->doQuery($sql);
	}

	/**
	 * This method returns the last SQL error
	 *
	 * @return string
	 *
	 * @see doQuery
	 *
	 * @author H2LSOFT */
	public function dbGetError()
	{
		return $this->DBLastError;
	}

	/**
	 * this method allows to return database query results count.
	 *
	 * @return int
	 *
	 * @deprecated
	 * @see DBNumRows
	 * @author H2LSOFT */
	public function getRowsCount()
	{
		if(!is_object($this->req[$this->req_index]))
		{
			$this->dbError(2.2);
			return;
		}

		$rows_count = $this->req[$this->req_index]->rowCount();
		return $rows_count;
	}

	/**
	 * this method allows to return database query results count.
	 *
	 * @return int
	 *
	 * @since 2.9
	 * @author H2LSOFT */
	public function dbNumRows()
	{
		return $this->getRowsCount();
	}

	/**
	 * allows to obtain in structured array form database query results.
	 *
	 * It is different in the array created which has the fields as keys in the array.
	 *
	 * The array depends by DoQuery() parameter by default associative array is created.
	 *
	 * @return array
	 * @author H2LSOFT */
	public function  dbFetch($pdo_fetch_mode=PDO::FETCH_ASSOC)
	{
		if(!is_object($this->req[$this->req_index]))
		{
			$this->dbError(2.2);
			return;
		}
		$row = $this->req[$this->req_index]->fetch($pdo_fetch_mode);
		return $row;
	}
	
	/**
	 * method allows to obtain as abject form database query results.
	 *
	 * @return object
	 *
	 * @since 3.5
	 * @see DBFetchObject()
	 * @author H2LSOFT */
	public function dbFetchObject()
	{
		if(!is_object($this->req[$this->req_index]))
		{
			$this->dbError(2.2);
			return;
		}

		$row = $this->req[$this->req_index]->fetch(PDO::FETCH_OBJ);
		return $row;
	}
	

	/**
	 * method allows to obtain as array form database query results.
	 *
	 * @return array
	 *
	 * @since 2.8
	 * @see DBFetch()
	 * @author H2LSOFT */
	public function dbFetchArray()
	{
		if(!is_object($this->req[$this->req_index]))
		{
			$this->dbError(2.2);
			return;
		}

		$row = $this->req[$this->req_index]->fetch(PDO::FETCH_NUM);
		return $row;
	}

	/**
	 * allows to obtain in structured array form database query results.
	 *
	 * It is different in the array created which has the fields as keys in the array.
	 *
	 * @return array
	 *
	 * @since 2.8
	 * @author H2LSOFT */
	public function dbFetchAssoc()
	{
		if(!is_object($this->req[$this->req_index]))
		{
			$this->dbError(2.2);
			return;
		}
		$row = $this->req[$this->req_index]->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	/**
	 * allows to release memory allocated for your server.
	 *
	 * For example, you can to use this method after long treatments.
	 *
	 * @author H2LSOFT */
	public function dbFreeResult()
	{
		if(!is_object($this->req[$this->req_index]))
		{
			$this->dbError(2.2);
			return;
		}

		$this->req[$this->req_index] = null;
	}

	/**
	 * This method allows to get your recorset in one time
	 *
	 * @deprecated
	 * @return array
	 * @see dbGetData
	 * @author H2LSOFT */
	public function getData()
	{
		$i = 0;
		$results = array();
		while($row = $this->dbFetchAssoc())
			$results[] = $row;

		$this->dbFreeResult();

		return $results;
	}

	/**
	 * This method allows to get your recorset in one time
	 *
	 * @return array
	 *
	 * @since 2.8
	 * @author H2LSOFT */
	public function dbGetData()
	{
		return $this->getData();
	}

	/**
	 * @see dbGetOne()
	 *
	 * @deprecated
	 * @return int
	 * @author H2LSOFT */
	public function getOne()
	{
		$res = $this->dbFetchArray();
		$this->dbFreeResult();
		
		if(count($res) == 0)
			return '';
		else		
			return @$res[0];
	}

	/**
	 * method allows to return the first result of a database query.
	 *
	 * This method is useful for a count database query for exemple.
	 *
	 * @return int
	 * @author H2LSOFT */
	public function dbGetOne()
	{
		return $this->getOne();
	}

	/**
	 * this method allows to return the database query fields number.
	 *
	 * @deprecated
	 * @return int
	 * @see dbGetNumFields
	 * @author H2LSOFT */
	public function getNumFields()
	{
		if(!is_object($this->req[$this->req_index]))
		{
			$this->dbError(2.2);
			return;
		}

		$fields_count = $this->req[$this->req_index]->columnCount();
		return $fields_count;
	}

	/**
	 * allows you to return the database query fields number
	 *
	 * @return int
	 * @author H2LSOFT */
	public function dbGetNumFields()
	{
		return $this->getNumFields();
	}

	/**
	 * this method allows to return the database query fields name.
	 *
	 * @return array
	 * @deprecated
	 * @since 1.5
	 * @see dbGetFields
	 * @author H2LSOFT */
	public function getFields()
	{
		return array_keys($this->req[$this->req_index]->fields);
	}

	/**
	 * this method allows to return the database query fields name.
	 *
	 * @return array
	 * @author H2LSOFT */
	public function dbGetFields()
	{
		return $this->getFields();
	}

	/**
	 * allows to return the field name from your database query.
	 *
	 * @deprecated
	 * @param int $field_no
	 * @return string
	 * @see dbGetFieldName
	 * @author H2LSOFT */
	public function getFieldName($field_no)
	{
		if(!is_object($this->req[$this->req_index]))
		{
			$this->dbError(2.2);
			return;
		}

		if($field_no < 0 || $field_no >= $this->getNumFields())
		{
			$this->dbError(2.3, $field_no);
			return;
		}

		// take the first line
		$row = $this->DBFetchAssoc();
		// $row =  $this->req[$this->req_index]->fetchColumn();
		$col = array_keys($row);

		return $col[$field_no];
	}

	/**
	 * allows to return the field name from the database query.
	 *
	 * @param int $field_no
	 *
	 * @return string
	 * @author H2LSOFT */
	public function dbGetFieldName($field_no)
	{
		return $this->getFieldName($field_no);
	}

	/**
	 * this method allows to obtain the database names of the server.
	 *
	 * @deprecated
	 * @return array
	 * @see dbGetDatabaseList
	 * @author H2LSOFT */
	public function getDbList()
	{
		if(!is_object($this->db[$this->db_index]))
		{
			$this->dbError(0.1);
			return;
		}
		$this->doQuery('SHOW DATABASES');
		$dbs = array();
		while($row = $this->dbFetchArray())
		{
			$dbs[] = $row[0];
		}

		return $dbs;
	}

	/**
	 * this method allows to obtain the database names of the server.
	 *
	 * @return array
	 * @author H2LSOFT */
	public function dbGetDatabaseList()
	{
		return $this->getDBList();
	}

	/**
	 * this method allows to obtain arrays name of the database.
	 * @deprecated
	 * @see dbGetTableList
	 * @author H2LSOFT */
	public function getTableList()
	{
		if(!is_object($this->db[$this->db_index]))
		{
			$this->dbError(0.1);
			return;
		}

		$this->doQuery('SHOW TABLES');
		while($row = $this->dbFetchArray())
			$res[] = $row[0];

		return $res;
	}

	/**
	 * this method allows to obtain arrays name of the database.
	 *
	 * @return array
	 * @author H2LSOFT */
	public function dbGetTableList()
	{
		return $this->getTableList();
	}

// constuction the request
	/**
	 * en commentaire
	 * @param string $query
	 * @author H2LSOFT */
	protected function setQuery($query)
	{
		// encodage de la requete
		// $query = str_replace("\n",' ',$query);
		// $query = str_replace("\r",' ',$query);

	/*if(count($this->cons_query) > 0 || is_array($this->user_resulset)) return;

	$this->cons_query['STRING'] = $query;
	// on parcours la requete � l'envers ;-)'
	// limit ?
	if(preg_match("/LIMIT(.*)/msi", $query, $match))
	{
		$this->cons_query['LIMIT'] = trim($match[1]);
		$query = str_replace($match[0], '', $query); // on remplace ds la chaine
	}
	// order by
	if(preg_match("/ORDER BY(.*)/msi", $query, $match))
	{
		$this->cons_query['ORDER BY'] = trim($match[1]);
		$query = str_replace($match[0], '', $query); // on remplace ds la chaine

		// y a t il une clause speciale ? order by filter ?
		if(isset($_GET['torder_by']))
			$this->cons_query['ORDER BY'] = "{$_GET['torder_by']} {$_GET['tsens']}, ".$this->cons_query['ORDER BY'];
	}
	else
	{
		if(isset($_GET['torder_by']))
			$this->cons_query['ORDER BY'] = "{$_GET['torder_by']} {$_GET['tsens']}";
	}


	// group by
	if(preg_match("/GROUP BY(.*)/msi", $query, $match))
	{
		$this->cons_query['GROUP BY'] = trim($match[1]);
		$query = str_replace($match[0], '', $query); // on remplace ds la chaine
	}
	// where
	if(preg_match("/WHERE(.*)/msi", $query, $match))
	{
		$this->cons_query['WHERE'] = trim($match[1]);
		$query = str_replace($match[0], '', $query); // on remplace ds la chaine
	}
	// from
	if(!preg_match("/FROM(.*)/msi", $query, $match))$this->_DBError(4);

	$this->cons_query['FROM'] = trim($match[1]);
	$query = str_replace($match[0], '', $query); // on remplace ds la chaine
	// select
	if(!preg_match("/SELECT(.*)/msi", $query, $match))$this->_DBError(3);

	$this->cons_query['SELECT'] = trim($match[1]);
	$query = str_replace($match[0], '', $query); // on remplace ds la chaine
	* @author H2LSOFT */
	}

// recreate a request with the new parameters of LIMIT.
	/**
	 *
	 * @author H2LSOFT */
	protected function setQueryLimit()
	{
		if(empty($this->PageNumber))
		{
			$this->PageNumber = 1;
		}
		// calcul du nombre de pages
		if($this->NbRecordPerPage == 0)
		{
			$this->PageCount = 1;
			$this->PageNumber = 1;
			$this->First = 1;
			$this->T_first = $this->First;
			$this->Last = $this->Count;
		}
		else
		{
			$this->PageCount = ceil($this->Count / $this->NbRecordPerPage); // arrondi a l'entier superieur
			if($this->PageNumber > $this->PageCount)
			{
				$this->PageNumber = $this->PageCount;
			}

			// on determine debut du limit
			$this->First = ($this->PageNumber - 1) * $this->NbRecordPerPage;
			$this->T_first = $this->First ;
			$this->Last = $this->First + $this->NbRecordPerPage;
		}

	/* v2.9
	if(!is_array($this->user_resulset))
	{
		// on reconstruit la requete
		$query = "SELECT \n";
			$query .= $this->cons_query['SELECT']." \n";
		$query .= " FROM \n";
		$query .= $this->cons_query['FROM']." \n";
		// Where
		if(!empty($this->cons_query['WHERE']))
		{
			$query .= ' WHERE '." \n";
			$query .= $this->cons_query['WHERE']." \n";
		}
		// Group By
		if(!empty($this->cons_query['GROUP BY']))
		{
			$query .= ' GROUP BY '." \n";
			$query .= $this->cons_query['GROUP BY']." \n";
		}
		// Order By
		if(!empty($this->cons_query['ORDER BY']))
		{
			$query .= ' ORDER BY '." \n";;
			$query .= $this->cons_query['ORDER BY']." \n";
		}
	}
	* @author H2LSOFT */

		// limit ?
		if($this->NbRecordPerPage > 0)
		{
			// if($this->First == 0){$this->First = 1;}
			$this->First++;
			if($this->Last > $this->Count)
			{
				$this->Last = $this->Count;
			}

		/* v2.9
		if(!is_array($this->user_resulset))
			$query .= " LIMIT  $this->First,$this->NbRecordPerPage";
		* @author H2LSOFT */
		}

	/* v2.9
	if(!is_array($this->user_resulset))
		return $query;
	* @author H2LSOFT */
	}


	/**
	 * return the correct formatted url
	 * @param string $t_pg
	 *
	 * @return string
	 * @author H2LSOFT */
	protected function SRgetUrl($t_pg = '')
	{		
		$url = $this->Url."tpg=$t_pg".$this->url_var;		
		if(!empty($this->UrlRgxPatterns) && !empty($this->UrlRgxReplace))
			$url = @preg_replace('/'.$this->UrlRgxPatterns.'/i', $this->UrlRgxReplace, $url);

		return $url;
	}

	/**
	 * apply rewrite rule in URL
	 *
	 * @param string $patterns
	 * @param string $replace
	 * @author H2LSOFT */
	public function rewriteUrl($patterns, $replace)
	{
		$this->UrlRgxPatterns = $patterns;
		$this->UrlRgxReplace = $replace;
	}

	/**
	 * asign color alternation
	 *
	 * @param string $color1
	 * @param string $color2
	 * @author H2LSOFT */
	public function setNavColor($color1, $color2)
	{
		$this->NavColorFirst = $color1;
		$this->NavColorSecond = $color2;
	}

// prise totale des r�sulats
	/**
	 * en commentaire
	 * @author H2LSOFT */
	protected function getTotalCount()
	{
	/* v2.9

	$query = 'SELECT ';

	// on regarde s'il y a un distinct
	$distinct = false;
	$d = trim($this->cons_query['SELECT']);
	if(strpos($d, 'DISTINCT') === false)
		$query .= ' COUNT(*)';
	else
	{
		$distinct = true;
		// on capture le champs qui possede le distinct
		list($f) = explode(',', $this->cons_query['SELECT']);
		list($f) = explode(' AS ', $f);
		$query .= " COUNT($f) ";
	}

	$query .= 'FROM ';
	$query .= $this->cons_query['FROM'];

	if(!empty($this->cons_query['WHERE']))
	{
		$query .= ' WHERE ';
		$query .= $this->cons_query['WHERE'];
	}

	$this->DoQuery($query) or die($this->DBGetError());

	// group by sans clause distinct
	if(eregi('group by', $query) && !$distinct)
		$this->Count = $this->getRowsCount();
	else
		$this->Count = $this->GetOne();
	* @author H2LSOFT */
	}

	/**
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function applyPrivateVar()
	{
		if(empty($this->Url))
			$this->Url = $_SERVER['PHP_SELF'].'?';		

		// TPLN variable
		if($this->itemExists('_Count', 'data'))
			$this->parse('data._Count', $this->Count);

		if($this->itemExists('_First', 'data'))
			$this->Parse('data._First', $this->First);

		if($this->itemExists('_Last', 'data'))
			$this->parse('data._Last', $this->Last);

		if($this->itemExists('_PageCount', 'data'))
			$this->parse('data._PageCount', $this->PageCount);

		if($this->itemExists('_PageNumber', 'data'))
			$this->parse('data._PageNumber', $this->PageNumber);

		// order by ?
		for($i=0; $i < count($this->OrderByFields); $i++)
		{
			$f = $this->OrderByFields[$i];
			$img = '';
			$tpg = (isset($_GET['tpg'])) ? $_GET['tpg'] : 1;
			$u = $this->SRgetUrl($tpg);
			if($_GET['torder_by'] == $f)
			{
				$sens = ($_GET['tsens'] == 'asc') ? 'desc' : 'asc';
				$u = str_replace("tsens={$_GET['tsens']}", "tsens=$sens", $u);
				$img = "<a href=\"$u\"><img src=\"$this->OrderByImgsPath/order_{$_GET['tsens']}_actived.gif\" style=\"border:0;\" align=\"absmiddle\" /></a>";
			}
			else
			{
				$u = str_replace("torder_by={$_GET['torder_by']}&tsens={$_GET['tsens']}", "torder_by=$f&tsens=desc", $u);
				$img = "<a href=\"$u\"><img src=\"$this->OrderByImgsPath/order_desc.gif\" style=\"border:0;\" /></a>";
				//$u = str_replace("torder_by={$_GET['torder_by']}&tsens={$_GET['tsens']}", "torder_by=$f&tsens=asc", $u);
				//$img .= "<a href=\"$u\"><img src=\"$this->OrderByImgsPath/order_asc.gif\" style=\"border:0;\" /></a>";
			}

			$this->parse("data._order_by::$f", $img);
		}



		// PREVIOUS
		if($this->blocExists('previous'))
		{
			if(!$this->itemExists('_Url', 'previous'))
			{
				$this->error('1.1', $this->f[$this->f_no]['name'], 'previous', '_Url');
				return;
			}

			if($this->PageNumber <= $this->PageCount && $this->PageCount != 1 && $this->PageNumber > 1)
			{
				$prev_pg = $this->PageNumber - 1;
				if($prev_pg < 1)$prev_pg = 1;
				$url = $this->SRgetUrl($prev_pg);				
				$this->parse('data.previous._Url', $url);				
			}
			else
			{
				$this->eraseBloc('data.previous');
			}
		}

		// NEXT
		if($this->blocExists('next'))
		{
			if(!$this->itemExists('_Url', 'next'))
			{
				$this->error('1.1', $this->f[$this->f_no]['name'], 'next', '_Url');
				return;
			}

		if($this->PageNumber < $this->PageCount && $this->PageCount != 1)
		{
			$next_pg = $this->PageNumber + 1;
			$url = $this->SRgetUrl($next_pg);
			$this->parse('data.next._Url', $url);
		}
		else
			$this->EraseBloc('data.next');		
		}

		// START
		if($this->blocExists('start'))
		{
			if(!$this->itemExists('_Url', 'start'))
			{
				$this->error('1.1', $this->f[$this->f_no]['name'], 'start', '_Url');
				return;
			}

			if($this->PageCount > 1 && $this->PageNumber > 1)
			{
				$url = $this->SRgetUrl(1);
				$this->parse('data.start._Url', $url);
			}
			else
				$this->EraseBloc('data.start');
		}

		// END
		if($this->blocExists('end'))
		{
			if(!$this->itemExists('_Url', 'end'))
			{
				$this->error('1.1', $this->f[$this->f_no]['name'], 'end', '_Url');
				return;
			}

			if($this->PageCount > 1 && $this->PageNumber < $this->PageCount)
			{
				$url = $this->SRgetUrl($this->PageCount);
				$this->parse('data.end._Url', $url);
			}
			else
				$this->EraseBloc('data.end');
		}

		// PAGER
		if($this->blocExists('pager'))
		{
			// block out & in
			if(!$this->blocExists('out'))
			{
				$this->error(2, $this->f[$this->f_no]['name'], 'out');
				return;
			}
			if(!$this->itemExists('_Url', 'out'))
			{
				$this->error('1.1', $this->f[$this->f_no]['name'], 'out', '_Url');
				return;
			}
			if(!$this->itemExists('_Page', 'out'))
			{
				$this->error('1.1', $this->f[$this->f_no]['name'], 'out', '_Page');
				return;
			}

			if(!$this->blocExists('in'))
			{
				$this->error(2, $this->f[$this->f_no]['name'], 'in');
				return;
			}

			if(!$this->itemExists('_Page', 'in'))
			{
				$this->error('1.1', $this->f[$this->f_no]['name'], 'in', '_Page');
				return;
			}
			

			if($this->PageCount > 1)
			{
				$in = $this->getBlocInFile('data.pager.in');
				$out = $this->getBlocInFile('data.pager.out');
				// $this->eraseBloc("in");
				// $this->eraseBloc("out");
				$str = '';

				for($l = 1; $l <= $this->PageCount; $l++)
				{
					if($l == $this->PageNumber)
					{
						$str .= str_replace('{_Page}', $l, $in);
					}
					else
					{
						$url = $this->SRgetUrl($l);

						$tmp = str_replace('{_Page}', $l, $out);
						$tmp = str_replace('{_Url}', $url, $tmp);

						$str .= $tmp;
					}
				}

				$this->parseBloc('data.pager', $str);
			}
			else
			{
				$this->eraseBloc('data.pager');
			}
		}

		// initialize the navigation
		if(empty($this->NavColorFirst))
		{
			$this->NavColorFirst = TPLN_DB_NavColorFirst;
		}
		if(empty($this->NavColorSecond))
		{
			$this->NavColorSecond = TPLN_DB_NavColorSecond;
		}
	}

	/**
	 * change URL
	 *
	 * @param string $url
	 * @author H2LSOFT */
	public function setUrl($url)
	{
		$this->Url = $url;
	}

	/**
	 * add variable in URL
	 *
	 * @param string $url_add
	 * @author H2LSOFT */
	public function urlAddVar($url_add)
	{
		$this->url_var = "&$url_add";
	}

	/**
	 * this method allows to create field variables
	 * @param array $path
	 * @param array $fields
	 *
	 * @return boolean
	 * @author H2LSOFT */
	public function createFieldVars($path, $fields)
	{
		// path defined en array
		if(is_array($path))
		{
			for($i = 0;$i < count($path);$i++)
			{
				$this->createFieldVars($path[$i], $fields);
			}
			return;
		}

		$arr = @explode('.', $path);

		if(count($arr) == 1)
		{
			$this->error('13', $this->f[$this->f_no]['name'], $arr[0]);
			return;
		}

		$lastbloc = $arr[count($arr)-1];

		// Verification du mot clefs _Field
		if(!$this->itemExists('_Field', $lastbloc) && !$this->itemExists('_FieldLabel', $lastbloc))
		{
			$this->error('1.1', $this->f[$this->f_no]['name'], $lastbloc, '_Field');
			return;
		}

		$cur_bloc_ini = $this->getBlocInFile($lastbloc);
		$str = '';
		$all = '';
		$tab = array();

		for($i = 0;$i < count($fields);$i++)
		{
			// on remplace le mot clef field
			$str = str_replace('{_Field}', '{'.$fields[$i].'}', $cur_bloc_ini);
			$str = str_replace('{_FieldId}', $i, $str);
			$str = str_replace('{_FieldLabel}', str_replace('_', ' ', ucfirst($fields[$i])), $str);
			$all .= $str;
		}
		// on remplace le contenu du champs
		// $this->ParseBloc($path, $all);
		$this->parseBloc($lastbloc, $all);
		// on retire le dernier du path
		// on veut que le chemin des peres
		$path = $this->getFathers($path, 'ARRAY', 0);
		$path = array_slice ($path, 0, count($path)-1);
		$path = join($path, '.');

		$this->reloadBlocVars($path);
	}

	/**
	 * This method will manage your filter automatically.
	 *
	 * You have to put a preformatted variable in your template {_order_by::my_field}.
	 *
	 * @param array $fields
	 * @param array $img_dir
	 *
	 * @since 2.8
	 * @author H2LSOFT */
	public function showRecordsOrderBy($fields, $img_dir='')
	{
		$this->OrderByFields = $fields;

		if(!empty($img_dir))
			$this->OrderByImgsPath = $img_dir;
		else
			$this->OrderByImgsPath = TPLN_WEB_PATH.'/img';

		if(!isset($_GET['torder_by']) || !in_array($_GET['torder_by'], $fields))
			$_GET['torder_by'] = $fields[0];

		if(!isset($_GET['tsens']) || !in_array($_GET['tsens'], array('asc','desc')))
			$_GET['tsens'] = 'desc';

		$this->url_var .= "&torder_by={$_GET['torder_by']}&tsens={$_GET['tsens']}";
	}

	/**
	 * allows to parse automatically the datas from your databasse,
	 *
	 * you have only to create your template and this method make all the tasks for you!
	 *
	 * @param string $query
	 * @param int $nb_result_per_page
	 * @param string $func_data
	 *
	 * @return int
	 * @since TPLN 1.5
	 * @author H2LSOFT */
	public function showRecords($query, $nb_result_per_page = 0, $func_data = '')
	{
		// check the second parameter
		if(!is_int($nb_result_per_page))
		{
			$this->dbError(5);
			return;
		}
		if($nb_result_per_page < 0)
		{
			$this->dbError('5.1');
			return;
		}

		// check the results array
		$this->breaker_name = (!$this->blocExists('breaker')) ? '' : 'breaker';
		$this->user_resulset = $query;

		$this->NbRecordPerPage = $nb_result_per_page;

		if($this->NbRecordPerPage != 0 && isset($_GET['tpg']))
			$this->PageNumber = $_GET['tpg']; // variable on parameter
		else
			$this->PageNumber = null;

		// sql version
		if(!is_array($this->user_resulset))
		{			
			// $this->setQuery(''); // reconstruit la requete et assigne les variables
			// $this->_GetTotalCount(); //  nb total d'enregistrements

			//$res =  $this->db[$this->db_index]->Execute($query);
			// $this->Count = $res->numRows();
			// $this->query_count++;

			// patch query by laurent hayoun ******************************
			// assign dynamic count values for large table
			$query_tmp = $query;
			// $query_tmp = str_replace("\n", ' ', $query_tmp);
			$query_tmp = str_replace("\r", ' ', $query_tmp);
			$this->cons_query['STRING'] = $query;

			// replace subqueries bu _TPLN_REP_ID_
			$reps_pattern = array();
			$reps_rep = array();

			$query_tmp2 = $query_tmp;
			$query_tmp2 = str_replace(')', ' )', $query_tmp2);
			$query_tmp2 = str_replace('* )', '*)', $query_tmp2);
			$query_tmp2 = str_replace(' ) ) ', ') ) ', $query_tmp2);


			//preg_match_all("#\(SELECT(.*)\)#msU", $query_tmp, $matches);
			preg_match_all("#\(SELECT (.*) \) #", $query_tmp2, $matches);			

			if(isset($matches[0]))
			{
				for($i=0; $i < count($matches[0]); $i++)
				{
					$reps_pattern[] = '_TPLN_REP_'.$i.'_';
					$reps_rep[] = $matches[0][$i];
				}
				$query_tmp2 = str_replace($reps_rep, $reps_pattern, $query_tmp2);
			}

			// get LIMIT
			if(preg_match("/LIMIT(.*)/s", $query_tmp2, $match))
			{
				$str = str_replace($reps_pattern, $reps_rep, $match[1]);
				$this->cons_query['LIMIT'] = trim($str);
				$query_tmp2 = str_replace($match[0], '', $query_tmp2); // delete string
			}
			// get ORDER BY
			if(preg_match("/ORDER BY(.*)/s", $query_tmp2, $match))
			{
				$str = str_replace($reps_pattern, $reps_rep, $match[1]);
				$this->cons_query['ORDER BY'] = trim($str);
				$query_tmp2 = str_replace($match[0], '', $query_tmp2); // delete string

				// special clause
				if(isset($_GET['torder_by']))
				{
					$t_sens = strtoupper($_GET['tsens']);
					$this->cons_query['ORDER BY'] = "{$_GET['torder_by']} $t_sens, ".$this->cons_query['ORDER BY'];
				}
			}
			else
			{
				if(isset($_GET['torder_by']))
				{
					$t_sens = strtoupper($_GET['tsens']);
					$this->cons_query['ORDER BY'] = "{$_GET['torder_by']} $t_sens";
				}
			}
			// get GROUP BY
			if(preg_match("/GROUP BY(.*)/s", $query_tmp2, $match))
			{
				$str = str_replace($reps_pattern, $reps_rep, $match[1]);
				$this->cons_query['GROUP BY'] = trim($str);
				$query_tmp2 = str_replace($match[0], '', $query_tmp2); // delete string
			}
			// get WHERE
			if(preg_match("/WHERE(.*)/s", $query_tmp2, $match))
			{
				$str = str_replace($reps_pattern, $reps_rep, $match[1]);
				$this->cons_query['WHERE'] = trim($str);
				$query_tmp2 = str_replace($match[0], '', $query_tmp2); // delete string
			}
			// get FROM
			if(preg_match("/FROM(.*)/s", $query_tmp2, $match))
			{
				$str = str_replace($reps_pattern, $reps_rep, $match[1]);
				$this->cons_query['FROM'] = trim($str);
				$query_tmp2 = str_replace($match[0], '', $query_tmp2); // delete string
			}
			// get SELECT
			if(preg_match("/SELECT(.*)/s", $query_tmp2, $match))
			{
				$str = str_replace($reps_pattern, $reps_rep, $match[1]);
				$this->cons_query['SELECT'] = trim($str);
				$query_tmp2 = str_replace($match[0], '', $query_tmp2); // delete string
			}
			$this->cons_query['COUNT'] = "*";

			// get total count query
			$sql_tmp = "SELECT
                               COUNT({$this->cons_query['COUNT']})
                        FROM
								{$this->cons_query['FROM']} ";
			if(!empty($this->cons_query['WHERE']))
			{
				$sql_tmp .= "WHERE
					{$this->cons_query['WHERE']} ";
			}
			if(!empty($this->cons_query['GROUP BY']))
			{
				$sql_tmp .= "GROUP BY
					{$this->cons_query['GROUP BY']} ";
			}

			// hack prevent SQL_CALC_NUM_ROWS
			if(strpos($query, 'SQL_CALC_FOUND_ROWS') !== false)
			{
				$sql_tmp = 'SELECT FOUND_ROWS()';
			}			


			$this->doQuery($sql_tmp);
			$this->Count = $this->getOne();			

			// end of patch query by laurent hayoun ***********************

			// y a t il une clause speciale ? order by filter ?
			if(isset($_GET['torder_by']) && in_array($_GET['torder_by'], $this->OrderByFields))
			{
				// clause of the end ?
				if(preg_match("#(.*)ORDER BY(.*)$#si", $query))
				{
					$query .= " ,{$_GET['torder_by']} {$_GET['tsens']}";
				}
				else
				{
					$query .= " ORDER BY {$_GET['torder_by']} {$_GET['tsens']}";
				}
			}


			// $this->doQuery($query);
			// $this->Count = $this->dbNumRows();

			$this->setQueryLimit(); // request which contains the limits

			
			$tmp_nb_result_per_page = $nb_result_per_page;
			if($tmp_nb_result_per_page == 0)$tmp_nb_result_per_page = -1;		
			$limit_start = ($this->PageNumber-1) * $nb_result_per_page;
			if($limit_start < 0)$limit_start = 0;
			if($nb_result_per_page == 0)
			{
				$query_limit = $query;
			}
			else
			{
				$query_limit = $query." LIMIT ".$limit_start.', '.$tmp_nb_result_per_page;
			}
			
			$this->doQuery($query_limit);

		}
		else
		{
			$this->Count = count($this->user_resulset);
			$this->setQuery(''); // rebuild the request and assign the variables
		}

		// no records
		if($this->Count == 0)
		{
			$no_records = $this->getBlocInFile('data.norecord');
			$this->eraseBloc('data.norecord');
			$this->parseBloc('data', $no_records);

			$this->cons_query = array();
			return $this->Count;
		}

		$sql_results = array();
		if(!is_array($this->user_resulset))
		{
			$this->NbResults = $this->dbNumRows(); // resultats obtenues != $this->count
			$sql_results = $this->dbGetData(); // recupere les résultats
			
		}
		else
		{
			$this->setQueryLimit(); // requete contenant les limites
			$this->NbResults = count($this->user_resulset);
		}

		if($this->NbResults == 0) // il y'en a pas pour une recherche
		{
			// to define for the research
			$no_records = $this->getBlocInFile('data.norecord');
			$this->eraseBloc('data.norecord');
			$this->parseBloc('data', $no_records);

			$this->cons_query = array();
			return $this->Count;
		}
		else // if there is results then
		{
			$this->eraseBloc('data.norecord'); // erase norecord
			$this->applyPrivateVar(); // modify ths items propriaitaire $Count...
			if(!is_array($this->user_resulset) && count($this->f[$this->f_no]['shortcut_blocs']['loop']['items']) == 0)
			{
				$this->cons_query = array();
				return $this->Count;
			}
			// parsing
			$this->T_i = 0; // for the  _NavColor
			$this->T_id = $this->T_first; // for _Id

			if($this->T_id == 0)$this->T_id = 1;
			if(isset($_GET['tpg']) && $_GET['tpg'] > 1)$this->T_id++; # patch legrandd 
			// $this->T_id++;

			$this->last_header = '';
			if(!is_array($this->user_resulset))
			{
				// while($row = $this->dbFetchAssoc())
				foreach($sql_results as $row)
				{
					$this->trtShowRecords($row, $func_data);
				}
			}
			else
			{
				// take just the wished results
				if($this->PageCount > 1)
				{
					$this->user_resulset = array_slice($this->user_resulset, $this->First-1, $this->NbRecordPerPage);
				}

				foreach($this->user_resulset as $row)
				{
					$this->trtShowRecords($row, $func_data);
				}
			}
		}

		$this->loop('data');

		$this->cons_query = array();
		return $this->Count;
	}

	var $T_i = 0;
	var $T_id = 0;
	var $last_header;
	var $breaker_name;
	var $header_count;
	/**
	 *
	 * @param  array $row
	 * @param  string $func_data
	 * @author H2LSOFT */
	protected function trtShowRecords($row, $func_data)
	{
		// if(!empty($func_data))$row = $func_data($row);
		if(!empty($func_data))
		{
			// method ?
			if(strpos($func_data, '::') !== false || strpos($func_data, '->') !== false) // methode ?
				eval("\$row = $func_data(\$row);");
			else
				$row = $func_data($row);
		}

		// breaker
		if(!empty($this->breaker_name) && $this->last_header != $row['breaker'])
		{
			$this->last_header = $row['breaker'];
			$this->header_count = 0;
		}

		$keys = @array_keys($row); // take the names of the keys
		foreach($keys as $key)
		{
			// add the nav color
			if($this->itemExists('_NavColor', 'loop'))
			{
				$color = ($this->T_i % 2) ? $this->NavColorFirst : $this->NavColorSecond;
				$this->parse('data.loop._NavColor', $color);
			}

			// add the Id
			if($this->itemExists('_Id', 'loop'))
				$this->parse('data.loop._Id', $this->T_id);

			if(!is_int($key) && $this->itemExists($key, 'loop'))
				$this->parse("data.loop.$key", $row[$key]);

			// breaker
			if(!empty($this->breaker_name))
			{
				if(!is_int($key) && $this->itemExists($key, 'breaker'))
				{
					$this->parse("data.loop.breaker.$key", $row[$key]);
				}
			}
		}

		$this->T_i++;
		$this->T_id++;

		// breaker
		if(!empty($this->breaker_name))
		{
			if($this->header_count == 1)
			{
				$this->eraseBloc('data.loop.breaker');
				$this->header_count = 0;
			}

			// $this->loop('data.loop.breaker');
			$this->header_count++;
		}


		$this->loop('data.loop');
	}

	/**
	 *
	 * @global array $_err
	 * @param int $err_no
	 * @param string $msg
	 * @param int $exit
	 * @author H2LSOFT */
	protected function dbError($err_no, $msg = '', $exit = 1)
	{
		global $_err;

		$err_msg = $_err['DB']["$err_no"];
		$err_msg = str_replace('[:MSG:]', $msg, $err_msg);
		$this->error_msg = "<B>TPLN DB Error $err_no:</B> <table border=1><tr><td>$err_msg</td></tr></table>";
		$this->error_user_level = E_USER_ERROR;
		
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
			
			$this->error_msg .= "</pre>";
		}
		
		
		

		$this->outPutMessage($exit);
	}

	/**
	 * this method allows to parse all the variables of a block by the database query returned values.
	 *
	 * The parse_function is Parse() method parameter, you could add php methods inside this method.
	 *
	 * @param string $bloc
	 * @param string $msg
	 * @param string $func
	 * @author H2LSOFT */
	public function parseDbRow($bloc, $msg = '', $func = '')
	{
		$this->pathVerify($bloc);

		if(empty($this->NavColorFirst))$this->NavColorFirst = TPLN_DB_NavColorFirst;
		if(empty($this->NavColorSecond))$this->NavColorSecond = TPLN_DB_NavColorSecond;

		$res = $this->getRowsCount();
		if($res == 0)
		{
			$this->parseBloc($bloc, $msg);
		}
		else
		{
			$i = 0;
			while($row = $this->dbFetchAssoc())
			{
				$keys = @array_keys($row); // prise du mon des clefs
				$i++;

				foreach($keys as $key)
				{
					if($this->itemExists($key, $bloc))
					{
						//if(!empty($func))
						$this->parse("$bloc.$key", $row[$key], $func);
						//else
						//$this->Parse("$bloc.$key", $row[$key]);
					}

					if($this->itemExists('_NavColor', $bloc))
					{
						$color = ($i % 2) ? $this->NavColorFirst : $this->NavColorSecond;
						$this->parse($bloc.'._NavColor', $color);
					}

				}
				$this->loop($bloc);
			}
		}
	}

	/**
	 * this method allows to parse all the variables of a block by the database query field returned values.
	 *
	 * @param  string $bloc
	 * @param  string $func
	 *
	 * @since 2.3, 2.9
	 * @see ParseDBField().
	 * @author H2LSOFT */
	public function parseDbField($bloc, $func = '')
	{
		$fields = $this->getFields();

		// check the path
		$this->pathVerify($bloc);

		// simulate that we take an item
		// $last_bloc = $this->_GetItem($bloc);

		if(empty($this->NavColorFirst))$this->NavColorFirst = TPLN_DB_NavColorFirst;
		if(empty($this->NavColorSecond))$this->NavColorSecond = TPLN_DB_NavColorSecond;

		// control the path !
		for($i = 0; $i < count($fields); $i++)
		{
			$field_name = $fields[$i];

			// if($this->ItemExists($field_name, $last_bloc))
			if($this->itemExists('_Field', $bloc))
			{
				//if(!empty($func))
				$this->parse("$bloc._Field", $field_name, $func);
				//else
				//$this->Parse("$bloc._Field", $field_name);

				if($this->itemExists('_NavColor', $bloc))
				{
					$color = ($i % 2) ? $this->NavColorFirst : $this->NavColorSecond;
					$this->parse($bloc.'._NavColor', $color);
				}

			}
			$this->loop($bloc);
		}
	}

	/**
	 * This method allows to convert a date in mysql format:
	 *
	 * DD/MM/YYYY becomes YYYY-MM-DD DD-MM-YYYY HH:MM becomes YYYY-MM-DD HH:MM:SS
	 *
	 * @param string $date
	 *
	 * @return string
	 * @see db2Date
	 * @author H2LSOFT */
	public function date2Db($date)
	{
		if(empty($date) || $date == '00/00/0000' || $date == '00-00-0000' || $date == '00/00/0000 00:00' || $date == '00-00-0000 00:00')return '';
		
		// date
		if(strlen($date) == 10)
		{
			list($d, $m, $Y) = explode('/', $date);
			$new_date = "$Y-$m-$d";
		}
		// datetime
		else
		{
			list($d, $m, $Y) = explode('/', $date);
			$tmp = explode(' ', $Y);
			$Y = $tmp[0];
			list($H, $i) = explode(':', $tmp[1]);

			$new_date = "$Y-$m-$d $H:$i:00";
		}

		return $new_date;
	}

	/**
	 * This method allows you to convert a date in mysql format to:
	 *
	 * YYYY-MM-DD becomes DD-MM-YYYY YYYY-MM-DD HH:MM:SS becomes DD-MM-YYYY HH:MM
	 * @param string $date
	 *
	 * @return string
	 *
	 * @see date2Db
	 * @author H2LSOFT */
	public function db2Date($date)
	{
		if(empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00')return '';

		// date
		if(strlen($date) == 10)
		{
			list($Y, $m, $d) = explode('-', $date);
			$new_date = "$d/$m/$Y";
		}
		else
		{
			list($Y, $m, $d) = explode('-', $date);
			$tmp = explode(' ', $d);
			$d = $tmp[0];
			list($H, $i) = explode(':', $tmp[1]);

			$new_date = "$d/$m/$Y $H:$i";
		}

		return $new_date;
	}

	/**
	 * Enable protection mode for input data recommended true
	 *
	 * @param boolean $bool
	 * @since 3.2
	 */
	public function dbSetProtection($bool)
	{
		$this->dbProtectionMode = $bool;
	}

	/**
	 * Protect data agains xss attack
	 *
	 * @param array $array
	 * @return array
	 * @since 3.2
	 */
	protected function dbProtection($array)
	{
		if($this->dbProtectionMode)
		{
			$tmp = array();
			foreach($array as $key => $val)
			{
				$val = str_replace('{', '&#123; ', $val);
				$val = str_replace('}', ' &#125;', $val);
				$tmp[$key] = $val;
			}
			$array = $tmp;
		}

		return $array;
	}


}

?>