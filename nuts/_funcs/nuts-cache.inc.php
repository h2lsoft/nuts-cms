<?php
/**
 * Cache
 * @package Functions
 * @version 1.0
 */




/**
 * Get application cache (delete expiration before)
 *
 * @param $app
 *
 * @return mixed boolean|string
 */
function nutsGetCache($app)
{
	global $nuts;

	nutsClearCache($app);

	Query::factory()->select('Content')
		->from('NutsCache')
		->whereEqualTo('Application', $app)
		->order_by('Date DESC')
		->limit(1)
		->execute();

	if($nuts->dbNumRows() == 0)
		return false;
	else
		return $nuts->dbGetOne();

}

/**
 * Clear cache application automatic with NutsGetCache
 */
function nutsClearCache($app)
{
	global $nuts;

	$sql = "DELETE FROM NutsCache WHERE Expiration <= NOW() AND Application = '".sqlX($app)."'";
	$nuts->doQuery($sql);
}

/**
 * Create a cache application
 *
 * @param $app
 * @param $contents
 * @param $expiration (sql mode YYYY-MM-DD HH:II:SS or string begins by + example + 1 hour current time + one hour)
 */
function nutsSetCache($app, $contents, $expiration)
{
	global $nuts;

	$f = array();
	$f['Date'] = 'NOW()';
	$f['Application'] = $app;
	$f['Content'] = $contents;

	if(@$expiration[0] == '+')
	{
		$expiration = date('Y-m-d H:i:s', strtotime($expiration));
	}


	$f['Expiration'] = $expiration;

	$nuts->dbInsert('NutsCache', $f);

}

