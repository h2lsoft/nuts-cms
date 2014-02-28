<?php
/**
 * Array
 * @package Functions
 * @version 1.0
 */


/**
 * Transform an array to select
 *
 * @param array $options you can put multiple array for keys: label, value, optgroup
 * @param boolean $first_option_empty
 * @param string $select_name
 * @param string $attributes
 * @return string $select
 */
function array2select($options, $first_option_empty=true, $select_name="", $attributes=""){

	$select = "";
	$options_str = "";

	if($first_option_empty)
	{
		$options_str .= "<option value=\"\"></option>\n";
	}

	$last_optgroup = '';
	foreach($options as $option)
	{
		$selected = (!isset($option['selected'])) ? '' : 'selected="selected"';
		$value = $option['value'];
		$label = (!isset($option['label'])) ? $option['value'] : $option['label'];
		$optgroup = (!isset($option['optgroup'])) ? '' : $option['optgroup'];

		if(!empty($optgroup))
		{
			if($optgroup != $last_optgroup)
			{
				if(!empty($last_optgroup))
					$options_str .= "</optgroup>\n";

				$options_str .= "<optgroup label=\"$optgroup\">\n";
				$last_optgroup = $optgroup;
			}
		}

		$options_str .= "<option value=\"$value\" $selected>$label</option>\n";
	}

	if(!empty($last_optgroup))$options_str .= "</optgroup>\n";

	if(!empty($select_name))
	{
		if(!empty($attributes))$attributes .= ' '.$attributes;
		$select = "<select name=\"$select_name\" id=\"$select_name\"$attributes>\n";
		$select .= $options_str;
		$select .= "</select>\n";
	}
	else
	{
		$select = $options_str;
	}


	return $select;

}

/**
 * Convert an array to csv
 *
 * @param array $array your array
 * @param type $downloadable is file is for download ?
 * @param type $download_filename
 */
function array2csv($array, $downloadable=false, $download_filename='')
{
	$content = "";

	// lines
	$init = false;
	for($i=0; $i < count($array); $i++)
	{
		$line = $array[$i];

		if(!$init)
		{
			foreach($line as $key => $val)
			{
				$key = str_replace(';', ' ', $key);
				$content .= $key.';';
				$init = true;
			}

			$content .= CR;
		}

		foreach($line as $key => $val)
		{
			$val = str_replace(';', ',', $val);
			$val = str_replace(CR, '\n', $val);

			$content .= $val.';';
		}

		$content .= CR;
	}

	if(!$downloadable)
	{
		return $content;
	}
	else
	{
		if(empty($download_filename))
			$download_filename = date('Ymd').'.csv';

		// required for IE, otherwise Content-disposition is ignored
		if(@ini_get('zlib.output_compression'))@ini_set('zlib.output_compression', 'Off');

		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false); // required for certain browsers
		header("Content-Type: application/force-download; charset=utf-8");
		header("Content-Disposition: attachment; filename=\"".basename($download_filename)."\";");
		header("Content-Transfer-Encoding: binary");

		echo $content;
		exit;

	}


}

/**
 * Convert array to html table
 *
 * @param array $rows
 * @param array $headers_labels (optional) replace text of a th text
 * @param string $headers_style (optional) add style to th and td, example text-align:center
 * @param string $table_attributes (optional) add attibutes in table node, example border="1"
 * @param string $td_colors1 (optional) change color for td even (default: #e5e5e5)
 * @param string $td_colors2 (optional) change color for td odd (default: #ffffff)
 *
 * @return string html table formatted
 */
function array2table($rows, $headers_labels=array(), $headers_style=array(), $table_attributes="", $table_styles="",  $td_colors1='#e5e5e5', $td_colors2='#ffffff')
{
	if(!count($rows))return "";


	$str = "<table $table_attributes style=\"$table_styles\">";

	$init = false;
	$i = 0;
	foreach($rows as $row)
	{
		if(!$init)
		{
			$headers = array_keys($row);

			$str .= '<thead>';
			$str .= '<tr>';
			foreach($headers as $header)
			{
				$header_label = $header;
				if(isset($headers_labels[$header]))
					$header_label = $headers_labels[$header];

				$str .= '	<th style="'.@$headers_style[$header].'">'.$header_label.'&nbsp;</th>';
			}

			$str .= '</tr>';
			$str .= '</thead>';
			$str .= '<tbody>';

			$init = true;
		}

		$str .= '<tr>';
		$td_color = ($i % 2 == 0) ? $td_colors1 : $td_colors2;
		foreach($headers as $header)
		{
			$td_style = @$headers_style[$header];
			$td_style = "background-color: $td_color; $td_style";
			$str .= '	<td style="'.$td_style.'">'.$row[$header].'&nbsp;</td>';

		}
		$str .= '</tr>';

		$i++;
	}

	$str .= '</tbody>';
	$str .= "</table>";


	return $str;
}

/**
 * Convert the array into an array well structured
 *
 * @param array $arr
 *
 * @return array result formated
 */
function convertArrayForFormSelect($arr)
{
	$arrReturn   = array();
	foreach($arr as $key => $val)
	{
		$arrReturn[] = array('value' => $key, 'label' => $val);
	}
	return $arrReturn;
}

/**
 * Transform Csv file to structured array
 *
 * @param $file_name
 * @param string $separator  (default = `;`)
 * @param bool $ignore_first_line  (default = true)
 * @param bool $first_line_as_key  (default = false)
 * @param bool $encode_utf8 (default = false)
 *
 * @return array
 */
function csv2array($file_name, $separator=';', $ignore_first_line=true, $first_line_as_key=false, $encode_utf8=false)
{
	$arr = array();
	$keys = array();

	$init = false;
	$lines = file($file_name);
	foreach($lines as $line)
	{
		$cols = explode($separator, $line);
		$cols = array_map('trim', $cols);
		if($encode_utf8)$cols = array_map('utf8_encode', $cols);
		if($ignore_first_line && !$init)
		{
			if($first_line_as_key)
			{
				$keys = array_map('toCamelCase', $cols);
				$keys = array_map('str_replace_latin_accents', $keys);
			}
		}

		if($init || !$ignore_first_line)
		{
			if($first_line_as_key)
			{
				$tmp = array();
				$i = 0;
				foreach($keys as $key)
				{
					$tmp[$key] = $cols[$i];
					$i++;
				}

				$arr[] = $tmp;
			}
			else
			{
				$arr[] = $cols;
			}


		}

		$init = true;
	}


	return $arr;
}

/**
 * Flatten array
 *
 * @param array $a
 * @return array
 */
function array_flatten($array, $return=array())
{
	for($x = 0; $x <= count($array); $x++)
	{
		if(is_array(@$array[$x]))
		{
			$return = array_flatten($array[$x], $return);
		}
		else
		{
			if(@$array[$x])
			{
				$return[] = $array[$x];
			}
		}
	}
	return $return;
}