<?php

// controller *******************************************************************************************
if(!isset($_GET['NutsGroupID']))$_GET['NutsGroupID'] = 0;
$_GET['NutsGroupID'] = (int)$_GET['NutsGroupID'];
if($_GET['NutsGroupID'] != 0 && !$plugin->isRecordExists('NutsGroup', 'NutsGroupID'))$_GET['NutsGroupID'] = 0;

// save data ********************************************************************************************
if($_POST && $_GET['NutsGroupID'] != 0)
{		
	$nuts->doQuery("DELETE FROM NutsMenuRight WHERE NutsGroupID = {$_GET['NutsGroupID']}");
	
	if(!isset($_POST['right']) || !is_array($_POST['right']))$_POST['right'] = array();
	
	foreach($_POST['right'] as $key => $vals)
	{
		// search for the ID
		$nuts->dbSelect("SELECT ID FROM NutsMenu WHERE Name = '%s'", array($key));
		if($nuts->dbNumRows() == 0)
		{
			$nuts->dbInsert("NutsMenu", array('Name' => $key));
			$cID = $nuts->getMaxID("NutsMenu", 'ID');
		}
		else
		{
			$cID = (int)$nuts->getOne();
		}
	
		foreach($vals as $val)
		{
			$sql = "INSERT INTO NutsMenuRight 
						(
							NutsMenuID, 
							NutsGroupID, 
							Name
						) 
					VALUES 
						(
							(SELECT ID FROM NutsMenu WHERE Name = '$key' LIMIT 1), 
							{$_GET['NutsGroupID']}, 
							'$val'
						)";
			$nuts->doQuery($sql);
		}
	}
	
	// force hide my notes
	$sql = "UPDATE NutsMenu SET Visible = 'NO' WHERE Name = '_internal-memo'";
	$nuts->doQuery($sql);	
	
	$plugin->trace("`{$_POST['NutsGroupName']}` modified", $_GET['NutsGroupID']);
	die('ok');
}

// execution ********************************************************************************************s

// get group name
if($_GET['NutsGroupID'] != 0)
{
	$nuts->DoQuery("SELECT Name FROM NutsGroup WHERE ID = {$_GET['NutsGroupID']}");
	$NutsGroupName = $nuts->getOne();
}

// get groups
$sql = "SELECT ID, Priority, Name FROM NutsGroup WHERE Deleted = 'NO' ORDER BY Priority";
$nuts->DoQuery($sql);
$select_groups = '<option value=""></option>'."\n";;
while($row = $nuts->dbFetch())
{
	$select_groups .= '<option value="'.$row['ID'].'">'."#{$row['ID']} - ".$row['Name'].' (Priority: '.$row['Priority'].')'.'</option>"'."\n";
}

// parsing
$nuts->open(PLUGIN_PATH.'/form.html');



if($_GET['NutsGroupID'] == 0)
{
	$nuts->eraseBloc('ctn');
}
else
{	
	$cats = array();
	$all_cats = array();
	foreach($mods_group as $cat)
	{	
		$cats[] = $cat['name'];	
		$all_cats[$cat['name']] = array();
	}	
	
	// plugins listing with rights
	$dirs = glob(NUTS_PLUGINS_PATH."/*", GLOB_ONLYDIR);
	foreach($dirs as $dir)
	{
		$dir_origin = $dir;
		$dir = str_replace(NUTS_PLUGINS_PATH."/", '', $dir);
		$r_name = $dir;
		$name = str_replace(array('_', '-'), ' ', $dir);		
		
		// listing rights
		if(file_exists("$dir_origin/info.yml") && !in_array($dir, array('_error', '_home')))
		{			
			// get plugin category
			$sql = "SELECT Category FROM NutsMenu WHERE Name = '$r_name' LIMIT 1";
			$nuts->doQuery($sql);
			$catID = $nuts->dbGetOne();
			$catID = $catID - 1;
            if($catID < 0)$catID = 3; # plugins by default
			
			$type = 'public';
			if($r_name[0] == '_')$type = 'private';
			
			$r = spyc::YAMLLoad("$dir_origin/info.yml");
			$acts = $r["actions"];
			$acts_tab = explode(',', $acts);
			$acts_tab = array_map('trim', $acts_tab);
			
			// selected rights
			$nuts->doQuery("SELECT 
										NutsMenuRight.Name
							FROM 
										NutsMenuRight,
										NutsMenu
							WHERE
										NutsMenu.ID = NutsMenuRight.NutsMenuID AND
										NutsMenu.Name = '$r_name' AND
										NutsMenuRight.NutsGroupID = {$_GET['NutsGroupID']}");
			$act_selected = array();
			while($r = $nuts->dbFetch())
				$act_selected[] = $r['Name'];
			
			
			$all_cats[$cats[$catID]][$r_name]['category'] = $cats[$catID];
			$all_cats[$cats[$catID]][$r_name]['categoryID'] = $catID + 1;
			$all_cats[$cats[$catID]][$r_name]['name'] = $r_name;
			$all_cats[$cats[$catID]][$r_name]['label'] = $name;
			$all_cats[$cats[$catID]][$r_name]['type'] = $type;
			$all_cats[$cats[$catID]][$r_name]['actions'] = $acts_tab;
			$all_cats[$cats[$catID]][$r_name]['actions_selected'] = $act_selected;			
		}
	}
	
	// parsing		
	foreach($cats as $cat)
	{
		$nuts->parse('category.Category', $cat);
		$nuts->parse('category.category_count', count($all_cats[$cat]));
		
		// parsing rights
		if(!count($all_cats[$cat]))
		{
			$nuts->eraseBloc('category.rights');	
			$nuts->loop('category.rights');		
		}
		else
		{
			$plugins = array_keys($all_cats[$cat]);
			foreach($plugins as $xplugin)
			{
				$nuts->parse('category.rights.type', $all_cats[$cat][$xplugin]['type']);
				$nuts->parse('category.rights.p_name', $all_cats[$cat][$xplugin]['name']);	
				$nuts->parse('category.rights.name', $all_cats[$cat][$xplugin]['label'], '|trim|ucfirst');
				
				foreach($all_cats[$cat][$xplugin]['actions'] as $action)
				{
					$nuts->parse('category.rights.right.r_name', $all_cats[$cat][$xplugin]['name']);
					$nuts->parse('category.rights.right.r', $action);
					
					$checked = '';
					if(in_array($action, $all_cats[$cat][$xplugin]['actions_selected']) || $_GET['NutsGroupID'] == 1 || in_array($all_cats[$cat][$xplugin]['name'], array('_internal-memo', '_internal-messaging')))
						$checked = 'checked';
					
					$nuts->parse('category.rights.right.checked', $checked);
					$nuts->loop('category.rights.right');
				}
				
				
				
				$nuts->loop('category.rights');
			}
			
		}
		
		
		$nuts->loop('category');		
	}
	
	
	
	
	
}



$plugin->render = $nuts->output();



?>