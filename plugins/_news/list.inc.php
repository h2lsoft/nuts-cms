<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');

//events
$hidden_fields_arr = explode(',', str_replace(' ', '', $hidden_fields));


// assign table to db
$sql_list_added = "";
for($i=0; $i < count($cf); $i++)
{
	if(isset($cf[$i]['list_add_sql']))
		$sql_list_added .= " , ".$cf[$i]['list_add_sql'];
}



$plugin->listSetDbTable('NutsNews', "-- DATE_ADD(DateGMT, INTERVAL {$_SESSION['Timezone']} HOUR) AS DateGMT,
									--  DATE_ADD(DateGMTExpiration, INTERVAL {$_SESSION['Timezone']} HOUR) AS DateGMTExpiration,
									(SELECT CONCAT(FirstName,' ', LastName) FROM NutsUser WHERE ID = NutsNews.NutsUserID) AS Author,
									(SELECT Avatar FROM NutsUser WHERE ID = NutsNews.NutsUserID) AS Avatar
									$sql_list_added");
// search engine
$plugin->listSearchAddFieldText('ID');
if(!in_array('Type', $hidden_fields_arr))$plugin->listSearchAddFieldTextAjaxAutoComplete('Type');
$plugin->listSearchAddFieldDate('DateGMT', $lang_msg[2]);
if(!in_array('DateGMTExpiration', $hidden_fields_arr))$plugin->listSearchAddFieldDate('DateGMT2', $lang_msg[3], 'DateGMT');
$plugin->listSearchAddFieldSelectSql('NutsUserID', $lang_msg[16], "CONCAT(FirstName,' ',LastName)");
$plugin->listSearchAddFieldSelect('Language', $lang_msg[1], nutsGetOptionsLanguages());
$plugin->listSearchAddField('Title', $lang_msg[4], 'text');
if(!in_array('Tags', $hidden_fields_arr))$plugin->listSearchAddFieldText('Tags', $lang_msg[7]);
if(!in_array('Event', $hidden_fields_arr))$plugin->listSearchAddFieldBoolean('Event', $lang_msg[8]);
$plugin->listSearchAddFieldBoolean('Active', $lang_msg[9]);
if(!in_array('VirtualPageName', $hidden_fields_arr))$plugin->listSearchAddFieldText('VirtualPageName', $lang_msg[18]);


for($i=0; $i <  count($cf); $i++)
{
	if($cf[$i]['type'] == 'text')
	{
		$plugin->listSearchAddField('Filter'.($i+1), toPascalCase($cf[$i]['name']), 'text');
	}
	elseif($cf[$i]['type'] == 'select')
	{
		if(isset($cf[$i]['options'][0]['label']))
		{
			$sel = $cf[$i]['options'];
		}
		else
		{
			$sel = '<option></option>'."\n";
			for($j=0; $j <  count($cf[$i]['options']); $j++)
			{
				$sel .= sprintf('<option value="%s">%s</option>'."\n", $cf[$i]['options'][$j], $cf[$i]['options'][$j]);
			}
		}

		$plugin->listSearchAddField('Filter'.($i+1), toPascalCase($cf[$i]['name']), 'select', array('options' => $sel));

	}
}



// create fields
$plugin->listAddCol('ID', '', 'center; width:5px', true); // with order by

if($news_thumb_list_view)
{
	$plugin->listAddCol('NewsImage', 'Image', 'center; width:5px', false);
}

if(!in_array('Type', $hidden_fields_arr))
    $plugin->listAddCol('Type', '', 'center; width:5px', true);

$plugin->listAddCol('DateGMT', $lang_msg[2], 'center; width:40px; white-space:nowrap;', true);
if(!in_array('DateGMTExpiration', $hidden_fields_arr))$plugin->listAddCol('DateGMTExpiration', $lang_msg[3], 'center; width:40px; white-space:nowrap;', true);
$plugin->listAddCol('Title', $lang_msg[4], '', true);

$plugin->listAddCol('Author', $lang_msg[16], 'center; width:10px;', false);
$plugin->listAddColImg('Language', $lang_msg[1], '', true, NUTS_IMAGES_URL.'/flag/{Language}.gif');


for($i=0; $i <  count($cf); $i++)
{
	if($cf[$i]['list_view'])
	{
		$plugin->listAddCol('Filter'.($i+1), toPascalCase($cf[$i]['name']), 'center; width:40px; white-space:nowrap;', true);
	}
}


if(!in_array('Event', $hidden_fields_arr))$plugin->listAddColImg('Event', $lang_msg[8], '', true);
$plugin->listAddColImg('Active', $lang_msg[9], '', true);

if($news_new_system)
    $plugin->listAddCol('Url', '', 'center; width:40px; white-space:nowrap;', false);


// social columns ?
$plugin->listAddCol('Social', "", 'center; width:40px; white-space:nowrap;', false);





$plugin->listSetFirstOrderBy('DateGMT');


// render list
$plugin->listRender(20, 'hookData');



function hookData($row){

	global $cf, $nuts, $news_new_system, $plugin;


    $original_date = $row['DateGMT'];

	if($_SESSION['Language'] == 'fr')
	{
		$row['DateGMT'] = $nuts->db2date($row['DateGMT']);
		$row['DateGMTExpiration'] = $nuts->db2date($row['DateGMTExpiration']);
	}	
	
	if(!empty($row['NewsImage']))
	{
		$row['NewsImage'] = '<img src="'.NUTS_NEWS_IMAGES_URL.'/thumb_'.$row['NewsImage'].'?t='.time().'" style="height:40px;" class="image_preview" />';
	}
	else
	{
		if(!empty($row['NewsImageModel']))
		{
			$row['NewsImage'] = '<img src="'.$row['NewsImageModel'].'?t='.time().'" style="height:40px;" class="image_preview" />';
		}
	}

	for($i=0; $i <  count($cf); $i++)
	{
		$row['Filter'.($i+1)] = ucfirst(strtolower($row['Filter'.($i+1)]));
	}


    // url
    $row['Url'] = <<<EOF
<a href="javascript:;" onclick="popupModal('{$row['VirtualPageName']}')"><img src="/nuts/img/icon-code_editor.png" /></a>
EOF;


    // author
    if(!$plugin->listExportExcelMode)
    {
        if(empty($row['Avatar']))$row['Avatar'] = '/nuts/img/gravatar.jpg';
        $row['Author'] = "<a class='tt' title=\"{$row['Author']}\"><img src='{$row['Avatar']}' style='max-width:40px; max-height:40px;'></a>";
    }


    // social funcs
    $row['Social'] = '';

    // google agenda
    $titleX = urlencode(str_replace("'", "\'", $row['Title']));
    $datesX = str_replace('-', '', $original_date);

    $uriX = urlencode(WEBSITE_URL.$row['VirtualPageName']);

    $uri = "https://www.google.com/calendar/render?action=TEMPLATE&text=$titleX&dates=$datesX/$datesX&details=$uriX&location&trp=false&sprop&sprop=name:&sf=true&output=xml";
    $row['Social'] .= '<a title="Google agenda" href="javascript:popupModal(\''.$uri.'\');"><img src="/plugins/_social-share/img/google_calendar.png" style="width:16px;" /></a> ';


    // facebook
    if(FACEBOOK_PUBLISH_URL != '')
    {
        $row['Social'] .= '<a title="Facebook" href="javascript:openFacebook(\''.FACEBOOK_PUBLISH_URL.'\');"><img src="/plugins/_social-share/img/facebook.png" style="width:16px;" /></a> ';
    }

    // twitter
    if(TWITTER_LOGIN != '')
    {
        $status = $row['Title'];
        if($news_new_system)
            $status .= " => ".WEBSITE_URL.$row['VirtualPageName'];
        $status = str_replace("'", "\'", $status);

        $row['Social'] .= '<a title="Twitter" href="javascript:openTwitter(\''.$status.'\');"><img src="/plugins/_social-share/img/twitter.png" style="width:16px;" /></a> ';
    }

    // google plus
    if(GOOGLEP_PUBLISH_URL != '')
    {
        $row['Social'] .= '<a title="Google plus" href="javascript:openGoogleP(\''.GOOGLEP_PUBLISH_URL.'\');"><img src="/plugins/_social-share/img/googlep.png" style="width:16px;" /></a> ';
    }


	return $row;
}




?>