<?php

include('../../nuts/config.inc.php');
include('../../nuts/config_auto.inc.php');
include('../../nuts/_inc/func.inc.php');
// include('../../nuts/_inc/custom.inc.php');

include(NUTS_PHP_PATH.'/TPLN/TPLN.php');
$nuts = new TPLN;
$nuts->DbConnect();
include('../../nuts/_inc/session.inc.php');
include(WEBSITE_PATH."/nuts/lang/{$_SESSION['Language']}.inc.php");
include(WEBSITE_PATH."/plugins/_page-manager/lang/{$_SESSION['Language']}.inc.php");


// Templates & Link list js arrray ******************************************************************************
if(@$_GET['action'] == 'get_templates')
{
	echo "var tinyMCETemplateList = [
			// Name, URL, Description
	";

	$nuts->DoQuery("SELECT ID, Name, Description FROM NutsRteTemplate WHERE Deleted = 'NO' ORDER BY Name");
	$str = '';
	while($row = $nuts->dbFetch())
    {
		if(!empty($str))$str .= ",\n";
		$str .= '["'.$row['Name'].'", "'.NUTS_JS_URL.'/tiny_mce.php?action=get_template&ID='.$row['ID'].'", "'.$row['Description'].'"]';
	}

	echo $str."\n";
	echo '];';
	die();
}
elseif(@$_GET['action'] == 'get_template')
{
	$_GET['ID'] = (int)@$_GET['ID'];
	if($_GET['ID'] == 0)die();

	$nuts->DoQuery("SELECT Content FROM NutsRteTemplate WHERE Deleted = 'NO' AND ID = {$_GET['ID']}");
	die($nuts->dbGetOne());

}
elseif(@$_GET['action'] == 'get_links')
{
	echo "var tinyMCELinkList = new Array(
			// This list may be created by a server logic page PHP/ASP/ASPX/JSP in some backend system.
			// There links will be displayed as a dropdown in all link dialogs if the 'external_link_list_url'
			// option is defined in TinyMCE init.
	";

	$str = '';

	// we charge distinct language actived
	$nuts->DoQuery("SELECT LanguageDefault, Languages FROM NutsTemplateConfiguration WHERE ID = 1");
	$rec = $nuts->dbFetch();

	$languages = array();
	$languages[] = $rec['LanguageDefault'];
	if(!empty($rec['Languages']))
	{
		$tmp = explode(',', strtolower($rec['Languages']));
		$tmp = array_map('trim', $tmp);
		$languages = array_merge($languages, $tmp);
	}


	// get distinct zoneID for this language
	$zone_ids = array(0);
	foreach($languages as $lng)
	{
		// include parents page
		// $lng = $lng['Language'];
		$url = "/$lng/";
		if(!empty($str))
		{
			$str .= ",[\"\", \"\"],\n";
		}
		$str .= '["[ Home '.ucfirst($lng).' ]", "'.$url.'"]';

		$sql = "SELECT DISTINCT ZoneID, (SELECT Name FROM NutsZone WHERE ID = ZoneID) AS ZoneName FROM NutsPage WHERE Deleted = 'NO' AND State = 'PUBLISHED' AND Language = '$lng' ORDER BY ID";
		$nuts->DoQuery($sql);
		$qID = $nuts->dbGetQueryID();
		while($zone = $nuts->dbFetch())
		{
			$c_zoneID = $zone['ZoneID'];
			$c_zone_name = $zone['ZoneName'];

			if(!empty($c_zone_name))
			{
				if(!empty($str))$str .= ",\n";
				$str .= '["", ""]';
				if(!empty($str))$str .= ",\n";
				$str .= '["[ '.$c_zone_name.' ]", ""]';
			}

			// pages listings
			$nuts_page_id = 0;
			$sql_tpl = "SELECT ID, Language, MenuName, VirtualPagename, _HasChildren FROM NutsPage WHERE NutsPageID = %s AND Language = '$lng' AND ZoneID = $c_zoneID AND Deleted = 'NO' AND State = 'PUBLISHED' ORDER BY Position";
			$nuts->dbSelect($sql_tpl, array($nuts_page_id));
			$qID1 = $nuts->dbGetQueryID();
			while($pg = $nuts->dbFetch())
			{
				$name = str_replace("'", "\'", $pg['MenuName']);
				$name = str_replace('"', '`', $pg['MenuName']);

				$url = nutsGetPageUrl($pg['ID'], $lng, $pg['VirtualPagename']);
				if(!empty($str))$str .= ",\n";
				$str .= '["'.$name.'", "'.$url.'"]';

				// recursivity for children page
				if($pg['_HasChildren'] == 'YES')
				{
					// level 2
					$nuts->dbSelect($sql_tpl, array($pg['ID']));
					$qID2 = $nuts->dbGetQueryID();
					while($pg2 = $nuts->dbFetch())
					{
						$name = str_replace("'", "\'", $pg2['MenuName']);
						$name = str_replace('"', '`', $pg2['MenuName']);

						$url = nutsGetPageUrl($pg2['ID'], $lng, $pg2['VirtualPagename']);
						if(!empty($str))$str .= ",\n";
						$str .= '[" &nbsp; |&#150; '.$name.'", "'.$url.'"]';

						// page has children ?
						if($pg2['_HasChildren'] == 'YES')
						{
							// level 3
							$nuts->dbSelect($sql_tpl, array($pg2['ID']));
							$qID3 = $nuts->dbGetQueryID();
							while($pg3 = $nuts->dbFetch())
							{
								$name = str_replace("'", "\'", $pg3['MenuName']);
								$name = str_replace('"', '`', $pg3['MenuName']);

								$url = nutsGetPageUrl($pg3['ID'], $lng, $pg3['VirtualPagename']);
								if(!empty($str))$str .= ",\n";
								$str .= '[" &nbsp;&nbsp;&nbsp; |&#150; '.$name.'", "'.$url.'"]';

								// level 4
								if($pg3['_HasChildren'] == 'YES')
								{
									$nuts->dbSelect($sql_tpl, array($pg3['ID']));
									while($pg4 = $nuts->dbFetch())
									{
										$name = str_replace("'", "\'", $pg4['MenuName']);
										$name = str_replace('"', '`', $pg4['MenuName']);

										$url = nutsGetPageUrl($pg4['ID'], $lng, $pg4['VirtualPagename']);
										if(!empty($str))$str .= ",\n";
										$str .= '[" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; |&#150; '.$name.'", "'.$url.'"]';
									}

								}

								$nuts->dbSetQueryID($qID3);
							}
						}

						$nuts->dbSetQueryID($qID2);
					}
				}

				$nuts->dbSetQueryID($qID1);
			}

			$nuts->dbSetQueryID($qID);
		}
	}

	echo $str."\n";
	echo ');';
	die();
}

// Nuts blocks menu *******************************************************************************
function menu_block()
{
    global $nuts;

    $nuts->DoQuery("SELECT ID, Name FROM NutsBlock WHERE Deleted = 'NO' AND Visible = 'YES' ORDER BY Name");
    if($nuts->dbNumrows() == 0)return;
    echo 'sub = m.addMenu({title : "Blocks"});';

    while($row = $nuts->dbFetch())
    {
        $ID = $row['ID'];
        $name = $row['Name'];
        $code = sprintf("{@NUTS    TYPE='BLOCK'    NAME='%s'}", $name);

        echo 'sub.add({title : "'.$name.'", onclick : function() {
				tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
			}});';
    }
}


// Nuts forms menu ******************************************************************************
function menu_form()
{
    global $nuts;

    $nuts->DoQuery("SELECT ID, Name FROM NutsForm WHERE Deleted = 'NO' ORDER BY Name");
	if($nuts->dbNumrows() == 0)return;
    echo 'sub = m.addMenu({title : "Forms"});';

    while($row = $nuts->dbFetch())
    {
        $ID = $row['ID'];
        $name = $row['Name'];
        $code = sprintf("{@NUTS    TYPE='FORM'    NAME='%s'}", $name);

		echo 'sub.add({title : "'.$name.'", onclick : function() {
				tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
			}});';
    }
}


// Nuts gallery menu ***************************************************************************
function menu_gallery()
{
    global $nuts;

    $nuts->DoQuery("SELECT ID, Name FROM NutsGallery WHERE Deleted = 'NO' AND Active = 'YES' ORDER BY Name");
    if($nuts->dbNumrows() == 0)return;
    echo 'sub = m.addMenu({title : "Gallery"});';

    while($row = $nuts->dbFetch())
    {
        $ID = $row['ID'];
        $name = $row['Name'];
        $code = sprintf("{@NUTS    TYPE='GALLERY'    NAME='%s'}", $name);

        echo 'sub.add({title : "'.$name.'", onclick : function() {
				tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
			}});';
    }
}



// Nuts media menu ******************************************************************************
function menu_media()
{
    global $nuts;

    // youtube video
    $nuts->DoQuery("SELECT ID, Name FROM NutsMedia WHERE Type='YOUTUBE VIDEO' AND Deleted = 'NO' ORDER BY Name");
    if($nuts->dbNumrows() > 0)
    {
        echo 'sub = m.addMenu({title : "Media> Youtube video"});';
        while($row = $nuts->dbFetch())
        {
            $ID = $row['ID'];
            $name = $row['Name'];
            $code = sprintf("{@NUTS    TYPE='MEDIA'    OBJECT='YOUTUBE VIDEO'    ID='%s'    NAME='%s'}", $ID, str_replace("'", "`", $name));

            echo 'sub.add({title : "'.$name.'", onclick : function() {
						tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
				}});';
        }
    }

    // dailymotion
    $nuts->DoQuery("SELECT ID, Name FROM NutsMedia WHERE Type='DAILYMOTION' AND Deleted = 'NO' ORDER BY Name");
    if($nuts->dbNumrows() > 0)
    {
        echo 'sub = m.addMenu({title : "Media> Dailymotion video"});';
        while($row = $nuts->dbFetch())
        {
            $ID = $row['ID'];
            $name = $row['Name'];
            $code = sprintf("{@NUTS    TYPE='MEDIA'    OBJECT='DAILYMOTION'    ID='%s'    NAME='%s'}", $ID, str_replace("'", "`", $name));

            echo 'sub.add({title : "'.$name.'", onclick : function() {
						tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
				}});';
        }
    }

    // iframe
    $nuts->DoQuery("SELECT ID, Name FROM NutsMedia WHERE Type='IFRAME' AND Deleted = 'NO' ORDER BY Name");
    if($nuts->dbNumrows() > 0)
    {
        echo 'sub = m.addMenu({title : "Media> Iframe"});';
        while($row = $nuts->dbFetch())
        {
            $ID = $row['ID'];
            $name = $row['Name'];
            $code = sprintf("{@NUTS    TYPE='MEDIA'    OBJECT='IFRAME'    ID='%s'    NAME='%s'}", $ID, str_replace("'", "`", $name));

            echo 'sub.add({title : "'.$name.'", onclick : function() {
						tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
				}});';
        }
    }

    // embed code
    $nuts->DoQuery("SELECT ID, Name FROM NutsMedia WHERE Type='EMBED CODE' AND Deleted = 'NO' ORDER BY Name");
    if($nuts->dbNumrows() > 0)
    {
        echo 'sub = m.addMenu({title : "Media> Embed code"});';
        while($row = $nuts->dbFetch())
        {
            $ID = $row['ID'];
            $name = $row['Name'];
            $code = sprintf("{@NUTS    TYPE='MEDIA'    OBJECT='EMBED CODE'    ID='%s'    NAME='%s'}", $ID, str_replace("'", "`", $name));

            echo 'sub.add({title : "'.$name.'", onclick : function() {
						tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
				}});';
        }
    }



    // audio
    $nuts->DoQuery("SELECT ID, Name FROM NutsMedia WHERE Type='AUDIO' AND Deleted = 'NO' ORDER BY Name");
    if($nuts->dbNumrows() > 0)
    {
        echo 'sub = m.addMenu({title : "Media> Audio"});';
        while($row = $nuts->dbFetch())
        {
            $ID = $row['ID'];
            $name = $row['Name'];
            $code = sprintf("{@NUTS    TYPE='MEDIA'    OBJECT='AUDIO'    ID='%s'    NAME='%s'}", $ID, str_replace("'", "`", $name));

            echo 'sub.add({title : "'.$name.'", onclick : function() {
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
				}});';
        }
    }
    // video
    $nuts->DoQuery("SELECT ID, Name FROM NutsMedia WHERE Type='VIDEO' AND Deleted = 'NO' ORDER BY Name");
    if($nuts->dbNumrows() > 0)
    {
        echo 'sub = m.addMenu({title : "Media> Video"});';
        while($row = $nuts->dbFetch())
        {
            $ID = $row['ID'];
            $name = $row['Name'];
            $code = sprintf("{@NUTS    TYPE='MEDIA'    OBJECT='VIDEO'    ID='%s'    NAME='%s'}", $ID, str_replace("'", "`", $name));

            echo 'sub.add({title : "'.$name.'", onclick : function() {
						tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
				}});';
        }
    }



}

// Nuts patterns menu ******************************************************************************
function menu_pattern()
{
    global $nuts;

    $nuts->DoQuery("SELECT Name, Pattern FROM NutsPattern WHERE Deleted = 'NO' ORDER BY Name");
    if($nuts->dbNumrows() == 0)return;
    echo 'sub = m.addMenu({title : "Patterns"});';

    while($row = $nuts->dbFetch())
    {
        $name = $row['Name'];
        $pattern = $row['Pattern'];

        $code = sprintf("%s", $pattern);

        echo 'sub.add({title : "'.$name.'", onclick : function() {
				tinyMCE.activeEditor.execCommand("mceInsertContent", false, "'.$code.'");
			}});';
    }
}


// Nuts plugin menu ******************************************************************************
function menu_plugin()
{
    $dir_plugin = WEBSITE_PATH.'/plugins';
    $plugins = glob($dir_plugin.'/*', GLOB_ONLYDIR);

    $new_plugins = array();
    foreach($plugins as $plugin)
    {
        $plugin = str_replace($dir_plugin."/", '', $plugin);
        if(!in_array($plugin, array('_page-manager','_news')) && is_dir($dir_plugin."/".$plugin."/www"))
            $new_plugins[] = $plugin;
    }


    if(count($new_plugins) == 0)
        return;



    echo 'sub = m.addMenu({title : "Plugins"});';

    foreach($new_plugins as $plugin)
    {
        $plugin = str_replace($dir_plugin."/", '', $plugin);
        $code = sprintf("{@NUTS    TYPE='PLUGIN'    NAME='%s'    PARAMETERS=''}", $plugin);

        echo 'sub.add({title : "'.$plugin.'", onclick : function() {
						tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));

					}});';
    }
}



// Nuts regions menu *******************************************************************************
function menu_region()
{
    global $nuts;

    $nuts->DoQuery("SELECT Name FROM NutsRegion WHERE Deleted = 'NO' ORDER BY Name");
    if($nuts->dbNumrows() == 0)return;
    echo 'sub = m.addMenu({title : "Regions"});';

    while($row = $nuts->dbFetch())
    {
        $name = $row['Name'];
        $code = sprintf("{@NUTS    TYPE='REGION'    NAME='%s'}", $name);
        echo 'sub.add({title : "'.$name.'", onclick : function() {
				tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
			}});';
    }
}

// Nuts survey menu ******************************************************************************
function menu_survey()
{
    global $nuts;

    $nuts->DoQuery("SELECT ID, Title FROM NutsSurvey WHERE Deleted = 'NO' ORDER BY Title");
	if($nuts->dbNumrows() == 0)return;
    echo 'sub = m.addMenu({title : "Survey"});';

    while($row = $nuts->dbFetch())
    {
        $ID = $row['ID'];
        $title = str_replace("'", '`', $row['Title']);

        $code = sprintf("{@NUTS    TYPE='SURVEY'    ID='%s'    TITLE='%s'}", $ID, $title);

		echo 'sub.add({title : "'.$title.'", onclick : function() {
				tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
			}});';
    }
}

// Nuts zones menu ****************************************************************************
function menu_zone()
{
    global $nuts;
    $plugin = "_zone-manager";

    $nuts->DoQuery("SELECT ID, Name FROM NutsZone WHERE Deleted = 'NO' AND Visible = 'YES' ORDER BY Name");
	if($nuts->dbNumrows() == 0)return;
    echo 'sub = m.addMenu({title : "Zones"});';

    while($row = $nuts->dbFetch())
    {
        $ID = $row['ID'];
        $name = $row['Name'];
		$code = sprintf("{@NUTS    TYPE='ZONE'    NAME='%s'}", $name);

        echo 'sub.add({title : "'.$name.'", onclick : function() {
						tinyMCE.activeEditor.execCommand("mceInsertContent", false, parse_nuts_tags("'.$code.'"));
					}});';
    }
}



// no cache
// header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
// header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

?>
<html>
    <head>
		<meta http-equiv="X-UA-Compatible" content="chrome=1" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Nuts Rich Editor</title>
		<style type="text/css">
		tr.mceLast {height:25px !important;}
		</style>

        <script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript" src="php.js"></script>
		<script type="text/javascript" src="../../nuts/nuts.js"></script>
		<script type="text/javascript">var lang = "<?php echo $_SESSION['Language'] ?>";</script>

		<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
		<!-- <script type="text/javascript" src="<?php echo NUTS_JS_URL; ?>/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script> -->
		<script type="text/javascript">
		function filebrowser(field_name, url, type, win)
		{
			fileBrowserURL = "<?php echo WEBSITE_URL; ?>/app/file_browser/index.php?editor=tinymce&filter="+type;

			tinyMCE.activeEditor.windowManager.open({
				title: "File Browser",
				url: fileBrowserURL,
				width: 1024,
				height: 768,
				inline: 1,
				maximizable: 1,
				close_previous: 0
			},
				{
					window : win,
					input : field_name
				}
			);
		}
		</script>

        <script>


		// Creates a new plugin class and a custom listbox
		tinymce.create('tinymce.plugins.NutsPlugins', {
			createControl: function(n, cm) {
				switch (n) {
					case 'NutsPlugins':
						var c = cm.createMenuButton('NutsPlugins', {
							title : 'Nuts plugins',
							image : '/nuts/img/icon-nuts.png',
							icons : false
						});

						c.onRenderMenu.add(function(c, m) {
										var sub;
										<?php
												menu_block();
												menu_form();
												menu_gallery();
												menu_media();
												menu_region();
												menu_pattern();
												menu_plugin();
												menu_survey();
												menu_zone();
												include('tiny_mce_custom_menu.inc.php');
										?>
						});

						// Return the new menu button instance
						return c;
				}

				return null;
			}
		});


		tinymce.PluginManager.add('NutsPlugins', tinymce.plugins.NutsPlugins);

		tinyMCE.init({
				language : "<?php echo $_SESSION['Language']; ?>",
				mode : "exact",
				elements : "RichEditor",

				theme : "advanced",
				skin : "o2k7",
//				skin_variant : "silver",
				editor_selector : "mceEditor",
				paste_text_use_dialog: true,


				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
				content_css : "/themes/editor_css.php?t=<?php echo $_GET['theme']; ?>&tstmp=<?php echo @$_GET['t']; ?>",
				document_base_url : "<?php echo WEBSITE_URL; ?>/",

				relative_urls : false,
				remove_script_host : true,
				file_browser_callback : "filebrowser",
				convert_urls : false,
				auto_focus : "RichEditor",

				template_external_list_url : "<?php echo NUTS_JS_URL ?>/tiny_mce.php?action=get_templates",
				external_link_list_url : "<?php echo NUTS_JS_URL ?>/tiny_mce.php?action=get_links",

				theme_advanced_resizing_use_cookie : false,
				theme_advanced_resize_horizontal : false,
				theme_advanced_resizing : false,
				auto_resize : false,
				theme_advanced_path : true,
				theme_advanced_statusbar_location : "bottom",

				plugins : "-NutsPlugins,template,imgmap,spellchecker,safari,style,layer,table,advhr,advimage,advlink,preview,media,searchreplace,print,contextmenu,paste,directionality,visualchars,nonbreaking,xhtmlxtras,code2,inlinepopups,save,wordcount2,gtranslate",
//				extended_valid_elements : "img[usemap|class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],map[id|name|style],area[shape|alt|coords|href|target|style]",
				theme_advanced_buttons1 : "cut,copy,pastetext,pasteword,|,search,replace,|,undo,redo,|,charmap,advhr,|,sub,sup,|,visualchars,visualaid,|,print,spellchecker,cleanup,|,gtranslate,wordcount2,|,code2,code,preview,|,save,cancel,|,exit",
				theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,styleprops,cite,|,link,unlink,anchor,|,forecolor,backcolor,|,removeformat,attribs",
				theme_advanced_buttons3 : "NutsPlugins,template,|,image,imgmap,media,|,tablecontrols,formatselect,styleselect,fontselect,fontsizeselect",


				spellchecker_languages : "<?php echo nutsGetSpellcheckerLanguages(); ?>",


				// begin tinymce custom
				<?php

$nuts->doQuery("SELECT Content FROM NutsRichEditor WHERE ID = 1");
echo $nuts->dbGetOne();

				?>
				// end tinymce custom

				save_onsavecallback : function(){my_save(true);},
				save_oncancelcallback: "my_cancel",

                setup : function(ed) {

					ed.addButton('exit', {
                        title : '<?php echo $lang_msg[68]; ?>',
                        image : '/nuts/img/exit.gif',
                        onclick : function() {
                            my_exit();
                        }
                    })
                }
		});

        // call back
        function my_close()
        {
			// tinyMCE.get('RichEditor').setContent('');
            // parent.closeWYSIWYG();
			window.close();
		}

		function my_exit()
		{
			my_save(false);
			my_close();
		}

		function my_save(direct_saving)
        {
			//var parentID = parent.RTE_parent_object;
			var parentID = "<?php echo $_GET['objID']; ?>";

			val = tinyMCE.get('RichEditor').getContent();
			window.opener.$('textarea#'+parentID).val(val);
			window.opener.WYSIWYGIFrameReload(parentID, val);
            window.opener.refreshWYSIWYG(parentID);

			if(direct_saving == true)
			{
				/*if(window.opener.$('#chk_Close'))
					window.opener.$('#chk_Close').attr('checked', false);
				else
					window.opener.$('#close_after').attr('checked', false);*/

				window.opener.$('#chk_Close').attr('checked', false);
				window.opener.$('#close_after').attr('checked', false);

				window.opener.$('#former').submit();
			}

		}

		function my_cancel()
		{
			msg = "<?php echo $lang_msg[56]; ?>";
			if((c=confirm(msg)))
			{
				my_close();
			}
		}

		function loadParent()
		{
			//var parentID = parent.RTE_parent_object;
			//new_value = parent.$('textarea#'+parentID).val();
			//tinyMCE.get('RichEditor').setContent(new_value);

			var parentID = "<?php echo $_GET['objID']; ?>";
			new_value = window.opener.$('textarea#'+parentID).val();
			$('#RichEditor').val(new_value);
			// tinyMCE.get('RichEditor').setContent(new_value);
		}

		function reloadIt()
		{
			alert("An error occurred editor will be refreshed");
			window.close();
		}

        </script>
		<style type="text/css">
		.button {opacity:0.8;}
		.button:hover {opacity:1;}
		</style>

    </head>
    <body style="margin:0; padding:0; background-color:#EEEEEE; overflow: hidden;">

		<form name="form" target="" onsubmit="return false;">
			<textarea name="RichEditor" id="RichEditor" style="width:100%; height:99%;" class="mceEditor" onclick="reloadIt()" onkeydown="reloadIt()"></textarea>
		</form>
<?php

$nuts->DbClose();

?>
		<script type="text/javascript">
		loadParent();

		// resize window
		window.moveTo(0,0);
		window.resizeTo(screen.availWidth,screen.availHeight);

		</script>
    </body>
</html>
