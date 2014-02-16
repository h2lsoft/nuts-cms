<?php
/**
 * String
 * @package Functions
 * @version 1.0
 */

/**
 * Format a float to be well displayed ex: 1234.5678 => 1 234.57
 * @param float $num
 * @return float
 */
function number_formatX($num)
{
	$num = number_format($num, 2, '.', ' ');
	return $num;
}

/**
 * Format a float to be well displayed ex: 1234 => 1 234
 * @param float $int
 * @return float
 */
function int_formatX($int)
{
	$int = number_format($int, 0, '.', ' ');
	return $int;
}

/**
 * Convert string strtolower and ucfirst
 * @param $str
 *
 * @return string
 */
function ucFirstLower($str)
{
	$str = mb_strtolower($str, 'utf-8');
	$str = ucfirst($str);

	return $str;
}

/**
 * Translates a camel case string into a string
 *
 * @param string $str String in camel case format
 * @param boolean $ucwords apply ucwords
 *
 * @return string $str Translated
 */
function fromCamelCase($str, $ucwords=true)
{
	$func = create_function('$c', 'return " " . $c[1];');
	$str = preg_replace_callback('/([A-Z])/', $func, $str);

	if($ucwords)$str = ucwords($str);

	return $str;
}

/**
 * Translates a string with underscores into camel case (e.g. first name -&gt; firstName)
 *
 * @param string $str String in underscore format
 * @param boolean $capitalize_first_char (If true (default), capitalise the first char in $str)
 *
 * @return string $str translated into camel caps
 */
function toCamelCase($str, $capitalize_first_char=true)
{
	if($capitalize_first_char)$str[0] = strtoupper($str[0]);

	$func = create_function('$c', 'return strtoupper($c[1]);');
	return preg_replace_callback('/ ([a-z])/', $func, $str);
}

/**
 * Convert a string to url rewrited
 *
 * @param string $str
 * @param boolean $convert_slashes (default true)
 * @param boolean $added_patterns (default empty)
 * @param boolean $added_replaces (default empty)
 * @return string
 */
function strtouri($str, $convert_slashes=true, $added_patterns=array(), $added_replaces=array())
{
	$str = trim($str);
	$str = mb_strtolower($str, 'utf8');

	$str = str_replace(array('é', 'ê', 'è', 'ë'), 'e', $str);
	$str = str_replace(array('à', 'ä', 'â'), 'a', $str);
	$str = str_replace(array('ô', 'ö', 'ô'), 'o', $str);
	$str = str_replace(array('î', 'ï'), 'i', $str);
	$str = str_replace("ù", 'u', $str);
	$str = str_replace("ç", 'c', $str);

	$str = str_replace(array(' ',"(", ")", '"', "'", '-', '%', ), '_', $str);
	$str = str_replace('?', '', $str);

	if($convert_slashes)$str = str_replace('/', '-', $str);

	$str = str_replace('___', '_', $str);
	$str = str_replace('__', '_', $str);
	$str = str_replace('..', '.', $str);
	$str = str_replace('--', '-', $str);
	$str = str_replace('-.', '.', $str);

	if(count($added_patterns))
		$str = str_replace($added_patterns, $added_replaces, $str);

	return $str;
}

/**
 * Cut a string and concat (Warning UTF-8 uses mb_* function and strip_tags is applied before)
 *
 * @param type $str
 * @param type $max_caracters (default 80)
 * @param type $concat_str
 *
 * return string cutted string
 */
function str_cut($str, $max_caracters=80, $concat_str='...'){

	$str = strip_tags($str);
	$str2 = mb_strcut($str, 0, $max_caracters, 'UTF-8');
	$str2 = trim($str2);
	if(mb_strlen($str) > $max_caracters)
	{
		$str2 .= $concat_str;
	}

	return $str2;
}

/**
 * Convert a string to pascal case litteral (obsolete: use fromCamelCase instead)
 *
 * @param string string
 * @return string pascal case myCase => My case
 *
 */
function toPascalCase($str)
{
	$str = preg_replace('/(?<=[a-z])(?=[A-Z])/',' ', $str);

	$str = strtolower($str);
	$str = trim($str);
	$str = ucwords($str);

	return $str;
}

/**
 * Replace latin accent like éèêë by e for example
 *
 * @param $str
 * @return string
 */
function str_replace_latin_accents($str)
{

	$reps = array();
	$reps[] = array(
		'pattern' => array('é', 'è', 'ê', 'ë'),
		'replacement' => 'e'
	);
	$reps[] = array(
		'pattern' => array('à', 'â', 'ä', 'â'),
		'replacement' => 'a'
	);

	$reps[] = array(
		'pattern' => array('ç'),
		'replacement' => 'c'
	);


	$reps[] = array(
		'pattern' => array('ÿ'),
		'replacement' => 'y'
	);

	$reps[] = array(
		'pattern' => array('û', 'ü', 'ù'),
		'replacement' => 'u'
	);

	$reps[] = array(
		'pattern' => array('î', 'ï'),
		'replacement' => 'i'
	);

	$reps[] = array(
		'pattern' => array('ö', 'ô'),
		'replacement' => 'o'
	);


	foreach($reps as $rep)
	{
		$str = str_replace($rep['pattern'], $rep['replacement'], $str);

		$rep['pattern'] = array_map('strtoupper', $rep['pattern']);
		$str = str_replace($rep['pattern'], strtoupper($rep['replacement']), $str);
	}


	return $str;
}

/**
 * Erase caracters from string
 *
 * @param string|array $patterns
 * @param $str
 *
 * @return mixed
 */
function str_erase($patterns, $str)
{
	return str_replace($patterns, '', $str);
}

/**
 * Protect sql paramater against Xss attacks
 *
 * @param $str
 * @return string
 */
function sqlX($str)
{
	return strtr($str, array("\x00" => '\x00', "\n" => '\n', "\r" => '\r', '\\' => '\\\\', "'" => "\'", '"' => '\"', "\x1a" => '\x1a'));
}

/**
 * Verify if is correct email
 *
 * @param string $email
 * @return boolean
 */
function email($email)
{
	// $pattern = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#' ;
	$pattern = "/^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+.)+[a-zA-Z]{2,5}$/";
	return preg_match($pattern, $email);

}