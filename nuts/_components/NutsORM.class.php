<?php
/**
 * Nuts ORM - simple ORM factory for Nuts
 *
 * @package Nuts-Component
 * @version 2.0
 * @date 21/03/2012
 */
class NutsORM
{
	protected $_table_name;
	protected $_data;
	protected $_is_new;
	protected $_DBLink = false;

	public $forbidden_key = array(); # forbiddden key to exclude in query

	private $query_cons; # query contructor
	public $last_query = "";

	/**
	 * Private constructor; shouldn't be called directly.
	 * Use the NutsORM::factory method instead.
	 *
	 * @param $table_name
	 * @param array $data
	 */
	protected function __construct($table_name) {

		if(!$this->_DBLink)
		{
			if(isset($GLOBALS['nuts']))$this->_DBLink = $GLOBALS['nuts'];
			elseif(isset($GLOBALS['job']))$this->_DBLink = $GLOBALS['job'];
			else {die("Error: DB link not found");}
		}

		$this->_table_name = $table_name;
		$this->_is_new = true;
	}

	/**
	 * Set magic
	 * @param $key string
	 * @param $value string
	 */
	public function __set($key, $value) {
		$this->_data[$key] = $value;
	}

	/**
	 * Get magic
	 * @param $key string
	 * @return mixed string
	 */
	public function __get($key) {
		return $this->_data[$key];
	}

	/**
	 * @static Factory
	 * @param $table_name string
	 */
	public static function factory($table_name){
		return new self($table_name);
	}

	/**
	 * Save current record
	 * @param $force_creation
	 */
	public function save($force_creation=false){

		if($force_creation)$this->_is_new = true;

		$data = $this->_data;

		if($this->_is_new)
		{
			if(isset($data['ID']))$data['ID'] = 0;
			$ID = $this->_DBLink->dbInsert($this->_table_name, $data, $this->forbidden_key, true);
			$this->_is_new = false;
			$this->_data['ID'] = $ID;
		}
		else
		{
			$this->_DBLink->dbUpdate($this->_table_name, $data, "ID={$this->_data['ID']}", $this->forbidden_key);
		}

	}

    /**
     * Force update on a record
     */
    public function update(){

        if(!isset($this->_data['ID'])){
            trigger_error("Oject has no ID value", E_USER_WARNING);
            return;
        }

        $this->_is_new = false;
        $this->save();
    }


	/**
	 * Delete current record and reinit data parameter
	 */
	public function delete(){

		if(!isset($this->_data['ID'])){
			trigger_error("Oject has no ID value", E_USER_WARNING);
			return;
		}

		$curID = $this->_data['ID'];
		$this->_DBLink->dbUpdate($this->_table_name, array('Deleted' => "YES"), "ID=$curID");
		$this->_is_new = true;
		$this->_data = array();
	}


}












?>