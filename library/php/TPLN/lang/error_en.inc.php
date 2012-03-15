<?php

// Error fatal [:FILE:], [:BLOC:], [:ITEM:] are replaced
$_err['0'] 	= "File <font color=\"#FF0000\"><b>[:FILE:]</b></font> not found";
$_err['1'] 	= "Item <font color=\"#0000FF\">{[:ITEM:]}</font> not found in file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['1.1'] 	= "Item <font color=\"#0000FF\">{[:ITEM:]}</font> not found in bloc <font color=\"#008000\">&lt;bloc::[:BLOC:]&gt;</font> of file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['2'] 	= "Bloc <font color=\"#008000\">&lt;bloc::[:BLOC:]&gt;</font> not found in file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['2.1'] 	= "Bloc <font color=\"#008000\">&lt;bloc::[:BLOC:]&gt;</font>is in double in file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['3'] 	= "Bloc <font color=\"#008000\">&lt;bloc::[:BLOC:]&gt;</font> not found in Bloc <font color=\"#0000FF\">{[:ITEM:]}</font> in file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['4'] 	= "Bloc path <font color=\"#0000FF\">[:BLOC:]</font> in file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['4.1'] 	= "Bloc path <font color=\"#0000FF\">[:BLOC:]</font> in Loop function of file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['4.2'] 	= "Bloc path <font color=\"#0000FF\">[:BLOC:]</font> is already looped in file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['5'] 	= "Can't define an empty bloc";
$_err['6'] 	= "Can't define an empty item";
$_err['7'] 	= "Can't create cache dir ".TPLN_CACHE_DIR;
$_err['7.1'] 	= "Can't create dir [:FILE:]";
$_err['7.2'] 	= "Can't create default templates directory ".TPLN_DEFAULT_PATH;
$_err['8'] 	= "Can't find end bloc <font color=\"#008000\">[:BLOC:]</font> in file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['9'] 	= "Template number [:FILE:] is not an integer";
$_err['10'] 	= "Template number [:FILE:] doesn't exist";
$_err['11'] 	= "Template name [:FILE:] doesn't defined";
$_err['12'] 	= 'DefineTemplate() must have an array in parameter';
$_err['13'] 	= "Your bloc path must have a father <font color=\"#008000\">[:BLOC:]</font> in file <font color=\"#FF0000\"><b>[:FILE:]</b></font>";

// DB error
$_err['DB']['0'] 		= "Database connection problem ([:MSG:])";
$_err['DB']['0.1']	= 'No connection found';
$_err['DB']['1'] 		= "Database close problem ([:MSG:])";
$_err['DB']['2'] 		= "Query problem ([:MSG:])";
$_err['DB']['2.1'] 	= "Change connection index ([:MSG:])";
$_err['DB']['2.11'] 	= "Change query index ([:MSG:])";
$_err['DB']['2.2'] 	= 'No query found';
$_err['DB']['2.3'] 	= 'Column number not valid';
$_err['DB']['3'] 		= 'SELECT not found in your query';
$_err['DB']['4'] 		= 'FROM not found in your query';
$_err['DB']['5'] 		= 'Showrecords() must have an integer in second parameter';
$_err['DB']['5.1'] 	= 'Showrecords() must have integer greater than zero in second parameter';





?>
