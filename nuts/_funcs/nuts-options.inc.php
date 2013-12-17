<?php

/**
 * Is website is multilang configured
 * @return boolean
 */
function isWebsiteMultiLang()
{
	$GLOBALS['nuts']->doQuery("SELECT Languages FROM NutsTemplateConfiguration");
	$lng = $GLOBALS['nuts']->getOne();

	$lng = str_replace(' ', '', $lng);
	$lng = trim($lng);

	if(!empty($lng))
		return true;

	return false;

}

/**
 * Return current defined theme
 *
 * @return $theme
 */
function nutsGetTheme()
{
	/*$GLOBALS['nuts']->doQuery("SELECT Theme FROM NutsTemplateConfiguration");
	$theme = $GLOBALS['nuts']->getOne();*/

	$theme = $GLOBALS['nuts_theme_selected'];

	return $theme;
}

/**
 * Return nuts distinct content type
 *
 * @param string $type select by default
 * @return $values
 */
function nutsGetOptionsContentType($type='select')
{
	$GLOBALS['nuts']->doQuery("SELECT Type FROM NutsContentType");
	$rows = $GLOBALS['nuts']->getData();
	if($type == 'select')
	{
		$options = '';
		foreach($rows as $type)
		{
			$options .= '<option value="'.$type['Type'].'">'.$type['Type'].'</option>'."\n";
		}

		return $options;
	}
}

/**
 * Get nuts users list
 *
 * @param type $type default = select
 * @return $values
 */
function nutsGetOptionsUsers($type='select')
{
	$GLOBALS['nuts']->doQuery("SELECT
										ID,
										CONCAT_WS(' ',UPPER(LastName), FirstName) AS Name,
										Deleted
								FROM
										NutsUser
								ORDER BY
										Deleted DESC");


	$rows = $GLOBALS['nuts']->getData();
	if($type == 'select')
	{
		$options = '';
		foreach($rows as $user)
		{
			$style = '';
			if(strtoupper($user['Deleted']) == 'YES')
				$style = ' style="background-color:red;"';

			$options .= '<option value="'.$user['ID'].'"'.$style.'">'.$user['Name'].'</option>'."\n";
		}

		return $options;
	}
}

/**
 * Get nuts distinct theme defined
 *
 * @param string $type default= select
 * @return $options
 */
function nutsGetOptionsTemplates($type='select')
{
	/*$GLOBALS['nuts']->doQuery("SELECT
										Theme
								FROM
										NutsTemplateConfiguration");
	$theme = $GLOBALS['nuts']->getOne();*/

	$theme = nutsGetTheme();

	$tpls = (array)glob(NUTS_THEMES_PATH.'/'.$theme.'/*.html');

	if($type == 'select')
	{
		$options = '';
		foreach($tpls as $tpl)
		{
			$tpl = str_replace(NUTS_THEMES_PATH.'/'.$theme.'/', '', $tpl);
			if($tpl != 'index.html' && $tpl[0] != '_')
			{
				$options .= '<option value="'.$tpl.'">'.$tpl.'</option>'."\n";
			}
		}
		return $options;
	}
}

/**
 * get all header image defined in folder library/images/header
 *
 * @param string $type default select
 * @return $values
 */
function nutsGetOptionsHeaderImage($type='select')
{
	$imgs = (array)glob(NUTS_HEADER_IMAGES_PATH.'/*.*');
	if($type == 'select')
	{
		$options = '';
		foreach($imgs as $img)
		{
			$img = str_replace(NUTS_HEADER_IMAGES_PATH.'/', '', $img);
			$ext = substr($img, strrpos($img, '.') + 1);
			if(in_array($ext, array('gif', 'png', 'jpg', 'swf')))
			{
				$options .= '<option value="'.$img.'">'.$img.'</option>'."\n";
			}
		}
		return $options;
	}
}

/**
 * Get nuts distinct languages defined
 *
 * @param string $type default= select
 * @return array|string $values
 */
function nutsGetOptionsLanguages($type='select')
{

	$GLOBALS['nuts']->doQuery("SELECT
										LanguageDefault,
										Languages
								FROM
										NutsTemplateConfiguration");
	$row = $GLOBALS['nuts']->dbFetch();
	$tab = explode(',', trim($row['Languages']));
	$tab = array_map('trim', $tab);

	if($type == 'select')
	{
		$options = '<option value="'.strtolower($row['LanguageDefault']).'">'.nutsGetLanguage(strtolower($row['LanguageDefault'])).'</option>'."\n";
		foreach($tab as $t)
			if(!empty($t))
				$options .= '<option value="'.$t.'">'.nutsGetLanguage($t).'</option>'."\n";
	}
	elseif($type == 'array')
	{
		return $tab;
	}

	return $options;

}

/**
 * Get nuts language by iso code
 *
 * @param string $initials
 * @return string language
 */
function nutsGetLanguage($initials)
{
	global $nuts_lang_options;

	foreach($nuts_lang_options as $n)
	{
		if($n['value'] == strtolower($initials))
		{
			return $n['label'];
		}
	}
}

/**
 * Get selected language by default
 *
 * @return string $language
 */
function nutsGetDefaultLanguage()
{
	$GLOBALS['nuts']->doQuery("SELECT
										LanguageDefault
							FROM
									NutsTemplateConfiguration");
	$lng = $GLOBALS['nuts']->getOne();
	return $lng;
}

/**
 * Get distinct zone defined by user
 *
 * @return array zone
 */
function nutsGetOptionsMenu()
{
	global $nuts_lang_msg;
	$options = '<option value="0">'.$nuts_lang_msg[41].'</option>'."\n";

	$GLOBALS['nuts']->doQuery("SELECT
										ID,
										Name
								FROM
										NutsZone
								WHERE
										Type = 'MENU' AND
										Deleted = 'NO'");
	while($row = $GLOBALS['nuts']->dbFetch())
		$options .= '<option value="'.$row['ID'].'">'.$row['Name'].'</option>'."\n";

	return $options;
}

/**
 * get list fot tinymce spellchecker language
 */
function nutsGetSpellcheckerLanguages()
{
	global $nuts_lang_options;

	$str = '';
	$init = false;
	foreach($nuts_lang_options as $lng)
	{
		if(!empty($str))$str .= ',';

		$add = '';
		if(isset($_GET['lang']) && $_GET['lang'] == $lng['value'])
		{
			$add = '+';
			$init = true;
		}

		$str .= "$add{$lng['label']}={$lng['value']}";
	}

	if(!$init)
		$str = '+'.$str;

	return $str;
}