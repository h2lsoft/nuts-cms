<?php

include('plugins/_news/config.inc.php');
$hidden_fields_arr = explode(',', str_replace(' ', '', $hidden_fields));

// get page information
if(count($this->plugin_args) == 0)$this->error404();
$newsID = (int)$this->plugin_args[0];
if($newsID == 0)$this->error404();

$sql_added = '';
/*if(!in_array('DateGMTExpiration', $hidden_fields_arr))
	$sql_added = "AND DateGMTExpiration > NOW()";*/

$this->doQuery("SELECT * $sql_front_added FROM NutsNews WHERE ID=$newsID AND Active = 'YES' $sql_added".$this->sqlAdded(true));

if($this->dbNumRows() == 0)$this->error404();
$this->vars = $this->dbFetch();

// create date array
$tmp = explode(' ', trim($this->vars['DateGMT']));

$this->vars['Date'] = $tmp[0];
list($this->vars['Date-Y'], $this->vars['Date-M'], $this->vars['Date-D']) = explode('-', $this->vars['Date']);
$this->vars['Date-y'] = substr($this->vars['Date-Y'], 2, 2);

/*$this->vars['Time'] = $tmp[1];
list($this->vars['Time-H'], $this->vars['Time-M'], $this->vars['Time-S']) = explode(':', $this->vars['Time']);
*/

// user define template ?
$this->openPluginTemplate();
$GLOBALS['NUTS_CONTENT'] = $this->output();



?>