<?php

if($for == 'MAIN')
	$nuts->open(WEBSITE_PATH.'/nuts/_templates/main_menu.html');
else
	$nuts->open(WEBSITE_PATH.'/nuts/_templates/home_menu.html');


$i = 0;
$mod_parsed = 0;
foreach($mods_group as $group)
{
	// get all childs
	$sql = "SELECT
					NutsMenu.*
			FROM
					NutsMenu,
					NutsMenuRight
			WHERE
					NutsMenuRight.NutsMenuID = NutsMenu.ID  AND
					Category = ".($i+1)." AND
					NutsMenuRight.NutsGroupID = {$_SESSION['NutsGroupID']} AND
					NutsMenu.Visible = 'YES'
			GROUP BY
					NutsMenu.Name
			ORDER BY
					NutsMenu.Position";
	$nuts->doQuery($sql);
	$res = $nuts->getRowsCount();


	if($res == 0)
	{
		// $nuts->loop('parent.child');
	}
	else
	{
		$nuts->parse('parent.name', $group['name']);
		if($for == 'MAIN')
		{
			$nuts->parse('parent.color', $group['color']);

			$color_sel = '';
			/*if($plugin->Categorie == ($i+1))
				$color_sel = $group['color'];*/

			$nuts->parse('parent.color_sel', $color_sel);
		}

		while($row = $nuts->dbFetch())
		{
			$mod_parsed++;

			$yaml = Spyc::YAMLLoad(WEBSITE_PATH.'/plugins/'.$row['Name'].'/info.yml');
			$langs = array_map('trim', explode(',',$yaml['langs']));

			$default_lang = $langs[0];
			$yaml['info'] = trim($yaml['info']);

			if(file_exists(WEBSITE_PATH.'/plugins/'.$row['Name'].'/lang/'.$NutsUserLang.'.inc.php'))
			{
				include(WEBSITE_PATH.'/plugins/'.$row['Name'].'/lang/'.$NutsUserLang.'.inc.php');
			}
			else
			{
				include(WEBSITE_PATH.'/plugins/'.$row['Name'].'/lang/'.$default_lang.'.inc.php');
			}

			// description for home
			if($for == 'HOME')
			{
				$mod_title = "";
				if($default_lang == $NutsUserLang || !in_array($NutsUserLang, $langs))
				{
					$mod_title = $yaml['info'];
				}
				elseif(!empty($yaml['info']))
				{
					if(isset($yaml['info_'.$NutsUserLang]))
					{
						$mod_title = $yaml['info_'.$NutsUserLang];
					}
					else
					{
						// translate $default_lang to $NutsUserLang
						$uri = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=$default_lang|$NutsUserLang";
						$uri .= "&q=".urlencode($yaml['info']);
						$table = json_decode(file_get_contents($uri));
						$mod_title = $table->{'responseData'}->{'translatedText'};

						$info_yml_contents = file_get_contents(WEBSITE_PATH.'/plugins/'.$row['Name'].'/info.yml');
						$info_yml_lines = explode("\n", $info_yml_contents);

						// get line for replace
						$line = "";
						foreach($info_yml_lines as $ln)
						{
							if(($line = strstr($ln, 'info:')))
								break;
						}

						if(!empty($line))
						{
							$line2 = $line."\n"."info_{$NutsUserLang}: $mod_title";
							$info_yml_contents = str_replace($line, $line2, $info_yml_contents);
							file_put_contents(WEBSITE_PATH.'/plugins/'.$row['Name'].'/info.yml', $info_yml_contents);
						}
					}
				}

				$nuts->parse('parent.child.mod_title', $mod_title, '|trim');
			}

			// get icone plugin
			$nuts->parse('parent.child.c_name', $row['Name']);

			// get real name of each module
			$nuts->parse('parent.child.c_rname', $lang_msg[0]);

			// external menu ?
			if(empty($row['ExternalUrl']))
			{
				$yaml = Spyc::YAMLLoad(WEBSITE_PATH.'/plugins/'.$row['Name'].'/info.yml');
				$url = 'index.php?mod='.$row['Name'].'&do='.$yaml['default_action'];
				$nuts->parse('parent.child.c_uri', "javascript:system_goto('".$url."', 'content');");
				$nuts->parse('parent.child.c_target', '');

				if($for == 'MAIN')$nuts->parse('parent.child.c_uri_direct', $url);
			}
			else
			{
				$nuts->parse('parent.child.c_uri', $row['ExternalUrl']);

				$blank = (preg_match('#^(http|ftp|mailto)#i', $row['ExternalUrl'])) ? '_blank' : '';
				$nuts->parse('parent.child.c_target', $blank);


				if($for == 'MAIN')$nuts->parse('parent.child.c_uri_direct', $row['ExternalUrl']);
			}

			// hr ?
			if($for == 'MAIN')
			{
				$break_before = '';
				if($row['BreakBefore'] == 1)$break_before = '<li class="breaker"><hr /></li>';
				$nuts->parse('parent.child.break_before', $break_before);

				$break_after = '';
				if($row['BreakAfter'] == 1)$break_after = '<li class="breaker"><hr /></li>';
				$nuts->parse('parent.child.break_after', $break_after);

			}
			else
			{
				$br = '';
				//if($row['BreakAfter'] == 1)$br = '<div style="clear:left"></div>';
				$nuts->parse('parent.child.br', $br);

				// border
				$style = '';
				if($row['BreakBefore'] == 1)$style .= '';
				if($row['BreakAfter'] == 1)$style .= '';
				$nuts->parse('parent.child.style', $style);
			}

			$nuts->loop('parent.child');
		}

		$nuts->loop('parent');
	}

	$i++;
}
// no meu found
if(!$mod_parsed)
{
	if($for == 'MAIN')
		$nuts->eraseBloc('parent');
	else
		$nuts->parseBloc('parent', '<div id="nuts_no_plugin">'.$nuts_lang_msg[77].'</div>');
}

$menu = $nuts->output();

// add fixed menu at end ?
if($for == 'MAIN')
{
	$contact_url = 'http://www.nuts-cms.com/en/20-contact-us.html';
	$website_url = 'http://www.nuts-cms.com';

	$help_menu = <<<EOF

   <li onmouseout="this.style.backgroundColor='';" onmouseover="this.style.backgroundColor='#DC57B1';" style=""><a>?</a>

		<!-- child -->
		<ul style="border-color: #DC57B1;" class="ulc">
		<li>
			<a target="_blank" href="https://github.com/h2lsoft/Nuts-CMS/issues"> <img width="16" height="16" src="/nuts/img/bug_48.png" align="bottom"> {$nuts_lang_msg[58]}</a>
		</li>
		<li>
			<a target="_blank" href="https://github.com/h2lsoft/Nuts-CMS/issues"> <img width="16" height="16" src="/nuts/img/suggest_48.png"> {$nuts_lang_msg[59]}</a>
		</li>

		<li class="breaker"><hr /></li>

		<li>
			<a target="_blank" href="$website_url"> <img width="16" height="16" src="/nuts/img/website_48.png"> {$nuts_lang_msg[60]}</a>
		</li>

		</ul>
		<!-- child -->

	</li>

EOF;

	$menu = trim($menu);
	$menu = substr($menu, 0, -5);
	$menu .= $help_menu.'</ul>';
}




?>