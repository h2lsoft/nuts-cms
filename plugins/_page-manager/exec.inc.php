<?php

/* @var $nuts NutsCore */

$select_language = nutsGetOptionsLanguages();
$select_zone_id = nutsGetOptionsMenu();
$select_user = nutsGetOptionsUsers();
$select_tpls = nutsGetOptionsTemplates();
$select_header_img = nutsGetOptionsHeaderImage();
$select_content_type = nutsGetOptionsContentType();
$theme = nutsGetTheme();

// page content view
$recs = Query::factory()->select('ID, Name')->from('NutsPageContentView')->order_by('Name')->executeAndGetAll();
$select_page_content_views = "";
foreach($recs as $rec)
{
    $select_page_content_views .= "<option value=\"{$rec['ID']}\">{$rec['Name']}</option>".CR;
}


include(PLUGIN_PATH.'/config.inc.php');
include(PLUGIN_PATH.'/actions.inc.php');

$nuts->open(PLUGIN_PATH.'/form.html');

// rights matrix ******************************************************************************************

// groups allowed in page manager
$groups = Query::factory()->select('ID, Name')
						  ->from('NutsGroup')
						  ->whereEqualTo('BackofficeAccess', 'YES')
						  ->where('ID', 'IN', "(SELECT NutsGroupID FROM NutsMenuRight WHERE NutsGroupID = NutsGroup.ID AND NutsMenuID = (SELECT ID FROM NutsMenu WHERE Name = '_page-manager' AND Deleted = 'NO'))")
						  ->order_by('Priority ASC')
						  ->executeAndGetAll();

foreach($groups as $group)
{
	$nuts->parse('right_matrix.GroupID', $group['ID']);
	$nuts->parse('right_matrix.GroupName', $group['Name']);
	$nuts->loop('right_matrix');
}





// custom vars ******************************************************************************************
if(count($custom_fields) == 0)
{
	$nuts->eraseBloc('custom_fields');
}
else
{
	foreach($custom_fields as $c)
	{
		if(!isset($c['label']))$c['label']  = $c['name'];

		// convert pascal case to normal
		$replacements = array();
		for($i=0; $i < strlen($c['label']); $i++)
		{
			if(strtoupper($c['label'][$i]) == $c['label'][$i])
				$replacements[] = $c['label'][$i];
		}
		for($i=0; $i < count($replacements); $i++)
			 $c['label'] = str_replace($replacements[$i], ' '.$replacements[$i], $c['label']);
		$c['label'] = trim($c['label']);


		$nuts->parse('custom_fields.custom_fields_label', $c['label']);
		// $nuts->parse('custom_fields.custom_fields_name', $c['name']);

		// tooltip
		if(!isset($c['help']))$c['help'] = '';
		$nuts->parse('custom_fields.custom_fields_help', $c['help']);

		$field_types = array('text', 'textarea', 'select', 'image', 'file', 'boolean', 'booleanX', 'date', 'datetime', 'colorpicker');
		$except = $c['type'];

		$nuts->parse('custom_fields.custom_fields_'.$c['type'].'.custom_fields_name', $c['name']);



		if($c['type'] == 'text' || $c['type'] == 'colorpicker')
		{

		}
		elseif($c['type'] == 'textarea')
		{

		}
		elseif($c['type'] == 'image' || $c['type'] == 'file')
		{
			if(!isset($c['folder']))$c['folder'] = '';
			$nuts->parse('custom_fields.custom_fields_'.$c['type'].'.custom_fields_folder', $c['folder']);
		}
		elseif($c['type'] == 'select')
		{
			if(count($c['options']) == 0)
			{
				$nuts->eraseBloc('custom_fields.custom_fields_select.custom_fields_select_options');
			}
			else
			{
				foreach($c['options'] as $opt)
				{
					if(is_array($opt))
					{
						if(isset($opt['value']) && isset($opt['label']))
						{
							$label = $opt['label'];
							$value = $opt['value'];
						}
						else
						{
							$label = $opt[0];
							$value = (count($opt) > 1) ? $opt[1] : $opt[0];
						}
					}
					else
					{
						$label = $opt;
						$value = $opt;
					}

					$nuts->parse('custom_fields.custom_fields_select.custom_fields_select_options.custom_fields_opt', $opt);
					$nuts->loop('custom_fields.custom_fields_select.custom_fields_select_options');
				}
			}
		}

		// erase other types
		foreach($field_types as $ft)
		{
			if($except != $ft)
				$nuts->eraseBloc('custom_fields.custom_fields_'.$ft);

			$nuts->loop('custom_fields.custom_fields_'.$ft);
		}

		$nuts->loop('custom_fields');


	}
}

// custom block ***********************************************************************************
/*$nuts->doQuery("SELECT
						DISTINCT GroupName
				FROM
						NutsBlock
				WHERE
						Deleted = 'NO'
				ORDER BY
						GroupName");
$custom_blocks = $nuts->getData();*/
$custom_blocks = &$allowed_groups_block;
$block_preview = array();


if(count($custom_blocks) == 0)
{
	$nuts->parseBloc('custom_blocks', $lang_msg[64]);
}
else
{

	foreach($custom_blocks as $custom_block)
	{
		$nuts->parse('custom_blocks.custom_block_nameX', $custom_block, '|toPascalCase');
		$nuts->parse('custom_blocks.custom_block_name', $custom_block);


		// load every lock by name
		$nuts->doQuery("SELECT
								ID,
								SubGroupName,
								Name,
								Preview
						FROM
								NutsBlock
						WHERE
								Deleted = 'NO' AND
								GroupName = '".addslashes($custom_block)."'
						ORDER BY
								SubGroupName,
								Name");
		$vals = $nuts->getData();
		if(count($vals) == 0)
		{
			$nuts->eraseBloc('custom_blocks.custom_block_options');
		}
		else
		{
			foreach($vals as $val)
			{
				// block preview url
				if(empty($val['Preview']))$val['Preview'] = '/nuts/img/no-preview.png';

				$nuts->parse('custom_blocks.custom_block_options.image_preview', $val['Preview']);
				$nuts->parse('custom_blocks.custom_block_options.custom_block_val', $val['ID']);
				$nuts->parse('custom_blocks.custom_block_options.custom_block_val_name', $val['SubGroupName'].'> '.$val['Name'], '|toPascalCase');
				$nuts->loop('custom_blocks.custom_block_options');

				$block_preview[$val['ID']] = $val['Preview'];
			}
		}

		$nuts->loop('custom_blocks');
	}
}


$nuts->parse('page_manager_block_preview_url', json_encode($block_preview));


// current templates in themes
$tpls_library_path = NUTS_THEMES_PATH.'/'.$theme;
$tpls = (array)glob($tpls_library_path.'/*.html');

$tpls = array_merge(array($tpls_library_path.'/index.html'), $tpls);

$init = false;
$cur_theme = nutsGetTheme();
foreach($tpls as $tpl)
{
	$tpl_name = str_replace($tpls_library_path.'/', '', $tpl);
	$tpl_name_file = $tpl_name;
	$tpl_name = str_replace('.html', '', $tpl_name);
	$tpl_nameX = ucfirst($tpl_name);
	$tpl_nameX = str_replace('-', ' ', $tpl_nameX);

	if($tpl_name[0] != '_' && (($init && $tpl_name != 'index') || (!$init && $tpl_name == 'index')))
	{
		$nuts->parse('tpls_preview.theme', $theme);

		// image preview
		$tpl_preview = WEBSITE_PATH.'/themes/'.$cur_theme.'/_preview/'.$tpl_name.'.png';
		if(!file_exists($tpl_preview))
			$tpl_preview = WEBSITE_PATH.'/_preview/no-preview.png';
		$tpl_preview = basename($tpl_preview);
		$nuts->parse('tpls_preview.tpl_preview', $tpl_preview);

		if($tpl_name == 'index')
		{
			$tpl_nameX = $nuts_lang_msg[75];
			$tpl_name_sel = '';
			$tpl_name_file = '';
		}

		$nuts->parse('tpls_preview.tpl_name_file', $tpl_name_file);
		$nuts->parse('tpls_preview.tpl_nameX', $tpl_nameX);

		$nuts->loop('tpls_preview');

		$init = true;
	}
}


// content views *******************************************************************************************************
$views = Query::factory()->select('ID')->from('NutsPageContentView')->order_by('Name')->executeAndGetAllOne();
$nuts_views_form = "";
foreach($views as $viewID)
{
    $view_fields =  Query::factory()->select('*')->from('NutsPageContentViewField')->where('NutsPageContentViewID', '=', $viewID)->order_by('Position')->executeAndGetAll();


    $nuts_views_form .= '<div class="content_view_wrapper" id="content_view_wrapper_'.$viewID.'">';
    $p_class = 'content_view content_view_'.$viewID;
    foreach($view_fields as $vf)
    {
        $type = strtolower($vf['Type']);
        $name = 'ContentView'.$vf['Name'].'_'.$viewID;

        if($type == 'text')
        {
            $nuts_views_form .= nutsFormAddText($name, $vf['Label'], $vf['CssStyle'], $vf['Value'], $vf['Help'], $vf['TextAfter'], $vf['HrAfter'], $p_class);
        }
        elseif($type == 'textarea')
        {
            $nuts_views_form .= nutsFormAddTextarea($name, $vf['Label'], $vf['CssStyle'], $vf['Value'], $vf['Help'], $vf['HrAfter'], $p_class);
        }
        elseif($type == 'htmlarea')
        {
            $nuts_views_form .= nutsFormAddHtmlArea($name, $vf['Label'], $vf['CssStyle'], $vf['Value'], $vf['Help'], $vf['HrAfter'], $p_class);
        }
        elseif($type == 'colorpicker')
        {
            $nuts_views_form .= nutsFormAddColorpicker($name, $vf['Label'], $vf['CssStyle'], $vf['Value'], $vf['Help'], $vf['HrAfter'], $p_class);
        }
        elseif($type == 'date' || $type == 'datetime')
        {
            $nuts_views_form .= nutsFormAddDate($name, $vf['Label'], $type, $vf['Value'], $vf['Help'], $vf['HrAfter'], $p_class);
        }
        elseif($type == 'boolean' || $type == 'booleanx')
        {
            $nuts_views_form .= nutsFormAddBoolean($name, $vf['Label'], $type, $vf['Help'], $vf['HrAfter'], $p_class);
        }
        elseif($type == 'filemanager' || $type == 'filemanager_media' || $type == 'filemanager_image')
        {
            $folder = trim($vf['SpecialOption']);
            $nuts_views_form .= nutsFormAddFilemanager($name, $vf['Label'], $type, $vf['Value'], $folder, $vf['CssStyle'], $vf['Help'], $vf['HrAfter'], $p_class);
        }
        elseif($type == 'select')
        {
            $options = rtrim($vf['SpecialOption']);
            $nuts_views_form .= nutsFormAddSelect($name, $vf['Label'], $options, $vf['Value'], $vf['CssStyle'], $vf['Help'], $vf['HrAfter'], $p_class);
        }
        elseif($type == 'select-sql')
        {
            $sql = trim($vf['SpecialOption']);
            $nuts_views_form .= nutsFormAddSelectSql($name, $vf['Label'], $sql, $vf['Value'], $vf['CssStyle'], $vf['Help'], $vf['HrAfter'], $p_class);
        }
    }


    $nuts_views_form .= '</div>';
}



$nuts->parse('nuts_views_form', $nuts_views_form);



// access groups front office ***********************************************************************************
$sql = "SELECT ID AS GroupID, Name AS GroupName FROM NutsGroup WHERE Deleted = 'NO' AND FrontofficeAccess = 'YES' ORDER BY Priority";
$nuts->doQuery($sql);
$nuts->parseDbRow('page_access');


// url rewriting shortcut
$userAllowedPluginUrlRewriting = nutsUserHasRight($_SESSION['NutsGroupID'], '_url_rewriting', 'list');
$nuts->parse('userAllowedPluginUrlRewriting', $userAllowedPluginUrlRewriting);

// block manager shortcut
$userAllowedPluginBlockManager = nutsUserHasRight($_SESSION['NutsGroupID'], '_block_builder', 'list');
$nuts->parse('userAllowedPluginBlockManager', $userAllowedPluginBlockManager);


$plugin->render = $nuts->output();


?>