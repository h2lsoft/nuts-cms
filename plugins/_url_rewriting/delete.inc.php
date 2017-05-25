<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

$plugin->deleteDbTable(array('NutsUrlRewriting')); # put table here
$plugin->deleteRender();

if($_POST)
{
	include_once(PLUGIN_PATH.'/trt_url_rewriting.inc.php');
}

