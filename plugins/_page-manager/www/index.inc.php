<?php

$pageID = $this->pageID;

// get page information
if($pageID == 0)$this->error404();
$this->doQuery("SELECT 
						NutsPage.*,
		
						UNIX_TIMESTAMP(NutsPage.DateCreation) AS DateCreationStamp,
						UNIX_TIMESTAMP(NutsPage.DateUpdate) AS DateUpdateStamp,

						NutsUser.FirstName AS AuthorFirstName,
						NutsUser.LastName AS AuthorLastName,
						NutsUser.Email AS AuthorEmail
				FROM
						NutsPage,
						NutsUser
				WHERE
						NutsPage.NutsUserID = NutsUser.ID AND
						NutsPage.ID = $pageID ".$this->sqlAdded(false, 'NutsPage.'));
if($this->dbNumRows() == 0)$this->error404();
$this->vars = $this->dbFetch();


// custom vars
$v = unserialize(trim($this->vars['CustomVars']));
$v2 = array();
if(is_array($v))
{
	foreach($v as $key => $val)
		$v2['cf'.$key] = $val;
}
$this->vars = array_merge($this->vars, $v2);

// custom block
$v = unserialize($this->vars['CustomBlock']);
$v2 = array('CustomBlock' => array());
if(is_array($v))
{
	foreach($v as $key => $val)
		$v2['CustomBlock'][$key] = $val;
}
$this->vars = array_merge($this->vars, $v2);




// user define template ?
$this->openPluginTemplate();
$GLOBALS['NUTS_CONTENT'] = $this->output();


?>