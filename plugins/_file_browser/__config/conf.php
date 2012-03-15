<?php

include('../../nuts/config.inc.php');
include('../../nuts/config_auto.inc.php');
session_start();
if(!isset($_SESSION["NutsUserID"]))die("Error: login required");


//------------------------------------------------------------------------------
// Configuration Variables
	
	// login to use QuiXplorer: (true/false)
	$GLOBALS["require_login"] = false;
	
	// language: (en, de, es, fr, nl, ru)
	$GLOBALS["language"] = $_SESSION['Language'];
	
	// the filename of the QuiXplorer script: (you rarely need to change this)
	//$GLOBALS["script_name"] = "http://".$GLOBALS['__SERVER']['HTTP_HOST'].$GLOBALS['__SERVER']["PHP_SELF"];
	$GLOBALS["script_name"] = WEBSITE_URL."/plugins/_file_browser/index.php";
	
	// allow Zip, Tar, TGz -> Only (experimental) Zip-support
	$GLOBALS["zip"] = true;	//function_exists("gzcompress");
	$GLOBALS["tar"] = false;
	$GLOBALS["tgz"] = false;
	
	// QuiXplorer version:
	$GLOBALS["version"] = "2.3";
//------------------------------------------------------------------------------
// Global User Variables (used when $require_login==false)
	
	// the home directory for the filemanager: (use '/', not '\' or '\\', no trailing '/')
	//$GLOBALS["home_dir"] = "/home/you/public_html";
	$GLOBALS["home_dir"] = WEBSITE_PATH;
	
	// the url corresponding with the home directory: (no trailing '/')
	$GLOBALS["home_url"] = WEBSITE_URL;
	
	// show hidden files in QuiXplorer: (hide files starting with '.', as in Linux/UNIX)
	$GLOBALS["show_hidden"] = true;
	
	// filenames not allowed to access: (uses PCRE regex syntax)
	$GLOBALS["no_access"] = "";
	
	// user permissions bitfield: (1=modify, 2=password, 4=admin, add the numbers)
	$GLOBALS["permissions"] = 7;
//------------------------------------------------------------------------------
/* NOTE:
	Users can be defined by using the Admin-section,
	or in the file "__config/.htusers.php".
	For more information about PCRE Regex Syntax,
	go to http://www.php.net/pcre.pattern.syntax
*/
//------------------------------------------------------------------------------
?>
