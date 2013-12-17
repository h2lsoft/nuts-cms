<?php
/**
 * Form for Front Office plugin
 *
 * @package Nuts-Component
 * @version 1.0
 */

/**
* Create a new field type text
*
* @param $name
* @param $label
* @param $style
* @param $value
* @param $help
* @param $text_after
* @param $hr_after
* @return string
*/
function nutsFormAddText($name, $label, $style, $value, $help, $text_after, $hr_after, $p_class)
{
$hr_after = ($hr_after == 'YES') ? '<hr />' : '';

$str = <<<EOF

<p class="$p_class">
	<label title="$help">$label</label>
	<input type="text" name="$name" id="$name" value="$value" style="$style" /> $text_after
</p>

$hr_after

EOF;

return $str;
}

/**
* Create a new field textarea
*
* @param $name
* @param $label
* @param $style
* @param $value
* @param $help
* @param $hr_after
* @param $p_class
*
* @return string
*/
function nutsFormAddTextarea($name, $label, $style, $value, $help, $hr_after, $p_class)
{
$hr_after = ($hr_after == 'YES') ? '<hr />' : '';

$str = <<<EOF
<p class="$p_class">
	<label title="$help">$label</label>
	<textarea name="$name" id="$name" style="$style">$value</textarea>
</p>
$hr_after

EOF;


return $str;
}

/**
* Create a new field htmlarea
*
* @param $name
* @param $label
* @param $style
* @param $value
* @param $help
* @param $hr_after
* @param $p_class
*
* @return string
*/
function nutsFormAddHtmlArea($name, $label, $style, $value, $help, $hr_after, $p_class)
{
$hr_after = ($hr_after == 'YES') ? '<hr />' : '';

$style = " width:86%; $style";

$str = <<<EOF
<p class="$p_class">
	<label title="$help">$label</label>
	<textarea name="$name" id="$name" style="$style" class="mceEditor processed">$value</textarea>
</p>
$hr_after

EOF;


return $str;
}

/**
* Create a new field type colorpicker
*
* @param $name
* @param $label
* @param $style
* @param $value
* @param $help
* @param $hr_after
* @param $p_class
*
* @return string
*/
function nutsFormAddColorpicker($name, $label, $style, $value, $help, $hr_after, $p_class)
{
$hr_after = ($hr_after == 'YES') ? '<hr />' : '';

$str = <<<EOF

<p class="$p_class">
	<label title="$help">$label</label>

<div id="{$name}_colorpicker_preview" class="widget_colorpicker_preview">&nbsp;</div>
<input type="text" name="$name" id="$name" value="$value" style="width:50px; $style" maxlength="7" class="widget_colorpicker"  />

</p>

$hr_after

EOF;

return $str;
}

/**
* Create a new field type date or datetime
*
* @param $name
* @param $label
* @param $type (date or datetime)
* @param $value
* @param $help
* @param $hr_after
* @return string
*/
function nutsFormAddDate($name, $label, $type, $value, $help, $hr_after, $p_class)
{
$hr_after = ($hr_after == 'YES') ? '<hr />' : '';

$str = <<<EOF

<p class="$p_class">
	<label title="$help">$label</label>
	<input autocomplete="off" type="text" id="$name" name="$name" value="$value" />
	<script>inputDate('$name', '$type');</script>
</p>

$hr_after

EOF;

return $str;
}

/**
* Create a new field type boolean or booleanx
*
* @param $name
* @param $label
* @param $type (boolean or booleanx)
* @param $help
* @param $hr_after
* @return string
*/
function nutsFormAddBoolean($name, $label, $type, $help, $hr_after, $p_class)
{
global $nuts_lang_msg;

$hr_after = ($hr_after == 'YES') ? '<hr />' : '';
if($type == 'boolean')
{
$yes_selected = 'selected';
$no_selected = '';
}
else
{
$yes_selected = '';
$no_selected = 'selected';
}

$str = <<<EOF

<p class="$p_class">
	<label title="$help">$label</label>
	<select id="$name" name="$name">
		<option value="YES" $yes_selected>{$nuts_lang_msg[30]}</option>
		<option value="NO" $no_selected>{$nuts_lang_msg[31]}</option>
	</select>

</p>

$hr_after

EOF;

return $str;
}

/**
* Create a new field type filemanager_media or filemanager_file or filemanager
*
* @param $name
* @param $label
* @param $type (filemanager_media or filemanager_file or filemanager)
* @param $value
* @param $folder
* @param $style
* @param $help
* @param $hr_after
*
* @return string
*/
function nutsFormAddFilemanager($name, $label, $type, $value, $folder, $style, $help, $hr_after, $p_class)
{
global $nuts_lang_msg;

$hr_after = ($hr_after == 'YES') ? '<hr />' : '';


$js_func = 'allBrowser';
$js_func_image = 'icon-file.png';

if($type == 'filemanager_image')
{
$js_func = 'imgBrowser';
$js_func_image = 'icon-preview-mini.gif';
}
elseif($type == 'filemanager_media')
{
$js_func = 'mediaBrowser';
$js_func_image = 'icon-media.png';
}

$str = <<<EOF

<p class="$p_class">
	<label title="$help">$label</label>
	<input autocomplete="off" type="text" id="$name" name="$name" value="$value" style="$style" />


	<a href="javascript:;" tabindex="-1" class="tt" title="{$nuts_lang_msg[87]}" onclick="$js_func('$name','$folder');"><img class="icon" align="absmiddle" src="/nuts/img/icon-folder.png"/></a>
	<a href="javascript:;" tabindex="-1" class="tt" title="{$nuts_lang_msg[86]}" onclick="openFile('$name');"><img class="icon" align="absmiddle" src="/nuts/img/$js_func_image"/></a>
</p>

$hr_after

EOF;

return $str;
}

/**
* Create a new field type select
*
* @param $name
* @param $label
* @param $options
* @param $value
* @param $style
* @param $help
* @param $hr_after
* @param $p_class
*
* @return string
*/
function nutsFormAddSelect($name, $label, $options, $value, $style, $help, $hr_after, $p_class)
{
$hr_after = ($hr_after == 'YES') ? '<hr />' : '';


$cur_options = explode("\n", $options);
$select_options = "";
foreach($cur_options as $cur_option)
{
$selected = "";

$cur_vals = explode('|', $cur_option);

if(count($cur_vals) == 2)
{
$v = $cur_vals[0];
$l = $cur_vals[1];
}
else
{
$v = $l = $cur_option;
}

if(!empty($value) && $v == $value)
{
$selected = ' selected';
}
$select_options .= '<option value="'.$v.'" '.$selected.'>'.$l.'</option>'.CR;

}

$str = <<<EOF

<p class="$p_class">
	<label title="$help">$label</label>

	<select id="$name" name="$name" style="$style">
		$select_options
	</select>

</p>

$hr_after

EOF;

return $str;
}

/**
* Create a new field type select-sql
*
* @param $name
* @param $label
* @param $sql
* @param $value
* @param $style
* @param $help
* @param $hr_after
* @param $p_class
*
* @return string
*/
function nutsFormAddSelectSql($name, $label, $sql, $value, $style, $help, $hr_after, $p_class)
{
global $nuts;

$hr_after = ($hr_after == 'YES') ? '<hr />' : '';


$nuts->doQuery($sql);

$select_options = "";
$select_options .= "<option value=\"\"></option>".CR;
while($r = $nuts->dbFetch())
{
$selected = ($r['value'] == $value) ? 'selected' : '';
$select_options .= "<option value=\"{$r['value']}\" $selected>{$r['label']}</option>".CR;
}


$str = <<<EOF

<p class="$p_class">
	<label title="$help">$label</label>

	<select id="$name" name="$name" style="$style">
		$select_options
	</select>

</p>

$hr_after

EOF;

return $str;
}