<?php
/**
 * Mail
 * @package Functions
 * @version 1.0
 */


/**
 * Send an email by nuts
 *
 * @param array $msg key: subject and body
 * @param array $data array foreach replacement
 * @param string $email mail address
 * @param boolean $ip_signature
 * @param string $from_address default NUTS_EMAIL_NO_REPLY
 * @param boolean $add_webapp_name_subject add WEBSITE_NAME to subject
 *
 * @return boolean success
 */
function nutsSendEmail($msg, $data, $email, $ip_signature=true, $from_address='', $add_webapp_name_subject=true)
{
	global $nuts;

	$subject = $msg['subject'];
	if($add_webapp_name_subject)
		$subject = '['.WEBSITE_NAME.'] '.$subject;


	$body = trim($msg['body']);

	$body = str_replace('{WEBSITE_NAME}', WEBSITE_NAME, $body);
	$body = str_replace('{WEBSITE_URL}', WEBSITE_URL, $body);

	foreach($data as $key => $val)
	{
		$body = str_replace('{'.$key.'}', $val, $body);
	}

	$body = rtrim($body);

	if($ip_signature)
	{
		$body .= "

--
Powered by Nuts
User IP: ".$nuts->getIP();
	}
	
	
	$from = (empty($from_address)) ? NUTS_EMAIL_NO_REPLY : $from_address;
	$headers = 'From: '.$from."\n";
	

	$headers .= "Content-Type: text/plain; charset=utf-8\n";

	$subject = html_entity_decode($subject);
	if(!@mail($email, $subject, $body, $headers, "-f $from"))
		return false;

	return true;

}

/**
 * Send Email throw Email module
 *
 * @param string $to seperated by comma
 * @param int $nutEmailID
 * @param array $datas to replace
 * @param boolean $xtrace (default=false)
 * @param string $app_name
 * @param string $message
 * @param int $recordID optionnal
 * @param string $app_name optionnal (default=job)
 *
 * @return boolean result
 */
function nutsMailer($to, $nutEmailID, $datas = array(), $xtrace=false, $action="", $message="", $recordID=0, $app_name='cron')
{
	global $nuts, $HTML_TEMPLATE;

	if(!isset($GLOBALS['nuts']) && isset($GLOBALS['job']) )
		$nuts = &$GLOBALS['job'];

	if(!isset($GLOBALS['NUTS_INCLUDES_EMAIL_CFG_VERIFY']))
	{
		include_once(WEBSITE_PATH."/plugins/_email/config.inc.php");
		$GLOBALS['NUTS_INCLUDES_EMAIL_CFG_VERIFY'] = true;
	}
	$GLOBALS['HTML_TEMPLATE'] = $HTML_TEMPLATE;

	$nutEmailID = (int)$nutEmailID;

	$nuts->doQuery("SELECT * FROM NutsEmail WHERE ID = $nutEmailID");
	if($nuts->dbNumRows() == 0)return false;
	$row = $nuts->dbFetch();

	// vars replacement
	$datas['WEBSITE_URL'] = WEBSITE_URL;
	$datas['WEBSITE_NAME'] = WEBSITE_NAME;

	foreach($datas as $key => $val)
	{
		$row['Subject'] = str_replace('{'.$key.'}', $val, $row['Subject']);
		$row['Body'] = str_replace('{'.$key.'}', $val, $row['Body']);
	}
	$row['Body'] = str_replace('[BODY]', $row['Body'], $HTML_TEMPLATE);

	$row['Body'] = str_replace('src="/', 'src="'.WEBSITE_URL.'/', $row['Body']);
	$row['Body'] = str_replace('href="/', 'href="'.WEBSITE_URL.'/', $row['Body']);


	// email send
	if(empty($row['Expeditor']))$row['Expeditor'] = NUTS_EMAIL_NO_REPLY;
	$nuts->mailFrom($row['Expeditor']);
	$nuts->mailCharset('utf-8');

	$row['Subject'] = html_entity_decode($row['Subject']);
	$nuts->mailSubject($row['Subject']);
	$nuts->mailBody($row['Body'], 'HTML');

	$to = explode(',', $to);

	$trt_ok = true;
	foreach($to as $t)
	{
		$t = strtolower(trim($t));
		if(!empty($t))
		{
			$nuts->mailTo($t);  // ajouté par JZ
			if(!$nuts->mailSend())
			{
				$trt_ok = false;
			}
		}
	}

	// xtrace ?
	if($xtrace)
	{
		xTrace($action, $message, $recordID, $app_name);
	}

	return $trt_ok;
}

