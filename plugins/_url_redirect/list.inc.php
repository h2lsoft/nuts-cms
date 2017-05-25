<?php
/**
 * Plugin url_redirect - action List
 *
 * @version 1.0
 * @date 12/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// ajax
if(@$_GET['_action'] == 'write_htaccess')
{
    $msg = 'ok';

    if(!is_writable(WEBSITE_PATH.'/.htaccess'))
    {
        $msg = "File .htaccess is not writable";
        die($msg);
    }

    // generate str
    $urls = Query::factory()->from('NutsUrlRedirect')
                            ->order_by('Position')
                            ->executeAndGetAll();

    $str = "";
    foreach($urls as $url)
    {
        $type = explode(' - ', $url['Type']);
        $type = $type[0];

        $line = "redirect $type {$url['UrlOld']} {$url['UrlNew']}";
        $str .= trim($line).CR;
    }



    // replace markup
    $content = file_get_contents(WEBSITE_PATH.'/.htaccess');
    $replace = $nuts->extractStr($content, "#### NUTS REDIRECT BEGIN ###", "#### NUTS REDIRECT END ###", true);

    $str = "#### NUTS REDIRECT BEGIN ###".CR.$str.CR."#### NUTS REDIRECT END ###";
    if(empty($replace))
    {
        $content .= CR.CR.$str;
    }
    else
    {
        $content = str_replace($replace, $str, $content);
    }

    // write
    if(!file_put_contents(WEBSITE_PATH.'/.htaccess', $content))
    {
        die("Error: error while writing .htaccess directives");
    }


    die($msg);
}



include_once(PLUGIN_PATH.'/config.inc.php');

// assign table to db
$plugin->listSetDbTable('NutsUrlRedirect');

// search engine
// $plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldSelect('Type', '', $types);
$plugin->listSearchAddFieldText ('UrlOld', $lang_msg[3], '', '', '^=');
$plugin->listSearchAddFieldText ('UrlNew', $lang_msg[4], '', '', '^=');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true);
$plugin->listAddCol('Type', '', 'center; width:30px', true);
$plugin->listAddCol('UrlOld', $lang_msg[3], '', false);
$plugin->listAddCol('UrlNew', $lang_msg[4], '', false);
$plugin->listAddCol('Position', '', 'center; width:30px', true);

// button write .htaccess
$plugin->listAddButton('Write', $lang_msg[2], 'writeHtaccess();');


// render list
$plugin->listSetFirstOrderBy('Position');
$plugin->listSetFirstOrderBySort('ASC');
$plugin->listRender(100, 'hookData');


function hookData($row)
{
	global $nuts, $plugin;
	
	
	
	return $row;
}

