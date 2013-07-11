<?php
/**
 * Functions
 * @package Functions
 * @version 1.0
 */

/**
 * Crypt/Decrypt a string
 *
 * @param string $str
 * @param bool $crypt
 *
 * @return crypted string
 */
function nutsCrypt($str, $crypt=true)
{
	global $nuts;

	$qID = $nuts->dbGetQueryID();

	if($crypt)
	{
		$sql = "SELECT ENCODE('".addslashes($str)."', '".NUTS_CRYPT_KEY."') AS str";
	}
	else
	{
		$sql = "SELECT DECODE('".addslashes($str)."', '".NUTS_CRYPT_KEY."') AS str";
	}

	$nuts->doQuery($sql);


	$str = $nuts->dbGetOne();
	$nuts->dbSetQueryID($qID);



	return $str;
}




/**
 * Destroy nuts session and return in login page
 */
function nutsDestroyIt($error='')
{
	$_COOKIE['NutsRemember'] = '';
	setcookie ("NutsRemember", "", time() - 3600);

	$_SESSION['NutsUserID'] = '';
	$_SESSION = array();
	unset($_SESSION);
	session_destroy();

	//header("Location: login.php");
	//exit();

    // ip_different
    $uri_added = '';
    if($error == 'ip_different')
    {
        $uri_added .= 'error=ip_different';
    }

    // redirection
    $redirect_uri = '';

    if(
			isset($_SERVER['REQUEST_URI']) &&
			$_SERVER['REQUEST_URI'] != '/nuts/index.php?mod=logout' &&
			strpos($_SERVER['REQUEST_URI'], '&do=add') === false &&
			strpos($_SERVER['REQUEST_URI'], '&do=view') === false &&
			strpos($_SERVER['REQUEST_URI'], '&do=edit') === false &&
			strpos($_SERVER['REQUEST_URI'], '&do=delete') === false

       )
    {
        if(!empty($uri_added))$uri_added .= '&';

        // remove ajax parameter
        $query_string = $_SERVER['REQUEST_URI'];
        $query_string = str_replace('&ajax=1', '', $query_string);
        $query_string = str_replace('&target=list', '', $query_string);
        $query_string = str_replace('&target=content', '', $query_string);
        $query_string = str_replace('mod=logout', '', $query_string);
	    $query_string = str_replace('&popup=&', '', $query_string);
	    $query_string = str_replace('parentID=&', '&', $query_string);
	    $query_string = str_replace('&&', '&', $query_string);

        $uri_added .= 'r='.urlencode($query_string);
    }

    if(!empty($uri_added))$uri_added = '?'.$uri_added;
	die("<script>document.location.href='login.php$uri_added';</script>");
}

/**
 * Logout treatment
 */
function nutsLogout()
{
	nutsTrace('_system', 'logout', '');
	nutsDestroyIt();
}

/**
 * Return current date in gtml mode
 *
 * @return sql-datetime sql date => 'Y-m-d H:i:s'
 */
function nutsGetGMTDate()
{
	return gmdate('Y-m-d H:i:s');
}
/**
 * Return date in GMT
 *
 * @param sql-datetime $date
 * @param string $format php date desired format
 * @param string $output user|other (user => return date with its own timezone)
 * @return date
 */
function nutsGetGMTDateUser($date, $format='', $output='user')
{
	if($date == '0000-00-00 00:00:00' || $date == '0000-00-00')return '';
	return $date;


	/*if(empty($format))
	{
		$format = 'Y-m-d H:i:s';
		if(strlen($date) == 10)
			$format = 'Y-m-d';
	}

	$timezone = $_SESSION['Timezone'];

	$year   = substr($date, 0, 4);
	$month  = substr($date, 5, 2);
	$day    = substr($date, 8, 2);
	$hour   = substr($date, 11, 2);
	$minute = substr($date, 14, 2);
	$second = substr($date, 17, 2);

	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	//Offset is in hours from gmt, including a - sign if applicable.
	//So lets turn offset into seconds
	$offset = $timezone * 60 * 60;

	if($output == 'user')
		$timestamp = $timestamp + $offset;
	else
		$timestamp = $timestamp - $offset;

	//Remember, adding a negative is still subtraction ;)
	$d = date($format, $timestamp);
	if(strlen($date) == 10)
		list($d) = explode(' ', $d);

	return $d;*/
}

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
 * Return current defined theme
 *
 * @return $theme
 */
function nutsGetTheme()
{
	/*$GLOBALS['nuts']->doQuery("SELECT Theme FROM NutsTemplateConfiguration");
	$theme = $GLOBALS['nuts']->getOne();*/

	$theme = $GLOBALS['nuts_theme_selected'];

	return $theme;
}

/**
 * Return nuts distinct content type
 *
 * @param string $type select by default
 * @return $values
 */
function nutsGetOptionsContentType($type='select')
{
	$GLOBALS['nuts']->doQuery("SELECT
										Type
								FROM
										NutsContentType");

	$rows = $GLOBALS['nuts']->getData();
	if($type == 'select')
	{
		$options = '';
		foreach($rows as $type)
		{
			$options .= '<option value="'.$type['Type'].'">'.$type['Type'].'</option>'."\n";
		}

		return $options;
	}
}

/**
 * Get nuts users list
 *
 * @param type $type default = select
 * @return $values
 */
function nutsGetOptionsUsers($type='select')
{
	$GLOBALS['nuts']->doQuery("SELECT
										ID,
										CONCAT_WS(' ',UPPER(LastName), FirstName) AS Name,
										Deleted
								FROM
										NutsUser
								ORDER BY
										Deleted DESC");


	$rows = $GLOBALS['nuts']->getData();
	if($type == 'select')
	{
		$options = '';
		foreach($rows as $user)
		{
			$style = '';
			if(strtoupper($user['Deleted']) == 'YES')
				$style = ' style="background-color:red;"';

			$options .= '<option value="'.$user['ID'].'"'.$style.'">'.$user['Name'].'</option>'."\n";
		}

		return $options;
	}
}
/**
 * Get nuts distinct theme defined
 *
 * @param string $type default= select
 * @return $options
 */
function nutsGetOptionsTemplates($type='select')
{
	/*$GLOBALS['nuts']->doQuery("SELECT
										Theme
								FROM
										NutsTemplateConfiguration");
	$theme = $GLOBALS['nuts']->getOne();*/

	$theme = nutsGetTheme();

	$tpls = (array)glob(NUTS_THEMES_PATH.'/'.$theme.'/*.html');

	if($type == 'select')
	{
		$options = '';
		foreach($tpls as $tpl)
		{
			$tpl = str_replace(NUTS_THEMES_PATH.'/'.$theme.'/', '', $tpl);
			if($tpl != 'index.html' && $tpl[0] != '_')
			{
				$options .= '<option value="'.$tpl.'">'.$tpl.'</option>'."\n";
			}
		}
		return $options;
	}
}

/**
 * get all header image defined in folder library/images/header
 *
 * @param string $type default select
 * @return $values
 */
function nutsGetOptionsHeaderImage($type='select')
{
	$imgs = (array)glob(NUTS_HEADER_IMAGES_PATH.'/*.*');
	if($type == 'select')
	{
		$options = '';
		foreach($imgs as $img)
		{
			$img = str_replace(NUTS_HEADER_IMAGES_PATH.'/', '', $img);
			$ext = substr($img, strrpos($img, '.') + 1);
			if(in_array($ext, array('gif', 'png', 'jpg', 'swf')))
			{
				$options .= '<option value="'.$img.'">'.$img.'</option>'."\n";
			}
		}
		return $options;
	}
}

/**
 * Get nuts distinct languages defined
 *
 * @param string $type default= select
 * @return array|string $values
 */
function nutsGetOptionsLanguages($type='select')
{

	$GLOBALS['nuts']->doQuery("SELECT
										LanguageDefault,
										Languages
								FROM
										NutsTemplateConfiguration");
	$row = $GLOBALS['nuts']->dbFetch();
	$tab = explode(',', trim($row['Languages']));
	$tab = array_map('trim', $tab);

	if($type == 'select')
	{
		$options = '<option value="'.strtolower($row['LanguageDefault']).'">'.nutsGetLanguage(strtolower($row['LanguageDefault'])).'</option>'."\n";
		foreach($tab as $t)
			if(!empty($t))
				$options .= '<option value="'.$t.'">'.nutsGetLanguage($t).'</option>'."\n";
	}
	elseif($type == 'array')
	{
		return $tab;
	}

	return $options;

}

/**
 * Get nuts language by iso code
 *
 * @param string $initials
 * @return string language
 */
function nutsGetLanguage($initials)
{
	global $nuts_lang_options;

	foreach($nuts_lang_options as $n)
	{
		if($n['value'] == strtolower($initials))
		{
			return $n['label'];
		}
	}
}


/**
 * Get selected language by default
 *
 * @return string $language
 */
function nutsGetDefaultLanguage()
{
	$GLOBALS['nuts']->doQuery("SELECT
										LanguageDefault
							FROM
									NutsTemplateConfiguration");
	$lng = $GLOBALS['nuts']->getOne();
	return $lng;
}

/**
 * Get distinct zone defined by user
 *
 * @return array zone
 */
function nutsGetOptionsMenu()
{
	global $nuts_lang_msg;
	$options = '<option value="0">'.$nuts_lang_msg[41].'</option>'."\n";

	$GLOBALS['nuts']->doQuery("SELECT
										ID,
										Name
								FROM
										NutsZone
								WHERE
										Type = 'MENU' AND
										Deleted = 'NO'");
	while($row = $GLOBALS['nuts']->dbFetch())
		$options .= '<option value="'.$row['ID'].'">'.$row['Name'].'</option>'."\n";

	return $options;
}

/**
 * Get page tree for a specific zone
 *
 * @param string $Language
 * @param int $ZoneID (0 = main menu)
 * @param int $NutsPageID
 * @param string $State
 * @return string $html_ul
 */
function nutsGetMenu($Language='', $ZoneID = 0, $NutsPageID = 0, $State = '', $directID = '')
{
	global $nuts_lang_msg, $lang_msg, $plugin;

	$ul = '';

	// select direct by ID
	$directIDMode = false;
	$directID = (int)$directID;
	if($directID != 0)
	{
		$GLOBALS['nuts']->doQuery("SELECT ID, Language, ZoneID, State, AccessRestricted  FROM NutsPage WHERE Deleted = 'NO' AND ID = $directID");
		if($GLOBALS['nuts']->dbNumRows() == 0)
		{
			$msg = <<<EOF
			@NO_TREE@
			<script>alert("{$lang_msg[66]}");</script>
EOF;
			die(trim($msg));
		}

		// reselect & force currect zone, language, status
		$directIDMode = true;
		$row2 = $GLOBALS['nuts']->dbFetch();

		$ul .= '<script>';
		$ul .= '$("#Language").val("'.$row2['Language'].'");';
		$ul .= '$("#ZoneID").val("'.$row2['ZoneID'].'");';
		// $ul .= '$("#Status").val("'.$row2['State'].'");';
		$ul .= '</script>';

		$Language = $row2['Language'];
		$ZoneID = $row2['ZoneID'];
		$State = $row2['State'];
		$AccessRestricted = $row2['AccessRestricted'];

	}



	if(empty($Language))
	{
		$Language = nutsGetDefaultLanguage();
	}

	$root = $nuts_lang_msg[41];
	if($ZoneID != 0)
	{
		// get zone name
		$GLOBALS['nuts']->doQuery("SELECT Name FROM NutsZone WHERE ID = $ZoneID");
		$root = $GLOBALS['nuts']->getOne();
	}


	if($NutsPageID == 0)
	{
		$ul .= '<ul class="simpleTree">'."\n";
		$ul .= '<li class="root" id="0"><span><b>'.$root.'</b></span>';
		$ul .= "<ul>\n";
	}

	$sql_state = '';
	if($directIDMode)
	{
		$sql_state = "ID = '".$directID."' AND ";
	}
	else
	{
		if(!empty($State))
		{

			$sql_state .= "State = '".addslashes($State)."' AND ";

			/*$parents_page_possible_ID = nutsGetPageIDSRecursive($Language, $ZoneID, $NutsPageID, $State);

			if(empty($parents_page_possible_ID))
			{
				$sql_state .= "State = '".addslashes($State)."' AND ";
			}
			else
			{
				// send a request to found all page ID with state
				$sql_state .= "(State = '".addslashes($State)."' OR ";
				$sql_state .= "ID IN ($parents_page_possible_ID) ";
				$sql_state .= " ) AND ";
			}*/
		}
		else
		{
			$sql_state = " NutsPageID = $NutsPageID AND ";
		}
	}

	$GLOBALS['nuts']->doQuery("SELECT
										ID,
										MenuName,
										_HasChildren,
                                        State,
										AccessRestricted
								FROM
										NutsPage
								WHERE
										Language = '".addslashes($Language)."' AND
										ZoneID = $ZoneID AND
										$sql_state
										Deleted = 'NO'
								ORDER BY
										Position");

	while($row = $GLOBALS['nuts']->dbFetch())
	{
		$ul2 = '';
		if($row['_HasChildren'] == 'YES' && empty($State))
		{
			$ajax_url = "index.php?mod={$plugin->name}&do={$plugin->action}&_action=reload_page&ID={$row['ID']}";
			$ajax_url .= "&language={$Language}";
			$ajax_url .= "&zoneID={$ZoneID}";
			$ajax_url .= "&state={$State}";

			$ul2 = '<ul class="ajax">';
			$ul2 .= '	<li>{url:'.$ajax_url.'}</li>';
			$ul2 .= '</ul>';
		}

        $img = '';
        if($row['State'] == 'DRAFT')
            $img = "<img src='img/icon-tag-edit.png' align='absbottom' />";
        elseif($row['State'] == 'WAITING MODERATION')
            $img = "<img src='img/icon-tag-moderator.png' align='absbottom' />";

		$img_lock = '';
		 if($row['AccessRestricted'] == 'YES')
			$img_lock = "<img src='img/icon-lock.png' align='absbottom' /> ";


        $ul .= "\t".'<li id="'.$row['ID'].'"><span>'.$img_lock.$row['MenuName'].'</span>'.$img.$ul2.'</li>'."\n";
	}

	if($NutsPageID == 0)
	{
		$ul .= "</ul>\n";
		$ul .= '</ul>';
	}

	return $ul;
}

/**
 * Get count page for a specific zone
 *
 * @param string $Language
 * @param int $ZoneID
 * @param string $State
 * @return int counter
 */
function nutsGetCountPages($Language, $ZoneID, $State='')
{
	global $nuts;

	if(!empty($State))
	{
		$State = "State = '$State' AND";
	}

	$sql = "SELECT
					COUNT(*)
			FROM
					NutsPage
			WHERE
					Language = '$Language' AND
					ZoneID = $ZoneID AND
					$State
					Deleted = 'NO'";
	$nuts->doQuery($sql);

	$counter = (int) $nuts->getOne();
	return $counter;
}

/**
 * Create thumbnail image
 *
 * @param string $fname
 * @param int $thumbWidth max width to resize
 * @param bool $create_new create new one with $create_new_prefix and $create_new_suffix
 * @param int $create_new_height force height (by default 0 = generated)
 * @param string $create_new_prefix prefix for image (thumb_ by default)
 * @param string $create_new_suffix suffix for image (empty by default)
 * @return boolean
 */
function createThumb($fname, $thumbWidth, $create_new = false, $create_new_height = 0, $create_new_prefix = "thumb_", $create_new_suffix = "")
{
	$tmp = explode('/', $fname);
	$file = $tmp[count($tmp)-1];

	$info = pathinfo($fname);
    $ext = strtolower($info['extension']);

	if($ext == 'jpg' || $ext == 'jpeg')$img = imagecreatefromjpeg($fname);
	elseif($ext == 'png')$img = imagecreatefrompng($fname);
	elseif($ext == 'gif')$img = imagecreatefromgif($fname);

	$width = imagesx($img);
    $height = imagesy($img);

    // calculate thumbnail size
	$new_width = $thumbWidth;

	if(!$create_new_height)
		$new_height = floor($height * ($thumbWidth / $width));
	else
		$new_height = $create_new_height;

    $tmp_img = @imagecreatetruecolor($new_width, $new_height);

    // preserve transparency
    if($ext == 'png' || $ext == 'gif')
    {
        @imagealphablending($tmp_img, false);
        @imagesavealpha($tmp_img, true);
    }

    //imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	// create new thumb
	if($create_new)
	{
		$fname = str_replace(basename($fname), $create_new_prefix.str_replace(".$ext", "", $file).$create_new_suffix.'.'.$ext, $fname);
	}

	if($ext == 'jpg' || $ext == 'jpeg')return imagejpeg($tmp_img, $fname, 100);
	elseif($ext == 'png')return imagepng($tmp_img, $fname);
	elseif($ext == 'gif')return imagegif($tmp_img, $fname);

	return false;
}

/**
 * Verify if is correct email
 *
 * @param string $email
 * @return boolean
 */
function email($email)
{
	// $pattern = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#' ;
    $pattern = "/^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+.)+[a-zA-Z]{2,5}$/";
	return preg_match($pattern, $email);

}

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

    if(empty($from_address))
        $headers = 'From: '.NUTS_EMAIL_NO_REPLY."\n";
    else
        $headers = 'From: '.$from_address."\n";

    $headers .= "Content-Type: text/plain; charset=utf-8\n";
    // $headers .= 'To: '.$email."\n";

    // utf8_decode
    // $subject = utf8_decode($subject);
    // $body = utf8_decode($body);

    $subject = html_entity_decode($subject);
    if(!@mail($email, $subject, $body, $headers))
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





/**
 * Convert the array into an array well structured
 *
 * @param array $arr
 *
 * @return array result formated
 */
function convertArrayForFormSelect($arr)
{
	$arrReturn   = array();
	foreach($arr as $key => $val)
	{
		$arrReturn[] = array('value' => $key, 'label' => $val);
	}
	return $arrReturn;
}



/**
 * Returns full name FirstName LastName of nuts user
 *
 * @param int ID optionnal = current user
 *
 * @return string
 */
function getNutsUserName($NutsUserID='')
{
	global $nuts;

	if(empty($NutsUserID))$NutsUserID = $_SESSION['NutsUserID'];

	$sql = "SELECT CONCAT(FirstName,' ', LastName) FROM NutsUser WHERE ID = $NutsUserID";
	$nuts->doQuery($sql);

	return $nuts->dbGetOne();

}

/**
 * Get an array with distinct list of email
 *
 * @param int $NutsGroupID
 * @param int $NutsUserID optionnal
 *
 * @return array
 */
function getNutsEmailList($NutsGroupID='', $NutsUserID='')
{
	global $nuts;


	$sql_added = '';
	if(!empty($NutsGroupID))
	{
		$sql_added .= "NutsGroupID = $NutsGroupID AND \n";
	}

	if(!empty($NutsUserID))
	{
		$sql_added .= "ID = $NutsUserID AND \n";
	}



	$sql = "SELECT
					DISTINCT Email
			FROM
					NutsUser
			WHERE
					$sql_added
					Deleted = 'NO'";

	$nuts->doQuery($sql);

	$arr = array();
	while($row = $nuts->dbFetch())
	{
		$arr[] = $row['Email'];
	}


	return $arr;
}

/**
 * Get nut page content
 *
 * @param int $pageID
 * @param array $fields
 *
 * @return array $res
 */
function nutsGetPage($pageID, $fields)
{
	global $nuts;

	$fields_str = join(',', $fields);
	$nuts->doQuery("SELECT $fields_str FROM NutsPage WHERE ID = $pageID");
	return $nuts->dbFetch();

}

/**
 * Convert a string to pascal case litteral (obsolete: use fromCamelCase instead)
 *
 * @param string string
 * @return string pascal case myCase => My case
 *
 */
function toPascalCase($str)
{
	$str = preg_replace('/(?<=[a-z])(?=[A-Z])/',' ', $str);

	$str = strtolower($str);
	$str = trim($str);
	$str = ucwords($str);

	return $str;
}



/**
 * Translates a camel case string into a string
 *
 * @param string $str String in camel case format
 * @param boolean $ucwords apply ucwords
 *
 * @return string $str Translated
 */
function fromCamelCase($str, $ucwords=true)
{
	$func = create_function('$c', 'return " " . $c[1];');
	$str = preg_replace_callback('/([A-Z])/', $func, $str);

	if($ucwords)$str = ucwords($str);

	return $str;
}

/**
 * Translates a string with underscores into camel case (e.g. first name -&gt; firstName)
 *
 * @param string $str String in underscore format
 * @param boolean $capitalize_first_char (If true (default), capitalise the first char in $str)
 *
 * @return string $str translated into camel caps
 */
function toCamelCase($str, $capitalize_first_char=true)
{
	if($capitalize_first_char)$str[0] = strtoupper($str[0]);

	$func = create_function('$c', 'return strtoupper($c[1]);');
	return preg_replace_callback('/ ([a-z])/', $func, $str);
}


/**
 * Convert a string to url rewrited
 *
 * @param string $str
 * @param boolean $convert_slashes (default true)
 * @param boolean $added_patterns (default empty)
 * @param boolean $added_replaces (default empty)
 * @return string
 */
function strtouri($str, $convert_slashes=true, $added_patterns=array(), $added_replaces=array())
{
    $str = trim($str);
	$str = mb_strtolower($str, 'utf8');

    $str = str_replace(array('é', 'ê', 'è', 'ë'), 'e', $str);
    $str = str_replace(array('à', 'ä', 'â'), 'a', $str);
    $str = str_replace(array('ô', 'ö', 'ô'), 'o', $str);
    $str = str_replace(array('î', 'ï'), 'i', $str);
    $str = str_replace("ù", 'u', $str);
    $str = str_replace("ç", 'c', $str);

    $str = str_replace(array(' ',"(", ")", '"', "'", '-', '%', ), '_', $str);
    $str = str_replace('?', '', $str);

    if($convert_slashes)$str = str_replace('/', '-', $str);

    $str = str_replace('___', '_', $str);
    $str = str_replace('__', '_', $str);
    $str = str_replace('..', '.', $str);
	$str = str_replace('--', '-', $str);
	$str = str_replace('-.', '.', $str);

    if(count($added_patterns))
        $str = str_replace($added_patterns, $added_replaces, $str);

	return $str;
}



/**
 * Cut a string and concat (Warning UTF-8 uses mb_* function and strip_tags is applied before)
 *
 * @param type $str
 * @param type $max_caracters (default 80)
 * @param type $concat_str
 *
 * return string cutted string
 */
function str_cut($str, $max_caracters=80, $concat_str='...'){

	$str = strip_tags($str);
	$str2 = mb_strcut($str, 0, $max_caracters, 'UTF-8');
    $str2 = trim($str2);
	if(mb_strlen($str) > $max_caracters)
	{
		$str2 .= $concat_str;
	}

	return $str2;
}



/**
 * Is website is multilang configured
 * @return boolean
 */
function isWebsiteMultiLang()
{
	$GLOBALS['nuts']->doQuery("SELECT Languages FROM NutsTemplateConfiguration");
	$lng = $GLOBALS['nuts']->getOne();

	$lng = str_replace(' ', '', $lng);
	$lng = trim($lng);

	if(!empty($lng))
		return true;

	return false;

}

/**
 * Return formatted url for a nuts page
 *
 * @param int $ID
 * @param string $Language
 * @param string $VitualPageName
 * @param boolean $TagVersion true by defaults
 *
 * @return string
 */
function nutsGetPageUrl($ID, $Language, $virtualPagename, $TagVersion=true)
{
	// force direct url
	if(preg_match('/^http/i', $virtualPagename) || (!empty($virtualPagename) && $virtualPagename[0] == '/') || (!empty($virtualPagename) && $virtualPagename[0] == '{'))
	{
		return $virtualPagename;
	}

	if(!empty($virtualPagename))$virtualPagename = '-'.$virtualPagename;
	$url = "/$Language/{$ID}{$virtualPagename}.html";

	if($TagVersion)
	{
		$url = "{@NUTS	TYPE='PAGE'	CONTENT='URL'	ID='$ID'}";
	}

	return $url;
}


/**
 * A simple function using Curl to post (GET) to Twitter
 * Kosso : March 14 2007
 *
 * @param string $username
 * @param string $password
 * @param string $message
 * @return boolean
 */
function postToTwitter($username, $password, $message){

    // $host = "http://twitter.com/statuses/update.xml?status=".urlencode(stripslashes(urldecode($message)));
    $url = "http://twitter.com/statuses/update.xml";

	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_POST, 1);
	curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$message");
	curl_setopt($curl_handle, CURLOPT_USERPWD, "$username:$password");
	$buffer = curl_exec($curl_handle);
	curl_close($curl_handle);

	// tweet no more supported
	//if(empty($buffer))
		$twitter_status = false;
	//else
		// $twitter_status = true;

    /*$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    // curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "status=$message");

    $result = curl_exec($ch);
    // Look at the returned header
    $resultArray = curl_getinfo($ch);
    curl_close($ch);

	new dBug($resultArray);

    if($resultArray['http_code'] == "200"){
         //$twitter_status = 'Your message has been sended! <a href="http://twitter.com/'.$username.'">See your profile</a>';
         $twitter_status = true;
    } else {
         //$twitter_status = "Error posting to Twitter. Retry";
         $twitter_status = false;
    }

	 */
	return $twitter_status;
}

/**
 * Transform an array to select
 *
 * @param array $options you can put multiple array for keys: label, value, optgroup
 * @param boolean $first_option_empty
 * @param string $select_name
 * @param string $attributes
 * @return string $select
 */
function array2select($options, $first_option_empty=true, $select_name="", $attributes=""){

	$select = "";
	$options_str = "";

	if($first_option_empty)
	{
		$options_str .= "<option value=\"\"></option>\n";
	}

	$last_optgroup = '';
	foreach($options as $option)
	{
		$selected = (!isset($option['selected'])) ? '' : 'selected="selected"';
		$value = $option['value'];
		$label = (!isset($option['label'])) ? $option['value'] : $option['label'];
		$optgroup = (!isset($option['optgroup'])) ? '' : $option['optgroup'];

		if(!empty($optgroup))
		{
			if($optgroup != $last_optgroup)
			{
				if(!empty($last_optgroup))
					$options_str .= "</optgroup>\n";

				$options_str .= "<optgroup label=\"$optgroup\">\n";
				$last_optgroup = $optgroup;
			}
		}

		$options_str .= "<option value=\"$value\" $selected>$label</option>\n";
	}

	if(!empty($last_optgroup))$options_str .= "</optgroup>\n";

	if(!empty($select_name))
	{
		if(!empty($attributes))$attributes .= ' '.$attributes;
		$select = "<select name=\"$select_name\" id=\"$select_name\"$attributes>\n";
		$select .= $options_str;
		$select .= "</select>\n";
	}
	else
	{
		$select = $options_str;
	}


	return $select;

}

/**
 * Convert an array to csv
 *
 * @param array $array your array
 * @param type $downloadable is file is for download ?
 * @param type $download_filename
 */
function array2csv($array, $downloadable=false, $download_filename='')
{
	$content = "";

	// lines
	$init = false;
	for($i=0; $i < count($array); $i++)
	{
		$line = $array[$i];

		if(!$init)
		{
			foreach($line as $key => $val)
			{
				$key = str_replace(';', ' ', $key);
				$content .= $key.';';
				$init = true;
			}

			$content .= CR;
		}

		foreach($line as $key => $val)
		{
			$val = str_replace(';', ',', $val);
			$val = str_replace(CR, '\n', $val);

			$content .= $val.';';
		}

		$content .= CR;
	}

	if(!$downloadable)
	{
		return $content;
	}
	else
	{
		if(empty($download_filename))
			$download_filename = date('Ymd').'.csv';

		// required for IE, otherwise Content-disposition is ignored
		if(@ini_get('zlib.output_compression'))@ini_set('zlib.output_compression', 'Off');

		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false); // required for certain browsers
		header("Content-Type: application/force-download; charset=utf-8");
		header("Content-Disposition: attachment; filename=\"".basename($download_filename)."\";");
		header("Content-Transfer-Encoding: binary");

		echo $content;
		exit;

	}


}



/**
 * Convert array to html table
 *
 * @param array $rows
 * @param array $headers_labels (optional) replace text of a th text
 * @param string $headers_style (optional) add style to th and td, example text-align:center
 * @param string $table_attributes (optional) add attibutes in table node, example border="1"
 * @param string $td_colors1 (optional) change color for td even (default: #e5e5e5)
 * @param string $td_colors2 (optional) change color for td odd (default: #ffffff)
 *
 * @return string html table formatted
 */
function array2table($rows, $headers_labels=array(), $headers_style=array(), $table_attributes="", $table_styles="",  $td_colors1='#e5e5e5', $td_colors2='#ffffff')
{
	if(!count($rows))return "";


	$str = "<table $table_attributes style=\"$table_styles\">";

	$init = false;
	$i = 0;
	foreach($rows as $row)
	{
		if(!$init)
		{
			$headers = array_keys($row);

			$str .= '<tr>';
			foreach($headers as $header)
			{
				$header_label = $header;
				if(isset($headers_labels[$header]))
					$header_label = $headers_labels[$header];

				$str .= '	<th style="'.@$headers_style[$header].'">'.$header_label.'&nbsp;</th>';
			}

			$str .= '</tr>';

			$init = true;
		}

		$str .= '<tr>';
		$td_color = ($i % 2 == 0) ? $td_colors1 : $td_colors2;
		foreach($headers as $header)
		{
			$td_style = @$headers_style[$header];
			$td_style = "background-color: $td_color; $td_style";
			$str .= '	<td style="'.$td_style.'">'.$row[$header].'&nbsp;</td>';

		}
		$str .= '</tr>';

		$i++;
	}

	$str .= "</table>";


	return $str;
}




/**
 * Return image extension for a file
 *
 * @param string $file
 * @return string image
 */
function getImageExtension($file)
{
	$file_name = basename($file);
	$exts = explode('.', $file_name);
	$ext = strtolower(end($exts));
	$ext = trim($ext);
    $ext2 = $ext;

    if(strlen($ext) >= 4)$ext2 = substr($ext, 0 , 3);

	$img = '<img src="/nuts/img/icon_extension/file.png" align="absmiddle" />';

    if(file_exists(WEBSITE_PATH."/nuts/img/icon_extension/{$ext}.png"))
        $img = "<img src=\"/nuts/img/icon_extension/{$ext}.png\" align=\"absmiddle\" />";
    elseif(file_exists(WEBSITE_PATH."/nuts/img/icon_extension/{$ext2}.png"))
        $img = "<img src=\"/nuts/img/icon_extension/{$ext2}.png\" align=\"absmiddle\" />";

    /*
	// doc
	if(in_array($ext, array('doc', 'docx', 'odt')))
	{
		$img = '<img src="/nuts/img/icon_extension/doc.png" align="absmiddle" />';
	}
	// excel
	elseif(in_array($ext, array('xls', 'xlsx', 'csv')))
	{
		$img = '<img src="/nuts/img/icon_extension/excel.png" align="absmiddle" />';
	}
	// pdf
	elseif(in_array($ext, array('pdf')))
	{
		$img = '<img src="/nuts/img/icon_extension/pdf.png" align="absmiddle" />';
	}
	// zip
	elseif(in_array($ext, array('zip')))
	{
		$img = '<img src="/nuts/img/icon_extension/zip.png" align="absmiddle" />';
	}
	// jpg, jpeg, gif, bmp, tiff, png, psd
	elseif(in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'tiff', 'psd')))
	{
		$img = '<img src="/nuts/img/icon_extension/image.png" align="absmiddle" />';
	}*/

	return $img;
}

/**
 * Format a float to be well displayed ex: 1234.5678 => 1 234.57
 * @param float $num
 * @return float
 */
function number_formatX($num)
{
	$num = number_format($num, 2, '.', ' ');
	return $num;
}

/**
 * Format a float to be well displayed ex: 1234 => 1 234
 * @param float $int
 * @return float
 */
function int_formatX($int)
{
    $int = number_format($int, 0, '.', ' ');
    return $int;
}




/**
 * Change contents for a special line for configuration file by example
 *
 * @param string $file
 * @param string $line_start
 * @param string $replacement
 * @return boolean
 */
function fileChangeLineContents($file, $line_start, $replacement)
{
	$file_contents = file_get_contents($file);
	if(!$file_contents)return false;

	$found = false;
	$lines = explode("\n", $file_contents);
	$i = 0;
	foreach($lines as $line)
	{
		$tmp_line = trim($line);
		if(strpos($tmp_line, $line_start) !== false && strpos($tmp_line, $line_start) == 0)
		{
			$lines[$i] = $replacement;
			$found = true;
			break;
		}
		$i++;
	}

	$new_file = join("\n", $lines);
	$new_file = trim($new_file);

	// save file
	if(!file_put_contents($file, $new_file))
		return false;

	return $found;

}

/**
 * get list fot tinymce spellchecker language
 */
function nutsGetSpellcheckerLanguages()
{
	global $nuts_lang_options;

    $str = '';
	$init = false;
	foreach($nuts_lang_options as $lng)
	{
		if(!empty($str))$str .= ',';

		$add = '';
		if(isset($_GET['lang']) && $_GET['lang'] == $lng['value'])
		{
			$add = '+';
			$init = true;
		}

		$str .= "$add{$lng['label']}={$lng['value']}";
	}

	if(!$init)
		$str = '+'.$str;

	return $str;
}




/**
 * Get all childrens for a page
 *
 * @param type $pageID
 * @return array with page ID inside structured
 */
function nutsPageGetChildrens($pageID, $init=false)
{
	global $nuts;

	if($init)
	{
		$IDs = array();
		$init = true;
	}

	$IDs[] = $pageID;
	$nuts->doQuery("SELECT ID FROM NutsPage WHERE NutsPageID = $pageID");
	$qID = $nuts->dbGetQueryID();
	while($pg = $nuts->dbFetch())
	{
		$pgs = nutsPageGetChildrens($pg['ID']);
		if(count($pgs) > 0)
			$IDs[] = $pgs;

		$nuts->dbSetQueryID($qID);
	}

	return $IDs;



}

/**
 * Flatten array
 *
 * @param array $a
 * @return array
 */
function array_flatten($array, $return=array())
{
	for($x = 0; $x <= count($array); $x++)
	{
		if(is_array(@$array[$x]))
		{
			$return = array_flatten($array[$x], $return);
		}
		else
		{
			if(@$array[$x])
			{
				$return[] = $array[$x];
			}
		}
	}
	return $return;
}


/**
 * Verify if user has right
 *
 * @param int $nutsUserID (empty = NutsGroupID in SESSION)
 * @return boolean
 */
function nutsUserHasRight($NutsGroupID='', $plugin, $right)
{
	global $nuts;

	$NutsGroupID = (int)$NutsGroupID;
    if(!$NutsGroupID)$NutsGroupID = $_SESSION['NutsGroupID'];


	$sql = "SELECT
					ID
			FROM
					NutsMenuRight
			WHERE
					NutsGroupID = $NutsGroupID AND
					Name = '$right' AND
					NutsMenuID IN(SELECT ID FROM NutsMenu WHERE Name = '$plugin')
			LIMIT
					1";
	$nuts->doQuery($sql);

	if(!(int)$nuts->dbNumRows())
		return false;



	return true;
}


/**
 * Generate thumbnail image dynamically from width and height attributes
 *
 * @param string $content
 * @return string content reformatted src image
 */
function smartImageResizer($content)
{
	$pattern = '/<img[^>]+>/i';
	preg_match_all($pattern, $content, $matches);

	$matches[0] = array_unique($matches[0]);

	foreach($matches[0] as $match)
	{
		if(strstr($match, 'src="/library/media/') !== false && strstr($match, 'height="') !== false && strstr($match, 'width="') !== false)
		{
			$tab = array();

			$tmp =  explode('src="', $match);
			$tmp =  explode('"', $tmp[1]);
			$tab['src'] = $tmp[0];

			$tmp =  explode('width="', $match);
			$tab['width'] = (int)$tmp[1];

			$tmp =  explode('height="', $match);
			$tab['height'] = (int)$tmp[1];

			// see original width and height
			list($original_width, $original_height, )  = @getimagesize(WEBSITE_PATH.$tab['src']);

			// see alreday defined
			$file_infos = @pathinfo($tab['src']);

            $tab['path'] = $file_infos['dirname'];
			$tab['file_name'] = $file_infos['basename'];
			$tab['extension'] = $file_infos['extension'];
			$tab['file_noext'] = $file_infos['filename'];
			$tab['file_thumbnail'] = $tab['file_noext']."-{$tab['width']}x{$tab['height']}.{$file_infos['extension']}";
			$thumb_full = WEBSITE_PATH.$tab['path'].'/'.$tab['file_thumbnail'];

			if($tab['width'] && $tab['width'] < $original_width && $tab['height'] && $tab['height'] < $original_height && isset($file_infos['filename']) && isset($file_infos['extension']) && in_array(strtolower($file_infos['extension']), array('jpg', 'png', 'gif')))
			{
				if(!file_exists($thumb_full))
				{
					createThumb(WEBSITE_PATH.$tab['src'], $tab['width'], true, $tab['height'], "", "-{$tab['width']}x{$tab['height']}");
				}
			}

            // at end force change content if user do not close window
            if($tab['width'] && $tab['height'] && isset($file_infos['filename']) && isset($file_infos['extension']) && in_array(strtolower($file_infos['extension']), array('jpg', 'png', 'gif')))
            {
                if(file_exists($thumb_full))
                {
               	    $imgX = $match;
                    $imgX = str_replace("src=\"{$tab['src']}\"", "src=\"{$tab['path']}/{$tab['file_thumbnail']}\"", $imgX);
                    $imgX = str_replace("width=\"{$tab['width']}\"", " ", $imgX);
                    $imgX = str_replace("height=\"{$tab['height']}\"", " ", $imgX);
                    $content = str_replace($match, $imgX, $content);
                }
            }
		}
	}

	return $content;
}


/**
 * Log event in file
 *
 * @param string $msg
 * @param int $level
 * @param string $file if empty use trace.log
 */
function xLog($msg, $level=0, $file="")
{
	if(empty($file))
		$file = 'trace.log';

	$contents = @file_get_contents($file);
	if(!empty($contents))
		$contents .= "\n";

	$spaces = str_repeat("\t", $level);

	$contents .= "[".date('Y-m-d H:i:s')."]\t$spaces".ucfirst(trim($msg));
	file_put_contents($file, $contents);
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

/**
 * Replace latin accent like éèêë by e for example
 *
 * @param $str
 * @return string
 */
function str_replace_latin_accents($str)
{

    $reps = array();
    $reps[] = array(
                        'pattern' => array('é', 'è', 'ê', 'ë'),
                        'replacement' => 'e'
                    );
    $reps[] = array(
                        'pattern' => array('à', 'â', 'ä', 'â'),
                        'replacement' => 'a'
                    );

    $reps[] = array(
                        'pattern' => array('ç'),
                        'replacement' => 'c'
    );


    $reps[] = array(
                        'pattern' => array('ÿ'),
                        'replacement' => 'y'
    );

    $reps[] = array(
                        'pattern' => array('û', 'ü', 'ù'),
                        'replacement' => 'u'
    );

    $reps[] = array(
                        'pattern' => array('î', 'ï'),
                        'replacement' => 'i'
    );

    $reps[] = array(
                        'pattern' => array('ö', 'ô'),
                        'replacement' => 'o'
    );


    foreach($reps as $rep)
    {
        $str = str_replace($rep['pattern'], $rep['replacement'], $str);

        $rep['pattern'] = array_map('strtoupper', $rep['pattern']);
        $str = str_replace($rep['pattern'], strtoupper($rep['replacement']), $str);
    }


    return $str;
}

/**
 * Transform Csv file to structured array
 *
 * @param $file_name
 * @param string $separator  (default = `;`)
 * @param bool $ignore_first_line  (default = true)
 * @param bool $first_line_as_key  (default = false)
 * @param bool $encode_utf8 (default = false)
 *
 * @return array
 */
function csv2array($file_name, $separator=';', $ignore_first_line=true, $first_line_as_key=false, $encode_utf8=false)
{
    $arr = array();
    $keys = array();

    $init = false;
    $lines = file($file_name);
    foreach($lines as $line)
    {
        $cols = explode($separator, $line);
        $cols = array_map('trim', $cols);
        if($encode_utf8)$cols = array_map('utf8_encode', $cols);
        if($ignore_first_line && !$init)
        {
            if($first_line_as_key)
            {
                $keys = array_map('toCamelCase', $cols);
                $keys = array_map('str_replace_latin_accents', $keys);
            }
        }

        if($init || !$ignore_first_line)
        {
            if($first_line_as_key)
            {
                $tmp = array();
                $i = 0;
                foreach($keys as $key)
                {
                    $tmp[$key] = $cols[$i];
                    $i++;
                }

                $arr[] = $tmp;
            }
            else
            {
                $arr[] = $cols;
            }


        }

        $init = true;
    }


    return $arr;
}


/**
 * Protect sql paramater against Xss attacks
 *
 * @param $str
 * @return string
 */
function sqlX($str)
{
    return strtr($str, array("\x00" => '\x00', "\n" => '\n', "\r" => '\r', '\\' => '\\\\', "'" => "\'", '"' => '\"', "\x1a" => '\x1a'));
}


/**
 * Get Youtube player
 *
 * @param string $player_id
 * @param $youtube_url
 * @param string $width
 * @param string $height
 * @param string $attributes
 * @return string
 */
function youtubeGetPlayer($player_id, $youtube_url, $width='', $height='', $attributes="")
{
    // get default parameters
    $video_width = 640;
    $video_height = 360;

    if(!empty($width))$video_width = $width;
    if(!empty($height))$video_height = $height;


    // get video id by url => http://stackoverflow.com/questions/3392993/php-regex-to-get-youtube-video-id
    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $youtube_url, $matches);
    $youtube_ID = @$matches[0];


    $player = "<iframe class=\"nuts_youtube_iframe_player\" type=\"text/html\" frameborder=\"0\" ";
    $player .= " id=\"nuts_youtube_iframe_player_$player_id\" ";
    $player .= " width=\"$video_width\" ";
    $player .= " height=\"$video_height\" ";
    $player .= " src=\"http://www.youtube.com/embed/$youtube_ID?HD=1&modestbranding=1&showinfo=0&rel=0\" ";
    $player .= " $attributes ";
    $player .= "></iframe>";

    return $player;
}

/**
 * Get Dailymotion player
 *
 * @param string $player_id
 * @param $url url
 * @param string $width
 * @param string $height
 * @param string $attributes
 * @return string
 */
function dailymotionGetPlayer($player_id, $url, $width='', $height='', $attributes="")
{
    // get default parameters
    $video_width = 640;
    $video_height = 360;

    if(!empty($width))$video_width = $width;
    if(!empty($height))$video_height = $height;

    preg_match('#http://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $url, $matches);
    $video_ID = @$matches[1];

    $player = "<iframe class=\"nuts_dailymotion_iframe_player\" frameborder=\"0\" ";
    $player .= " id=\"nuts_dailymotion_iframe_player_$player_id\" ";
    $player .= " width=\"$video_width\" ";
    $player .= " height=\"$video_height\" ";
    $player .= " src=\"http://www.dailymotion.com/embed/video/$video_ID?logo=0\" ";
    $player .= " $attributes ";
    $player .= "></iframe>";

    return $player;

}



/**
 * Return filesize in Mo
 *
 * @param string $file_path
 * @param string $suffix (` Mo` by default)
 * @return string
 */
function getFileSize($file_path, $suffix=' Mo')
{
    $size = @filesize($file_path);
    $size = bcdiv($size, 1048576, 2);
    if($size == 0)$size = 0.1;
    $size = number_formatX($size);
    $size_label = $size.' '.$suffix;
    $size_label = trim($size_label);

    return $size_label;

}

/**
 * Return audio flash player with html5 fallback by default in swf
 *
 * @param $player_id
 * @param $url
 * @param $params
 *
 * @return string
 */
function mediaGetAudioPlayer($player_id, $url, $params)
{
    // classic
    $width = 200;
    $height = 20;
    $swf_suffix = '';

    if(@$params['type'] == 'MINI')
    {
        $width = 160;
        $height = 20;
        $swf_suffix = '-mini';
    }
    elseif(@$params['type'] == 'BUBBLE')
    {
        $width = 260;
        $height = 65;
        $swf_suffix = '-bubble';
    }

    // autostart ?
    $html5_params_add = '';
    $url_param = '';
    if(@$params['autoplay'] == 'YES')
    {
        $url_param .= '&amp;autostart=true';
        # $html5_params_add .= ' autoplay';
    }

    // loop ?
    if(@$params['autoreplay'] == 'YES')
    {
        $url_param .= '&amp;autoreplay=true';
        $html5_params_add .= ' loop ';
    }


    $player = <<<EOF
<div id="nuts_audio_player_{$player_id}" class="nuts_audio_player">
    <object type="application/x-shockwave-flash" data="../library/js/dewplayer/dewplayer{$swf_suffix}.swf?mp3={$url}&amp;showtime=1{$url_param}" width="$width" height="$height">
        <param name="wmode" value="transparent" />
        <param name="movie" value="../library/js/dewplayer/dewplayer{$swf_suffix}.swf?mp3={$url}&amp;showtime=1" />

        <audio oncontextmenu="return false;" controls="controls" $html5_params_add>
            <source src="{$url}">
        </audio>

    </object>
</div>
EOF;

    // full html 5
    if(@$params['type'] == 'HTML 5')
    {
        $html5_params_add = '';
        if(@$params['autoplay'] == 'YES')$html5_params_add .= ' autoplay';
        if(@$params['autoreplay'] == 'YES')$html5_params_add .= ' loop';

        $player = <<<EOF
<div id="nuts_audio_player_{$player_id}" class="nuts_audio_player">
        <audio oncontextmenu="return false;" controls="controls" $html5_params_add>
            <source src="{$url}">
        </audio>
</div>
EOF;
    }

    return $player;
}

/**
 * Get ID from string useful for example with ajax component
 *
 * @param $str
 * @param string optional $separator (default '(')
 * @return int|null if zero
 */
function getIDFromString($str, $separator='(')
{
    $tmp = explode($separator, $str);

    $ID = '';
    if(count($tmp) >= 2)
    {
        $ID = (int)end($tmp);
        if($ID == 0)$ID = '';
    }

    return $ID;
}

/**
 * Recursive delete whole directory and files and directory itself
 *
 * @param $path without slashes at end
 */
function rm_r($path)
{
	foreach(glob($path . '/*') as $file) {
		if(is_dir($file))
			rm_r($file);
		else
			unlink($file);
	}

	rmdir($path);
}


/**
 * Verify if ajaxer is requested
 * @return bool
 */
function ajaxerRequested()
{
	return (@$_GET['ajaxer'] == 1);
}

/**
 * Verify if action if allower
 * @param string $action
 *
 * @return bool
 */
function ajaxerAction($action)
{
	return (@$_GET['_action'] == $action);
}


/**
 * Construct url for ajaxer
 *
 * @param string $action (ajax action)
 * @param string $plugin_name (plugin name if empty current plugin)
 * @param string $plugin_default_action (if empty 'list')
 * @param string|array $params_added parameters added
 *
 * @return mixed
 */
function ajaxerUrlConstruct($action, $plugin_name='', $plugin_default_action='list', $params_added='')
{
	if(empty($plugin_name))
		$plugin_name = PLUGIN_NAME;

	$uri = "index.php?mod={$plugin_name}&do={$plugin_default_action}&ajaxer=1&_action={$action}&t=".time();

	if(is_array($params_added))
	{
		$tmp = '';
		foreach($params_added as $key => $val)
		{
			$tmp .= "&{$key}=".urlencode($val);
		}
	}

	$uri .= $params_added;
	$uri = str_replace('&&', '&', $uri);
	return $uri;
}





?>