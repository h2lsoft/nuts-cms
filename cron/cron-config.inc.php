<?php

// parameters **********************************************************************************************************
$CRON_IP_CONTROL_ACCESS = true; // restrict cron by ip control
$CRON_IPS_ALLOWED = array(); // ip allowed
$CRON_ADMIN_ALERT = NUTS_ADMIN_EMAIL; // email security for cron

// do not edit *********************************************************************************************************

/**
 * Cron Control Access
 *
 * @param bool $send_admin_email
 * @param bool $log_message
 */
function cronControlAccess($send_admin_email=true, $log_message=true)
{
	global $job, $CRON_IP_CONTROL_ACCESS, $CRON_IPS_ALLOWED, $CRON_ADMIN_ALERT;

	if(!$CRON_IP_CONTROL_ACCESS)return;

	$user_ip = $job->getIP();
	if(!in_array($user_ip, $CRON_IPS_ALLOWED))
	{
		$msg = "IP `$user_ip` not allowed in `{$_SERVER['PHP_SELF']}`";

		// send email and trace
		$cron_name = explode('/', $_SERVER['PHP_SELF']);
		$cron_name = $cron_name[count($cron_name)-2];
		$cron_name = strtoupper($cron_name);

		// log message
		if($log_message)
		{
			xTrace($cron_name, $msg, 0, 'cron');
		}

		// send email
		if($send_admin_email)
		{
			$job->mailFrom($CRON_ADMIN_ALERT);
			$job->mailTo($CRON_ADMIN_ALERT);
			$job->mailSubject("[".APP_TITLE."] CRON> {$cron_name}> IP access forbidden");
			$job->mailBody($msg);
			$job->mailSend();
		}

		die($msg);
	}

}












