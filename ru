<?php

$a = explode('/', $_SERVER['REQUEST_URI']);
$tmp_lang = str_replace('/', '', $_SERVER['SCRIPT_NAME']);
include('language.inc.php');


?>