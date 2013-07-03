<?php
/**
 * Utils for Nuts
 * @package Utils
 * @version 1.0
 */


/**
 * Verify if nuts user is logon
 *
 * @param string $access_type front-office or back-office (default `front-office`)
 * @return bool
 */
function nutsUserIsLogon($access_type='front-office')
{
	if(!session_id())@session_start();

    if($access_type == 'front-office')
    {
        if(
            isset($_SESSION['NutsGroupID']) && $_SESSION['NutsGroupID'] != '' &&
            isset($_SESSION['NutsUserID']) && $_SESSION['NutsUserID'] != '' &&
            isset($_SESSION['FrontofficeAccess']) && $_SESSION['FrontofficeAccess'] == 'YES'
        )
            return true;
    }
    else
    {
        if(
            isset($_SESSION['NutsGroupID']) && $_SESSION['NutsGroupID'] != '' &&
            isset($_SESSION['NutsUserID']) && $_SESSION['NutsUserID'] != ''
        )
            return true;
    }

	return false;
}


/**
 * Redirection to login page
 *
 * @param string type: login (default) or forbidden or logon
 */
function nutsAccessRestrictedRedirectPage($type='login')
{
	/* @var $nuts Page */
	global $nuts;

	if($type == 'login')
        $d_const = 'LOGIN_PAGE_URL_'.strtoupper($nuts->language);
	elseif($type == 'logon')
        $d_const = 'LOGON_PAGE_URL_'.strtoupper($nuts->language);
	elseif($type == 'forbidden')
        $d_const = 'PRIVATE_PAGE_FORBIDDEN_URL_'.strtoupper($nuts->language);

	$uri = constant($d_const);
	if($uri == '')
	{
		die("Error: `$d_const` not defined");
	}

	$nuts->redirect($uri);

}

/**
 * User logout
 *
 * @param array $preserve_session_key
 */
function nutsUserLogout($preserve_session_key=array())
{
	/* @var $nuts Page */
	global $nuts;

	$session_keys = array();
	foreach($preserve_session_key as $pk)
	{
		if(isset($_SESSION[$pk]))
			$session_keys[$pk] = $_SESSION[$pk];
	}

	$_SESSION = $session_keys;

	$nuts->redirect(WEBSITE_URL.'/'.$nuts->language);
}



/**
 * Check user group
 *
 * @param int $NutsGroupID
 * @return boolean
 */
function nutsUserGroupIs($NutsGroupID)
{
	$res = false;

	if(@(int)$_SESSION['NutsGroupID'] == $NutsGroupID)
	{
		return true;
	}


	return $res;
}

/**
 * Return Email list for a group
 *
 * @param int $NutsGroupID
 * @return string email separated by comma
 */
function nutsGetEmail($NutsGroupID)
{
	global $nuts;

	$sql = "SELECT DISTINCT Email FROM NutsUser WHERE Deleted = 'NO' AND NutsGroupID = $NutsGroupID";
	$nuts->doQuery($sql);

	$emails = '';
	while($row = $nuts->dbFetch())
	{
		if(!empty($emails))$emails .= ',';
		$emails .= $row['Email'];
	}

	return $emails;
}


/**
 * Is Nuts User exists
 *
 * @param string $login_key must be Login or Email
 * @param string $login_value
 * @param int $CurrentUserID (optional) user exception useful for change a login for an existing user
 * @return mixed false or NutsUserID
 */
function nutsUserExists($login_key, $login_value, $CurrentUserID = 0)
{
	/* @var $nuts Page */
	global $nuts;
	
	$sql = "SELECT 
					ID 
			FROM 
					NutsUser 
			WHERE 
					$login_key = '%s' AND
					Deleted = 'NO' AND
					ID != $CurrentUserID
			LIMIT 1";
	$nuts->dbSelect($sql, array($login_value));
	if($nuts->dbNumRows() == 1)
		return $nuts->dbGetOne();

	return false;
}


/**
 * Activate or refresh Session parameters
 *
 * @param int $NutsUserID
 * @param string $sql_fields_added (add sql fields in session)
 * @param array $preserve_session_key exception ($nuts_preserve_session_key configuration is automatically included)
 */
function nutsUserLogin($NutsUserID, $sql_fields_added = "", $preserve_session_key = array())
{
	/* @var $nuts Page */
	global $nuts, $nuts_session_preserve_keys;
	
	$preserve_session_key = array_merge($nuts_session_preserve_keys, $preserve_session_key);
	$preserve_session_key = array_unique($preserve_session_key);
	
	$NutsUserID = (int)$NutsUserID;
	if(!empty($sql_fields_added))$sql_fields_added = ', '.$sql_fields_added;
	$nuts->dbSelect("SELECT
							NutsUser.ID,
							NutsUser.ID AS uID,
							NutsUser.Login,
							NutsUser.Email,
							NutsUser.Gender,
							NutsUser.FirstName,
							NutsUser.LastName,
							NutsUser.NutsGroupID,
							NutsUser.Language,
							NutsUser.Timezone,
							NutsUser.Country,
							FrontofficeAccess,
							BackofficeAccess
							$sql_fields_added
					FROM
							NutsUser,
							NutsGroup
					WHERE
							NutsUser.NutsGroupID = NutsGroup.ID AND
							NutsUser.ID = %s AND
							NutsUser.Active = 'YES' AND
							NutsGroup.FrontofficeAccess = 'YES' AND
							NutsGroup.Deleted = 'NO' AND
							NutsUser.Deleted = 'NO'", array($NutsUserID));

	$row = $nuts->dbFetchAssoc();

	$session_keys = array();
	foreach($preserve_session_key as $pk)
	{
		if(isset($_SESSION[$pk]))
			$session_keys[$pk] = $_SESSION[$pk];
	}

	$_SESSION = $row;
	$_SESSION['NutsUserID'] = $row['ID'];
	$_SESSION = array_merge($_SESSION, $session_keys);

}


/**
 * Initialize form from session
 *
 * @param string $sql_field_mapping
 */
function nutsUserFormInit($sql_field_mapping)
{
	/* @var $nuts Page */
	global $nuts;

	$NutsUserID = (int)$_SESSION['ID'];
	$sql = "SELECT
					$sql_field_mapping
			FROM
					NutsUser
			WHERE
					ID = $NutsUserID";
	$nuts->doQuery($sql);
	$rec = $nuts->dbfetch();
	$nuts->formInit($rec);
}


/**
 * Update current user profil
 *
 * @param array $fields
 * @param boolean $session_reload
 * @param array $exclude_fields
 */
function nutsUserUpdate($fields, $session_reload=true, $exclude_fields=array())
{
	/* @var $nuts Page */
	global $nuts;

    $exclude_fields[] = 'ID';
    $exclude_fields[] = 'Deleted';
    $exclude_fields = array_unique($exclude_fields);

	$NutsUserID = (int)$_SESSION['ID'];
	$nuts->dbUpdate('NutsUser', $fields, "ID=$NutsUserID", $exclude_fields);

	if($session_reload)
	{
		include(NUTS_PLUGINS_PATH.'/_login/config.inc.php');
		nutsUserLogin($NutsUserID, $session_add_sql_fields, $session_preserve_keys);
	}

}

/**
 * Get user data
 *
 * @param int $NutsUserID default $_SESSION['uID']
 * @param array $fields default *
 */
function nutsUserGetData($NutsUserID="", $fields="*")
{
	/* @var $nuts Page */
	global $nuts;

	if(empty($NutsUserID))
		$NutsUserID = (int)$_SESSION['NutsUserID'];

	$NutsUserID = (int)$NutsUserID;

	$nuts->doQuery("SELECT $fields FROM NutsUser WHERE ID = $NutsUserID");
	$rec = $nuts->dbFetch();

	return $rec;
}

/**
 * Verify if user is correct 
 *  
 * @param string $login Login verification alphanum + `_`
 * @return boolean
 */
function nustUserLoginIsValid($login)
{
	$login = str_replace('_', '', $login);
	return ctype_alnum($login);
}

/**
 * Verify if password is correct 
 *  
 * @param string $pass Password verification alphanum + `_` + `-`
 * @return boolean
 */
function nustUserPasswordIsValid($pass)
{
	$pass = str_replace(array('_', '-'), '', $pass);
	return ctype_alnum($pass);
}

/**
 * Register new user
 * 
 * @param array fields 
 * @param array fields exception (optional)
 * @return int userID
 */
function nutsUserRegister($fields, $except=array())
{
	/* @var $nuts Page */
	global $nuts;
	
	$USER_ID = $nuts->dbInsert('NutsUser', $fields, $except, true);

	if(array_key_exists('Password', $fields))
		nutsUserSetPassword($USER_ID, $fields['Password']);
	
	return $USER_ID;
}

/**
 * Update user password
 *  
 * @param int $NutsUserID
 * @param string $password 
 */
function nutsUserSetPassword($NutsUserID, $password)
{
	/* @var $nuts Page */
	global $nuts;
	
	
	// $password = utf8_encode($password);
	
	$sql = "UPDATE 
					NutsUser 
			SET 
					Password = ENCODE('$password', '".NUTS_CRYPT_KEY."') 
			WHERE 
					ID = $NutsUserID";	
	$nuts->doQuery($sql);	
	
}

/**
 * Get user password
 * 
 * @param int $NutsUserID
 * @return string $password uncrypted password
 */
function nutsUserGetPassword($NutsUserID)
{
	/* @var $nuts Page */
	global $nuts;
	
	$sql = "SELECT 
					DECODE(Password, '".NUTS_CRYPT_KEY."') AS Password
			FROM
					NutsUser
			WHERE 
					ID = $NutsUserID";	
	$nuts->doQuery($sql);	
	$password = $nuts->dbGetOne();
	
	
	return $password;
	
}

/**
 * Allow to start/auto registered trigger
 *
 * @param $name
 * @param bool $auto_register (false)
 * @param string $description
 */
function nutsTrigger($name, $auto_register=false, $description="")
{
	global $nuts;

    $sql = "SELECT * FROM NutsTrigger WHERE Deleted = 'NO' AND Name = '".sqlX($name)."' LIMIT 1";
    $nuts->doQuery($sql);
    if(!$nuts->dbNumRows())
    {
        if($auto_register)
        {
            $f = array();
            $f['Name'] = $name;
            $f['Description'] = ucfirst($description);
            $nuts->dbInsert('NutsTrigger', $f);
        }
    }
    else
    {
        $rec = $nuts->dbFetch();
        $rec['PhpCode'] = trim($rec['PhpCode']);
        if(!empty($rec['PhpCode']))
        {
            eval($rec['PhpCode']);
        }
    }
}

/**
 * Create a new field type text
 *
 * @param $name
 * @param $label
 * @param $style
 * @param $value
 * @param $help
 * @param $text_after
 * @param $hr_after
 * @return string
 */
function nutsFormAddText($name, $label, $style, $value, $help, $text_after, $hr_after, $p_class)
{
    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';

    $str = <<<EOF

    <p class="$p_class">
        <label title="$help">$label</label>
        <input type="text" name="$name" id="$name" value="$value" style="$style" /> $text_after
    </p>

    $hr_after

EOF;

    return $str;
}

/**
 * Create a new field textarea
 *
 * @param $name
 * @param $label
 * @param $style
 * @param $value
 * @param $help
 * @param $hr_after
 * @param $p_class
 *
 * @return string
 */
function nutsFormAddTextarea($name, $label, $style, $value, $help, $hr_after, $p_class)
{
    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';

    $str = <<<EOF
    <p class="$p_class">
        <label title="$help">$label</label>
        <textarea name="$name" id="$name" style="$style">$value</textarea>
    </p>
    $hr_after

EOF;


    return $str;
}

/**
 * Create a new field htmlarea
 *
 * @param $name
 * @param $label
 * @param $style
 * @param $value
 * @param $help
 * @param $hr_after
 * @param $p_class
 *
 * @return string
 */
function nutsFormAddHtmlArea($name, $label, $style, $value, $help, $hr_after, $p_class)
{
    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';

    $style = " width:86%; $style";

    $str = <<<EOF
    <p class="$p_class">
        <label title="$help">$label</label>
        <textarea name="$name" id="$name" style="$style" class="mceEditor processed">$value</textarea>
    </p>
    $hr_after

EOF;


    return $str;
}

/**
 * Create a new field type colorpicker
 *
 * @param $name
 * @param $label
 * @param $style
 * @param $value
 * @param $help
 * @param $hr_after
 * @param $p_class
 *
 * @return string
 */
function nutsFormAddColorpicker($name, $label, $style, $value, $help, $hr_after, $p_class)
{
    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';

    $str = <<<EOF

    <p class="$p_class">
        <label title="$help">$label</label>

        <div id="{$name}_colorpicker_preview" class="widget_colorpicker_preview">&nbsp;</div>
        <input type="text" name="$name" id="$name" value="$value" style="width:50px; $style" maxlength="7" class="widget_colorpicker"  />

    </p>

    $hr_after

EOF;

    return $str;
}

/**
 * Create a new field type date or datetime
 *
 * @param $name
 * @param $label
 * @param $type (date or datetime)
 * @param $value
 * @param $help
 * @param $hr_after
 * @return string
 */
function nutsFormAddDate($name, $label, $type, $value, $help, $hr_after, $p_class)
{
    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';

    $str = <<<EOF

    <p class="$p_class">
        <label title="$help">$label</label>
        <input autocomplete="off" type="text" id="$name" name="$name" value="$value" />
        <script>inputDate('$name', '$type');</script>
    </p>

    $hr_after

EOF;

    return $str;
}

/**
 * Create a new field type boolean or booleanx
 *
 * @param $name
 * @param $label
 * @param $type (boolean or booleanx)
 * @param $help
 * @param $hr_after
 * @return string
 */
function nutsFormAddBoolean($name, $label, $type, $help, $hr_after, $p_class)
{
    global $nuts_lang_msg;

    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';
    if($type == 'boolean')
    {
        $yes_selected = 'selected';
        $no_selected = '';
    }
    else
    {
        $yes_selected = '';
        $no_selected = 'selected';
    }

    $str = <<<EOF

    <p class="$p_class">
        <label title="$help">$label</label>
        <select id="$name" name="$name">
            <option value="YES" $yes_selected>{$nuts_lang_msg[30]}</option>
            <option value="NO" $no_selected>{$nuts_lang_msg[31]}</option>
        </select>

    </p>

    $hr_after

EOF;

    return $str;
}

/**
 * Create a new field type filemanager_media or filemanager_file or filemanager
 *
 * @param $name
 * @param $label
 * @param $type (filemanager_media or filemanager_file or filemanager)
 * @param $value
 * @param $folder
 * @param $style
 * @param $help
 * @param $hr_after
 *
 * @return string
 */
function nutsFormAddFilemanager($name, $label, $type, $value, $folder, $style, $help, $hr_after, $p_class)
{
    global $nuts_lang_msg;

    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';


    $js_func = 'allBrowser';
    $js_func_image = 'icon-file.png';

    if($type == 'filemanager_image')
    {
        $js_func = 'imgBrowser';
        $js_func_image = 'icon-preview-mini.gif';
    }
    elseif($type == 'filemanager_media')
    {
        $js_func = 'mediaBrowser';
        $js_func_image = 'icon-media.png';
    }

    $str = <<<EOF

    <p class="$p_class">
        <label title="$help">$label</label>
        <input autocomplete="off" type="text" id="$name" name="$name" value="$value" style="$style" />


        <a href="javascript:;" tabindex="-1" class="tt" title="{$nuts_lang_msg[87]}" onclick="$js_func('$name','$folder');"><img class="icon" align="absmiddle" src="/nuts/img/icon-folder.png"/></a>
        <a href="javascript:;" tabindex="-1" class="tt" title="{$nuts_lang_msg[86]}" onclick="openFile('$name');"><img class="icon" align="absmiddle" src="/nuts/img/$js_func_image"/></a>
    </p>

    $hr_after

EOF;

    return $str;
}

/**
 * Create a new field type select
 *
 * @param $name
 * @param $label
 * @param $options
 * @param $value
 * @param $style
 * @param $help
 * @param $hr_after
 * @param $p_class
 *
 * @return string
 */
function nutsFormAddSelect($name, $label, $options, $value, $style, $help, $hr_after, $p_class)
{
    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';


    $cur_options = explode("\n", $options);
    $select_options = "";
    foreach($cur_options as $cur_option)
    {
        $selected = "";

        $cur_vals = explode('|', $cur_option);

        if(count($cur_vals) == 2)
        {
            $v = $cur_vals[0];
            $l = $cur_vals[1];
        }
        else
        {
            $v = $l = $cur_option;
        }

        if(!empty($value) && $v == $value)
        {
            $selected = ' selected';
        }
        $select_options .= '<option value="'.$v.'" '.$selected.'>'.$l.'</option>'.CR;

    }

    $str = <<<EOF

    <p class="$p_class">
        <label title="$help">$label</label>

        <select id="$name" name="$name" style="$style">
        $select_options
        </select>

    </p>

    $hr_after

EOF;

    return $str;
}


/**
 * Create a new field type select-sql
 *
 * @param $name
 * @param $label
 * @param $sql
 * @param $value
 * @param $style
 * @param $help
 * @param $hr_after
 * @param $p_class
 *
 * @return string
 */
function nutsFormAddSelectSql($name, $label, $sql, $value, $style, $help, $hr_after, $p_class)
{
    global $nuts;

    $hr_after = ($hr_after == 'YES') ? '<hr />' : '';


    $nuts->doQuery($sql);

    $select_options = "";
    $select_options .= "<option value=\"\"></option>".CR;
    while($r = $nuts->dbFetch())
    {
        $selected = ($r['value'] == $value) ? 'selected' : '';
        $select_options .= "<option value=\"{$r['value']}\" $selected>{$r['label']}</option>".CR;
    }


    $str = <<<EOF

    <p class="$p_class">
        <label title="$help">$label</label>

        <select id="$name" name="$name" style="$style">
        $select_options
        </select>

    </p>

    $hr_after

EOF;

    return $str;
}


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
 * @param $expiration (sql mode YYYY-MM-DD HH:II:SS)
 */
function nutsSetCache($app, $contents, $expiration)
{
	global $nuts;

	$f = array();
	$f['Date'] = 'NOW()';
	$f['Application'] = $app;
	$f['Expiration'] = $expiration;

	$nuts->dbInsert('NutsCache', $f);

}









?>