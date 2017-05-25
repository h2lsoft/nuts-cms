<?php

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
$sql = str_replace(", (SELECT ", ",\n\t(SELECT ", $sql);
$sql = str_replace("\r", "", $sql);

$res = $nuts->extractStr($sql, "(SELECT\n", "\t) ", true);
while(!empty($res))
{
	$res2 = $res;
	$res2 = str_replace(array("\n", "\t"), ' ', $res2);
	$res2 = str_replace("    ", ' ', $res2);
	$res2 = str_replace("   ", ' ', $res2);
	$res2 = str_replace("  ", ' ', $res2);
	$sql = str_replace($res, $res2, $sql);
	$res = $nuts->extractStr($sql, "(SELECT\n", "\t)", true);
}

$sql = str_replace("-- AND\n\t", "\n\t-- AND ", $sql);

die($sql);


