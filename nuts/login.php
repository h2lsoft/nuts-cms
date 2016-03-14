<?php
header("content-type:text/html; charset=utf-8");

// includes ******************************************************************
include('config.inc.php');
include('headers.inc.php');

$nuts = new NutsCore();
session_start();


// redirect index prevent error chrome for remember password ***********************************************************
if(@$_POST['redirect_index'] == 1)
{
    $r = 'index.php';
    if(isset($_POST['r']) && strpos($_POST['r'], '/nuts/') !== false)
    {
        // $r = str_replace('/nuts/index.php', 'index.php', $_POST['r']);
    }

	$nuts->redirect($r);
}

$_SESSION = array();

$nuts->dbConnect();

$langs = @preg_split('[,;]', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

$_lang = (count($langs) >= 1 || !in_array($langs[0], $nuts_language_options)) ? strtolower($langs[0]) : 'en';
if(strlen($_lang) > 2)$_lang = substr($_lang, 0, 2);
(file_exists('lang/'.$_lang.'.inc.php')) ? include('lang/'.$_lang.'.inc.php') : include('lang/en.inc.php');

// Security: check if 5 times error with same ip restricted 10 minutes
$IP = $nuts->getIP();
$IP_long = (int)ip2long($IP);


$sql = "SELECT
				COUNT(*)
		FROM
				NutsLog
		WHERE
				IP = $IP_long AND
				DATE_ADD(DateGMT, INTERVAL 10 MINUTE) >= NOW() AND
				Application = '_system' AND
				Action = 'login'";
$nuts->doQuery($sql);
$login_error_count = $nuts->dbGetOne();



if($_POST)
{
	$_POST = array_map('trim', $_POST);
	$_POST = $nuts->xssProtect($_POST);


	// security ***********************************************************************************************
	if($login_error_count >= 5) # 5 times submitting
	{

		// email preventing admin email
		if($login_error_count == 5)
		{
			$f = array();
			$f['IP'] = $nuts->getIP();
			@nutsSendEmail($nuts_lang_msg[84], $f, NUTS_ADMIN_EMAIL);
		}

		die('error_security');
	}


	// lost password ***********************************************************************************************
	if(isset($_POST['from']) && $_POST['from'] == 'lp')
	{
		if(empty($_POST['Email']) || !email($_POST['Email']))
		{
			nutsTrace('_system', 'lost_password', 'Email => '.'`'.$_POST['Email'].'`', 0);
			die('error');
		}
		else
		{
			$nuts->dbSelect("SELECT
									NutsUser.Login,
									DECODE(NutsUser.Password, '".NUTS_CRYPT_KEY."') AS Password,
									NutsUser.FirstName,
									NutsUser.LastName
							FROM
									NutsUser,
									NutsGroup
							WHERE
									NutsUser.NutsGroupID = NutsGroup.ID AND
									NutsUser.Email = '%s' AND
									NutsUser.Active = 'YES' AND
									NutsGroup.BackofficeAccess = 'YES' AND
									NutsGroup.Deleted = 'NO' AND
									NutsUser.Deleted = 'NO'", array($_POST['Email']));

			if($nuts->dbNumRows() == 1)
			{
				$row = $nuts->dbFetch();
                nutsTrace('_system', 'lost_password');
                if(!nutsSendEmail($nuts_lang_msg[48], $row, $_POST['Email']))
                {
                    $phperror = error_get_last();
                    $phperror = $phperror['message'];
                    die("error_mail;$phperror");
                }

				die('ok');
			}
			else
			{
				nutsTrace('_system', 'lost_password', 'Email => '.'`'.$_POST['Email'].'`', 0);
				die('error');
			}
		}
	}

	// connection ************************************************************************************************
	if(empty($_POST['NutsLogin']) || empty($_POST['NutsPassword']))
	{
		die('error');
	}

	$crypt_pass = nutsCrypt($_POST['NutsPassword']);
	$nuts->dbSelect("SELECT
							NutsUser.ID,
							NutsUser.FirstName,
							NutsUser.LastName,
							NutsUser.NutsGroupID
					FROM
							NutsUser,
							NutsGroup
					WHERE
							NutsUser.NutsGroupID = NutsGroup.ID AND
							NutsUser.Login = '%s' AND
							NutsUser.Password = '%s' AND
							NutsUser.Active = 'YES' AND
							NutsGroup.BackofficeAccess = 'YES' AND
							NutsGroup.Deleted = 'NO' AND
							NutsUser.Deleted = 'NO'", array($_POST['NutsLogin'], $crypt_pass));

	if($nuts->dbNumRows() == 0)
	{
		$_SESSION['NutsGroupID'] = 0;
		$_SESSION['ID'] = 0;
		nutsTrace('_system', 'login', 'error => '.'`'.htmlentities($_POST['NutsLogin']).'`'."; ".'`'.htmlentities($_POST['NutsPassword']).'`', 0);
		session_destroy();

		die('error');
	}
	else
	{
		$row = $nuts->dbFetchAssoc();
		$_SESSION = $row;
		$_SESSION['NutsUserID'] = $row['ID'];
		nutsTrace('_system', 'logon');

		$nuts->dbUpdate('NutsUser', array('LastConnection' => 'NOW()'), "ID={$_SESSION['NutsUserID']}");


		$nuts->dbClose();
		exit(1);
	}
}

$nuts->dbClose();
$nuts->open('_templates/login.html');

if($login_error_count < 5)
{
	$nuts->eraseBloc('failed');
}
else
{
	$nuts->eraseBloc('submit');
}

$IP = $nuts->getIP();
$nuts->parse('IP', $IP);

/*if(!NUTS_TRADEMARK)
	$nuts->eraseBloc('trademark');
*/

$nuts->write();

