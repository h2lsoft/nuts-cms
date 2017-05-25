<?php
/**
 * Service exec
 *
 * @version 1.0
 * @date 19/11/2012
 */

/* @var $plugin Plugin */
/* @var $nuts TPLN */

header("Content-Type: text/plain");

// includes ************************************************************************************************************
include('../../nuts/config.inc.php');
include('../../nuts/headers.inc.php');

// execution ***********************************************************************************************************
$nuts = new NutsCore();
$nuts->dbConnect();

// controller **********************************************************************************************************
$_GET['ID'] = (int)@$_GET['ID'];

Query::factory()->select('*')
                ->from('NutsService')
                ->where('ID', '=', $_GET['ID'])
                ->execute();

if(!$nuts->dbNumRows())
{
    nutsTrace('service', 'get', print_r($_GET, true));
    die("Error: service not found");
}
else
{
    $rec = $nuts->dbFetch();
    if($rec['Token'] != @$_GET['token'])
    {
        nutsTrace('service', 'get', print_r($_GET, true));
        die("Error: service token not correct");
    }

    $output = (!in_array(strtolower(@$_GET['output']), array('json', 'array', 'xml'))) ? $rec['Output'] : $_GET['output'];
    $output = strtolower($output);

    $sql = $rec['Query'];
    $sql = str_replace('{_', '{$_', $sql);
    @eval("\$sql = \"$sql\";");
    $nuts->doQuery($sql);

    $data = array();
    while($rec = $nuts->dbFetch())
    {
        $data[] = $rec;
    }

    // output::array
    if($output == 'array')
    {
        die(serialize($data));
    }

    // output::json
    if($output == 'json'){
        die(json_encode($data));
    }

    // output::xml
    if($output == 'xml'){
        $xml = '<?xml version="1.0"?>'.CR;
        $xml .= '<rows>'.CR;

        foreach($data as $row)
        {
            $xml .= TAB.'<row>'.CR;

            foreach($row as $key => $val)
            {
                if(is_numeric($val) || is_float($val))
                    $xml .= TAB.TAB.'<'.$key.'>'.$val.'</'.$key.'>'.CR;
                else
                    $xml .= TAB.TAB.'<'.$key.'><![CDATA['.$val.']]></'.$key.'>'.CR;
            }

            $xml .= TAB.'</row>'.CR;
        }

        $xml .= '</rows>'.CR;


        die($xml);
    }

}





$nuts->dbClose();


