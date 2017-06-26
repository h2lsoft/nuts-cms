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

	$str = str_replace(' I D', ' ID', $str);
	
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
	$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
	                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
	                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
	                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
	                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );

	$str = strtr($str, $unwanted_array);
	
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
	
	$regexp = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/i';
	// $regexp = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
	return preg_match($regexp, $email);

}