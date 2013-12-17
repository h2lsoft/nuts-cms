<?php

/**
 * Trace event in log
 *
 * @param string $app
 * @param string $action
 * @param string $resume resume action default = empty
 * @param int $recordID default value = 0
 */
function nutsTrace($app, $action, $resume='', $recordID=0)
{
	$arr = array();
	$arr['NutsGroupID'] = (int)@$_SESSION['NutsGroupID'];
	$arr['NutsUserID'] = (int)@$_SESSION['ID'];
	$arr['DateGMT'] = nutsGetGMTDate();
	$arr['Application'] = $app;
	$arr['Action'] = $action;
	$arr['Resume'] = $resume;
	$arr['IP'] = $GLOBALS['nuts']->getIP();
	$arr['IP'] = ip2long($arr['IP']);
	$arr['RecordID'] = $recordID;

	$GLOBALS['nuts']->dbInsert('NutsLog', $arr);
}

/**
 * Trace application message in Log plugin
 *
 * @param string $app_name
 * @param string $message
 * @param int $recordID optionnal
 * @param string $app_name optionnal
 */
function xTrace($action, $message, $recordID=0, $app_name='job')
{
	global $nuts;

	$qID = $nuts->dbGetQueryID();

	$f = array();
	$f['DateGMT'] = 'NOW()';
	$f['Application'] = $app_name;
	$f['Action'] = $action;
	$f['Resume'] = $message;
	$f['IP'] = ip2long($nuts->getIp());

	if($recordID)
		$f['RecordID'] = $recordID;

	$nuts->dbInsert('NutsLog', $f);

	if($qID > -1)
		$nuts->dbSetQueryID($qID);
}

