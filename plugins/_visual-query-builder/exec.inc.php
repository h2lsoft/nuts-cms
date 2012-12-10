<?php
/**
 * Plugin Visual query builder - action Exec
 * 
 * @version 1.0
 * @date 02/12/2012
 * @author H2Lsoft (contact@h2lsoft.com) - www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// ajax ****************************************************************************************************************
if($_POST && @$_GET['ajaxer'] == 1)
{

    if($_GET['action'] == 'query_preview')
    {
        // security first word must be SELECT
        $query = $_POST['query'];
        $limit = (int)$_POST['limit'];
        if(substr($query, 0, strlen("SELECT\n")) != "SELECT\n")
        {
            die("Error: query must begins by SELECT clause");
        }

        // check LIMIT clause and replace it
        if(!$limit || $limit > 500)$limit = 100;
        $conn_str = "mysql:host=".NUTS_DB_HOST.";dbname=".NUTS_DB_BASE.";port=".NUTS_DB_PORT;
        $conn = new PDO($conn_str, NUTS_DB_USER, NUTS_DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try
        {
            $results = $conn->query($query);

            $results = $conn->prepare($query);
            $results->setFetchMode(PDO::FETCH_ASSOC);
            $results->execute();

            $rowCount = $results->rowCount();

            $total = 0;
            $rows = array();
            while($row = $results->fetch())
            {
                $rows[] = $row;
                $total++;
                if($total == $limit)
                    break;
            }
            $results->closeCursor();


            if(!$total)
            {
                $html = $lang_msg[14];
            }
            else
            {
                $html = "<h3>{$lang_msg[15]}: $rowCount ({$lang_msg[16]}: $limit)</h3>";
                $html .= array2table($rows, "", "", " class='datagrid'");
            }


            die($html);
        }
        catch (PDOException $e)
        {
            $err_code = $e->getCode();
            $err_msg = $e->getMessage();
            $html = "<b>Errror $err_code :</b><pre style='border:1px solid #ccc; padding:5px;'>".$err_msg.'</pre>';
            die($html);
        }


    }



    if($_GET['action'] == 'format_sql')
    {
        $_POST['sql'] = trim($_POST['sql']);
        if(empty($_POST['sql']))die();


        include_once(NUTS_PATH.'/_inc/sqlFormatter.class.php');
        $sql = SqlFormatter::format($_POST['sql'], false);
        $sql = str_replace("\nAND \n", "AND\n", $sql);
        $sql = str_replace("(\n\t\t", "(", $sql);
        $sql = explode(CR, $sql);
        $sql = array_map('rtrim', $sql);
        $sql = join(CR, $sql);

        $sql = str_replace('select'.CR, "SELECT".CR, $sql);
        $sql = str_replace(CR.'from'.CR, CR."FROM".CR, $sql);
        $sql = str_replace(CR.'where'.CR, CR."WHERE".CR, $sql);
        $sql = str_replace('and', " AND".CR, $sql);
        $sql = str_replace(CR.'group by'.CR, CR."GROUP BY".CR, $sql);
        $sql = str_replace(' having ', " HAVING ".CR, $sql);
        $sql = str_replace(CR.'order by'.CR, CR."ORDER BY".CR, $sql);

        // reconstruct sql
        $cons = array();
        $cons['QUERY'] = $sql;


        // catch SELECT
        $cons['SELECT'] = $nuts->extractStr($sql, "SELECT\n", "\nFROM\n", false);
        $cons['QUERY'] = str_replace($cons['SELECT'], 'X', $cons['QUERY']);

        // catch FROM
        $tmp = $nuts->extractStr($cons['QUERY'], "\nFROM\n", "\nWHERE\n", false);
        if(!empty($tmp))
        {
            $cons['FROM'] = $tmp;
        }
        elseif(($tmp = $nuts->extractStr($cons['QUERY'], "\nFROM\n", "\nGROUP BY\n", false)))
        {
            $cons['FROM'] = $tmp;
        }
        elseif(($tmp = $nuts->extractStr($cons['QUERY'], "\nFROM\n", "\nORDER BY\n", false)))
        {
            $cons['FROM'] = $tmp;
        }
        elseif(($tmp = $nuts->extractStr($cons['QUERY'], "\nFROM\n", "\nLIMIT\n", false)))
        {
            $cons['FROM'] = $tmp;
        }
        else
        {
            $cons['FROM'] = substr($cons['QUERY'], strpos($cons['QUERY'], "\nFROM\n"));
            $cons['FROM'] = str_replace("\nFROM\n", "", $cons['FROM']);
        }

        $cons['QUERY'] = str_replace($cons['FROM'], 'X', $cons['QUERY']);

        // catch WHERE
        $tmp = $nuts->extractStr($cons['QUERY'], "\nWHERE\n", "\nGROUP BY\n", false);
        if(!empty($tmp))
        {
            $cons['WHERE'] = $tmp;
        }
        elseif(($tmp = $nuts->extractStr($cons['QUERY'], "\nWHERE\n", "\nORDER BY\n", false)))
        {
            $cons['WHERE'] = $tmp;
        }
        elseif(($tmp = $nuts->extractStr($cons['QUERY'], "\nWHERE\n", "\nLIMIT\n", false)))
        {
            $cons['WHERE'] = $tmp;
        }
        else
        {
            $cons['WHERE'] = '';
            if(strpos($cons['QUERY'], "\nWHERE\n") !== false)
            {
                $cons['WHERE'] = substr($cons['QUERY'], strpos($cons['QUERY'], "\nWHERE\n"));
                $cons['WHERE'] = str_replace("\nWHERE\n", "", $cons['WHERE']);
            }
        }
        if(!empty($cons['WHERE']))
            $cons['QUERY'] = str_replace($cons['WHERE'], 'X', $cons['QUERY']);

        // catch GROUP BY
        $tmp = $nuts->extractStr($cons['QUERY'], "\nGROUP BY\n", "\nORDER BY\n", false);
        if(!empty($tmp))
        {
            $cons['GROUP BY'] = $tmp;
        }
        elseif(($tmp = $nuts->extractStr($cons['QUERY'], "\nGROUP BY\n", "\nLIMIT\n", false)))
        {
            $cons['GROUP BY'] = $tmp;
        }
        else
        {
            $cons['GROUP BY'] = '';
            if(strpos($cons['QUERY'], "\nGROUP BY\n") !== false)
            {
                $cons['GROUP BY'] = substr($cons['QUERY'], strpos($cons['QUERY'], "\nGROUP BY\n"));
                $cons['GROUP BY'] = str_replace("\nGROUP BY\n", "", $cons['GROUP BY']);
            }
        }

        if(!empty($cons['GROUP BY']))
            $cons['QUERY'] = str_replace($cons['GROUP BY'], 'X', $cons['QUERY']);

        // catch ORDER BY
        $tmp = $nuts->extractStr($cons['QUERY'], "\nORDER BY\n", "\nLIMIT\n", false);
        if(!empty($tmp))
        {
            $cons['ORDER BY'] = $tmp;
        }
        else
        {
            $cons['ORDER BY'] = '';
            if(strpos($cons['QUERY'], "\nORDER BY\n") !== false)
            {
                $cons['ORDER BY'] = substr($cons['QUERY'], strpos($cons['QUERY'], "\nORDER BY\n"));
                $cons['ORDER BY'] = str_replace("\nORDER BY\n", "", $cons['ORDER BY']);
            }
        }

        if(!empty($cons['ORDER BY']))
            $cons['QUERY'] = str_replace($cons['ORDER BY'], 'X', $cons['QUERY']);

        // catch LIMIT
        $cons['LIMIT'] = '';
        if(strpos($cons['QUERY'], "\nLIMIT\n") !== false)
        {
            $cons['LIMIT'] = substr($cons['LIMIT'], strpos($cons['QUERY'], "\nLIMIT\n"));
            $cons['LIMIT'] = str_replace("\nLIMIT\n", "", $cons['LIMIT']);
        }

        // apply format to all
        foreach($cons as $key => $val)
        {
            if(!empty($val))
            {
                $tmp = $cons[$key];
                $tmp = str_replace(array("\n", "\t"), ' ', $cons[$key]);

                while(strpos($tmp, '  ') !== false)
                    $tmp = str_replace('  ', ' ', $tmp);
                $tmp = str_replace(' )', ')', $tmp);
                $tmp = trim($tmp);

                // parse string
                $words = array();
                $word_next = array();
                $word_boundaries_level = 0;
                $word_boundaries_start = array('(', '"', "'");
                $word_boundaries_end   = array(')', '"', "'");

                for($i=0; $i < strlen($tmp); $i++)
                {
                    if(in_array($tmp[$i], $word_boundaries_start))
                    {
                        $word_boundaries_level++;
                    }
                    elseif($word_boundaries_level > 0 && in_array($tmp[$i], $word_boundaries_end))
                    {
                        $word_boundaries_level--;
                    }

                    if($word_boundaries_level == 0 && $tmp[$i] == ',')
                    {
                        $tmp[$i] = '~';
                    }
                }

                if(in_array($key, array('SELECT', 'FROM', 'ORDER BY')))
                {
                    $tmp = trim($tmp);
                    $tmp2 = explode('~', $tmp);
                    $tmp = '';
                    for($i=0; $i < count($tmp2); $i++)
                    {
                        $tmp2[$i] = trim($tmp2[$i]);
                        if(!empty($tmp2[$i]))
                        {
                            $tmp .= "\t".$tmp2[$i];
                            if($i < count($tmp2)-1)$tmp .= ",";
                            $tmp .= CR;
                        }
                    }
                }

                if(in_array($key, array('WHERE')))
                {
                    $tmp2 = explode(' AND ', $tmp);
                    $tmp = "";
                    for($i=0; $i < count($tmp2); $i++)
                    {
                        $tmp2[$i] = trim($tmp2[$i]);
                        if(!empty($tmp2[$i]))
                        {
                            $tmp .= "\t".$tmp2[$i];
                            if($i < count($tmp2)-1)$tmp .= " AND";
                            $tmp .= CR;
                        }
                    }
                }

                $sql = str_replace($cons[$key], "\t".trim($tmp), $sql);
            }
        }

        $sql = str_replace(" ORDER BY ", "\nORDER BY\n\t", $sql);
        $sql = str_replace(" .*", ".*", $sql);

        die($sql);

    }


    if($_GET['action'] == 'get_fields')
    {
        $resp = array();
        $table_name = $_POST['table'];

        $sql = "SHOW FIELDS FROM `$table_name`";
        $nuts->doQuery($sql);


        $resp = $nuts->dbGetData();
        $resp_joker[] = array('Field' => '*', 'Type' => '', 'Null' => '', 'Key' => 'JOKER', 'Default' => '', 'Extra' => '');
        $resp = array_merge($resp_joker, $resp);


        die(json_encode($resp));
    }

}


// execution ***********************************************************************************************************
$nuts->open(PLUGIN_PATH.'/exec.html');

// list table
$sql = "SHOW FULL TABLES";
$nuts->doQuery($sql);
while($row = $nuts->dbFetch())
{
    $keys = array_keys($row);

    $table_type = strtolower($row[$keys[1]]);
    $table_type = str_replace('base ', '', $table_type);

    $nuts->parse('tables.table_name', $row[$keys[0]]);
    $nuts->parse('tables.table_type', $table_type);
    $nuts->loop('tables');
}




$plugin->render = $nuts->output();



?>