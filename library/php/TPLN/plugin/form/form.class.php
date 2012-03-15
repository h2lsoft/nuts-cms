<?php
/**
 * TPLN Form Plugin
 * @package Template Engine
 */
class Form extends Mail
{
	protected  $new_objects_name = array(); // Array of names objects
	protected  $msg_err = array(); // Array of error messages
	protected  $last_obj = ''; // the last object on parameter
	protected  $objError = array(); // array of error object  / allows to indicate them in javascript
	protected  $objErrorMsg = array(); // array of errors for a possible customisation
	protected  $custom_msg = ''; // Personalize the errors messages
	protected  $formErrorLang = TPLN_LANG; // recover the language per defect
	protected  $formName = ''; // name of formulaire per defect
	protected  $init = 0; // initialize data of the formulaire ?
	protected  $row_val = array(); // contains the array of values
	protected  $error_display_mode = 'I'; // T: top ou bien I: Inline
	protected  $captcha = 0; // captcha activated

	/**
	 * @var string Form error color
	 */
	public $formErrorColor = "#FF4444"; // error color

	/**
	 * Return formatted message
	 *
	 * @param int $index
	 * @param array $parameters
	 * @return string
	 */
	public function getMessage($index, $parameters=array())
	{
		global $tpln_form_lng;

		$msg = $tpln_form_lng[$this->formErrorLang][$index];
		//die($msg);
		if(count($parameters) > 0)
		{
			//print_r($parameters);
			$msg = vsprintf($msg, $parameters);
		}

		if(TPLN_OUTPUT_CHARSET != 'utf-8')
			$msg = utf8_decode($msg);

		return $msg;
	}


	/**
	 * Add a translation for object
	 *
	 * @param string $object
	 * @param string $objects_name
	 */
	public function formSetObjectName($object, $objects_name)
	{
		$this->new_objects_name[$object] = $objects_name;
	}


	/**
	 * rewrite a new name object
	 *
	 * @param array $objects_name
	 */
	public function formSetObjectNames($objects_name= array())
	{
		$this->new_objects_name = $objects_name;
	}

	/**
	 * get the new name of the object
	 *
	 * @return string
	 */
	protected function formGetUserObjectName()
	{
		if(isset($this->new_objects_name[$this->last_obj]))
			return $this->new_objects_name[$this->last_obj];
		return $this->last_obj;
	}

	/**
	 * This method allows you to chose the place of form error messages T:
	 *
	 *  display messages on top (bloc form_error obliged) I (by default): display messages below html widget
	 *
	 * @param string $mode
	 * @author H2LSOFT */
	public function formSetDisplayMode($mode)
	{
		$this->error_display_mode = $mode;
	}

	/**
	 * This method allows you to modify form name.
	 *
	 * @param string $formName
	 * @author H2LSOFT */
	public function formSetName($formName)
	{
		$this->formName = $formName;
	}

	/**
	 * this method tests if there is personalized message error
	 * customize error message
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function errorCustom()
	{
		// recover the objects of error for the signals in javascript
		if(!in_array($this->last_obj, $this->objError))
		{
			$this->objError[] = $this->last_obj;
		}

		if(!empty($this->custom_msg))
		{
			$this->msg_err[] = $this->custom_msg;
			$this->objErrorMsg[$this->last_obj][] = $this->custom_msg;

			$this->custom_msg = '';
			return true;
		}
		else
		{
			$this->custom_msg = '';
			return false;
		}
	}

	/**
	 * Verify if treatment protection has be done
	 * @var bool
	 */
	private $formXEntitiesPostDone = false;


	/**
	 * this method
	 * @param string $obj
	 *
	 * @return boolean
	 * @author H2LSOFT */
	public function rules($obj)
	{
		if($_POST)
		{
			// apply htmlentites to protect data
			if(!$this->formXEntitiesPostDone && $this->dbProtectionMode)
			{
				$_POST = $this->formXEntities($_POST);
				$this->formXEntitiesPostDone = true;
			}

			// pull out the [] which is array
			$array = false;
			$obj2 = $obj;
			if(substr($obj2, -2, 2) == '[]')
			{
				$obj2 = substr($obj2, 0, strlen($obj2)-2);
				$array = true;
			}

			if(!isset($_POST[$obj2]))
			{
				if($array)
					$_POST[$obj2] = array(); #avoid the bugs IE, FIREFOX
				else
					$_POST[$obj2] = ''; #avoid the bugs IE, FIREFOX
			}

			if(empty($obj))$obj = $this->last_obj;
			if(empty($obj))
			{
//			if($this->formErrorLang == 'fr')
//				die("TPLN Form: `$obj` Aucun objet trouvé");
//			else
//				die("TPLN Form: `$obj` No object found");

				$obj_name = $this->formGetUserObjectName();
				//die($this->getMessage(0, array($obj_name)));
				trigger_error($this->getMessage(0, array($obj_name)), E_USER_ERROR);
			}

			$this->last_obj = $obj;
			return true;
		}

		return false; # obligatory
	}

	/**
	 * This method initializes data of HTML form by convertion of php parameter.
	 *
	 * It is usefull for editing a record of a table.
	 *
	 * @param array $arr
	 * @author H2LSOFT */
	public function formInit($arr)
	{
		$this->init = 1;
		$this->row_val = $arr;
	}

	/**
	 * this method allows you to record an error message
	 *
	 * which will be posted with the next error.
	 *
	 * @param string $msg
	 * @author H2LSOFT */
	public function setMessage($msg) // personalize an error message

	{
		if(TPLN_OUTPUT_CHARSET == 'utf-8')
			$this->custom_msg = $msg;
		else
			$this->custom_msg = utf8_decode($msg);
	}

	/**
	 * this method allows you to ckeck if the object isn't empty.
	 *
	 *  If that's the case then alarm error is started.
	 *
	 * @param string $obj
	 * @param string $msg
	 *
	 * @author H2LSOFT */
	public function notEmpty($obj = '', $msg= '') // check if the object isn't empty

	{
		if(is_array($obj))
		{
			foreach($obj as $cur_obj)
				$this->notEmpty($cur_obj);
			return;
		}

		if(!$this->rules($obj))return;

		$this->setMessage($msg);


		// if notEmpty is used on file objet then fileControl will be called instead
		if(isset($_FILES[$this->last_obj]))
		{
			$this->fileControl($this->last_obj, 1);
			return;
		}

		// Tow diffrents treantments for arrays and the others types
		$obj2 = str_replace('[]', '', $this->last_obj);
		$err = false;

		if(!is_array($_POST[$obj2]))
		{
			if(strlen(trim($_POST[$obj2])) == 0)
				$err = true;
		}
		elseif(count($_POST[$obj2]) == 0)
		{
			$err = true;
		}

		if($err)
		{
			if(!$this->errorCustom())
			{
				//$msg = "Field '$this->last_obj' can not be empty";
				//if($this->formErrorLang == 'fr')
				//$msg = "Le champ '$this->last_obj' ne peut être vide";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(1, array($obj_name));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
			// $this->objErrorMsg[$this->last_obj][] = $msg;
		}

		$this->custom_msg = ''; # remove custom error message
	}

	/**
	 * this method allows to ckeck that the value is a number,
	 *
	 *  if parameter floatnumber is true, Digit will accept a float number.
	 *
	 * @param string $obj
	 * @param boolean $float_accepted
	 *
	 * @author H2LSOFT */
	public function onlyDigit($obj = '', $float_accepted = 0)
	{
		if(!$this->rules($obj))return;

		if($float_accepted)
		{
			// $res = ereg('^[+-]?[0-9]*\.?[0-9]+$', $_POST[$this->last_obj]);
			$res = preg_match('/^[+-]?[0-9]*\.?[0-9]+$/', $_POST[$this->last_obj]);
		}
		else
		{
			$res = ctype_digit($_POST[$this->last_obj]);
		}

		if(!empty($_POST[$this->last_obj]) && !$res)
		{
			if(!$this->errorCustom())
			{
				//if($this->formErrorLang == 'fr')
				//	$msg = "Le champ '$this->last_obj' ne peut contenir que des chiffres";
				//else
				//	$msg = "Field '$this->last_obj' can only countain digit";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(2, array($obj_name));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}
		$this->custom_msg = ''; # remove custom error message


	}

	/**
	 * This method allows you to verify $_POST value is equal
	 *
	 * @param string $obj
	 * @param string $value
	 * @author H2LSOFT */
	public function equal($obj = '', $value)
	{
		$this->isEqual($obj = '', $value);

	}

	/**
	 * This method allows you to verify $_POST value is equal
	 *
	 * @param string $obj
	 * @param string $value
	 *
	 * @deprecated
	 *
	 * @author H2LSOFT */
	public function isEqual($obj = '', $value)
	{
		if(!$this->rules($obj))return;
		if(!empty($_POST[$this->last_obj]) && $_POST[$this->last_obj] != $value)
		{
			if(!$this->errorCustom())
			{
				//if($this->formErrorLang == 'fr')
				//	$msg = "Le champ '$this->last_obj' doit être égale à '$value'";
				//else
				//	$msg = "Field '$this->last_obj' must be equal at '$value'";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(3, array($obj_name, $value));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}

		$this->custom_msg = '';
	}

	/**
	 * This method allows to verify $_POST value is in the list
	 *
	 * @param string $obj
	 * @param array $value
	 * @author H2LSOFT */
	public function inList($obj = '', $value)
	{
		$this->inList($obj = '', $value);
	}

	/**
	 * This method allows to verify $_POST value is in the list
	 *
	 * @param string $obj
	 * @param array $arr_value
	 * @param boolean $case_sensitive
	 *
	 * @deprecated
	 *
	 * @author H2LSOFT */
	public function isInList($obj = '', $arr_value, $case_sensitive = false)
	{
		if(!$this->rules($obj))return;

		$obj = str_replace('[]', '', $obj);
		$val = $_POST[$obj];


		$arr_value_o = $arr_value;
		if(!$case_sensitive)
		{
			if(!is_array($val))
				$val = strtolower($val);
			else
				$val = array_map('strtolower', $val);

			$arr_value = array_map('strtolower', $arr_value);
		}

		$err = false;
		if(is_array($val))
		{
			foreach($val as $v)
			{
				if(!in_array($v, $arr_value, true))
				{
					$err = true;
					break;
				}
			}
		}
		else
		{
			if(!in_array($val, $arr_value, true))
			{
				$err = true;
			}
		}


		if($err)
		{
			if(!$this->errorCustom())
			{
				$values = join(', ', $arr_value_o);

				//if($this->formErrorLang == 'fr')
				//	$msg = "Le champ '$this->last_obj' doit être dans la liste à '$values'";
				//else
				//	$msg = "Field '$this->last_obj' must be in the list '$values'";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(4, array($obj_name, $values));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * this method allows to check if the value contained is only letters characters.
	 *
	 * @param string $obj
	 * @author H2LSOFT */
	public function onlyLetter($obj = '')
	{
		if(!$this->rules($obj))return;
		if(!ctype_alpha($_POST[$this->last_obj]))
		{
			if(!$this->errorCustom())
			{
				//if($this->formErrorLang == 'fr')
				//	$msg = "Le champ '$this->last_obj' ne peut contenir que des lettres";
				//else
				//	$msg = "Field '$this->last_obj' can only countain letters";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(5, array($obj_name));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}

		$this->custom_msg = '';
	}

	/**
	 * this method allows to check if the value given to the object is an email.
	 *
	 * @param string $obj
	 * @author H2LSOFT */
	public function email($obj = '')
	{
		if(!$this->rules($obj))return;

		//if(!eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $_POST[$this->last_obj]) && (!empty($_POST[$this->last_obj])))
		//if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/i", $_POST[$this->last_obj]) && (!empty($_POST[$this->last_obj])))
		$regexp = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
		if(!empty($_POST[$this->last_obj]) && !preg_match($regexp, $_POST[$this->last_obj]))
		{
			if(!$this->errorCustom())
			{
				//if($this->formErrorLang == 'fr')
				//	$msg = "Le champ '$this->last_obj' n'est pas une adresse mail valide";
				//else
				//	$msg = "Field '$this->last_obj' is not a valid mail address";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(6, array($obj_name));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * this method allows to check that the value is N length.
	 *
	 * @param string $obj
	 * @param int $length
	 * @author H2LSOFT */
	public function charLength($obj = '', $length)
	{
		if(!$this->rules($obj))return;
		if(strlen($_POST[$this->last_obj]) != $length)
		{
			if(!$this->errorCustom())
			{
				//if($this->formErrorLang == 'fr')
				//	$msg = "Le champ '$this->last_obj' doit contenir '$length' caractères";
				//else
				//	$msg = "Field '$this->last_obj' must contain '$length' characters";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(7, array($obj_name,$length));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * method allows you to put lenght minimum for a chain of character.
	 *
	 * @param string $obj
	 * @param int $length
	 * @author H2LSOFT */
	public function minLength($obj = '', $length)
	{
		if(!$this->rules($obj))return;
		if(!empty($_POST[$this->last_obj]) && strlen($_POST[$this->last_obj]) < $length)
		{
			if(!$this->errorCustom())
			{
				//if($this->formErrorLang == 'fr')
				//	$msg = "Le champ '$this->last_obj' doit contenir au moins '$length' caractères";
				//else
				//	$msg = "Field '$this->last_obj' must contain at least '$length' characters";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(8, array($obj_name,$length));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * this method allows you to check that the object does not exceed the N lenght.
	 *
	 * @param string $obj
	 * @param int $length
	 * @author H2LSOFT */
	public function maxLength($obj = '', $length)
	{
		if(!$this->rules($obj))return;
		if(strlen($_POST[$this->last_obj]) > $length)
		{
			if(!$this->errorCustom())
			{
				//if($this->formErrorLang == 'fr')
				//	$msg = "Le champ '$this->last_obj' doit contenir au maximum '$length' caractères";
				//else
				//	$msg = "Field '$this->last_obj' must contain '$length' characters maximum";

				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(9, array($obj_name, $length));

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * method allows to check if the object datas converted as parameters correspond to the pattern converted as parameter.
	 *
	 * The last argument(facultative) corresponds to the message in the event of error.
	 *
	 * @param string $pattern
	 * @param string $obj
	 * @param string $mess
	 * @author H2LSOFT */
	public function regexControl($pattern, $obj = '', $mess)
	{
		if(!$this->rules($obj))return;


		// if(is_array($_POST[$this->last_obj]) || (!empty($_POST[$this->last_obj]) && !eregi($pattern, $_POST[$this->last_obj])))
		if(is_array($_POST[$this->last_obj]) || (!empty($_POST[$this->last_obj]) && !preg_match('/'.$pattern.'/i', $_POST[$this->last_obj])))
		{
			$this->setMessage($mess);
			$this->errorCustom();
		}
		$this->custom_msg = '';
	}

	/**
	 * This method allows to control if object countains only alphabetic caracters
	 *
	 * @param string $obj
	 * @param string $except
	 * @author H2LSOFT */
	public function alpha($obj, $except = '')
	{
		// verify if the obj is on parameter, per defect or is missed
		if(!$this->rules($obj)) return;

		//if($this->formErrorLang == 'fr')
		//	$mess = "Le champ '$this->last_obj' contient des caractères non alphabétique";
		//else
		//	$mess = "Field '$this->last_obj' countains non alphabetic characters";

		$obj_name = $this->formGetUserObjectName();
		$mess = $this->getMessage(5, array($obj_name));

		$pattern = '[[:alpha:]]';
		if(!empty($except))
		{
			$tmp_pattern = preg_quote($except);
			// $pattern = '('.$pattern.'|'.$tmp_pattern.')';
			$pattern = "[a-zA-Z$tmp_pattern]";
		}

		if(!empty($this->custom_msg))
			$mess = $this->custom_msg;

		$this->regexControl('^'.$pattern.'*$', $obj, $mess);
		$this->custom_msg = '';
	}

	/**
	 * This method allows to control if object countains only alphanumeric characters
	 *
	 * @param string $obj
	 * @param string $except
	 * @author H2LSOFT */
	public function alphaNumeric($obj, $except = '')
	{
		// verify if the obj is on parameter, per defect or is missed
		if(!$this->rules($obj)) return;

		//if($this->formErrorLang == 'fr')
		//	$mess = "Le champ '$this->last_obj' contient des caractères non alphanumérique";
		//else
		//	$mess = "Field '$this->last_obj' countains non alphanumeric characters";

		$obj_name = $this->formGetUserObjectName();
		$mess = $this->getMessage(10, array($obj_name));

		$pattern = '[[:alnum:]]';
		if(!empty($except))
		{
			$tmp_pattern = preg_quote($except);
			// $pattern = '('.$pattern.'|'.$tmp_pattern.')';
			$pattern = "[a-zA-Z0-9$tmp_pattern]";

		}

		if(!empty($this->custom_msg))
			$mess = $this->custom_msg;

		//die('^'.$pattern.'*$');
		$this->regexControl('^'.$pattern.'*$', $obj, $mess);
		$this->custom_msg = '';
	}

	/**
	 * this method allows to verify a html form object is well filled. Format parameter must be a string composed by :
	 *
	 * d -> day m -> month y -> year 2 digits Y -> year 4 digits H -> hour i -> minutes s -> seconds
	 *
	 * @param string $obj
	 * @param string $format
	 * @author H2LSOFT */
	public function date($obj, $format)
	{
		$this->isDate($obj, $format);
	}

	/**
	 * This method allows to verify a html form object is well filled. Format parameter must be a string composed by :
	 *
	 * d -> day m -> month y -> year 2 digits Y -> year 4 digits H -> hour i -> minutes
	 *
	 * @param string $obj
	 * @param string $format
	 *
	 * @return boolean
	 * @see date
	 * @deprecated
	 * @author H2LSOFT */
	public function isDate($obj, $format)
	{
		// verify if the obj is on parameter, per defect or is missed
		if(!$this->rules($obj)) return;

		$pattern = $format;
		$pattern = str_replace('/', '\/', $pattern);

		$pattern = str_replace('d', '[0-9]{2}', $pattern);
		$pattern = str_replace('m', '[0-9]{2}', $pattern);
		$pattern = str_replace('y', '[0-9]{2}', $pattern);
		$pattern = str_replace('Y', '[0-9]{4}', $pattern);

		$pattern = str_replace('H', '[0-9]{2}', $pattern);
		$pattern = str_replace('i', '[0-9]{2}', $pattern);
		$pattern = str_replace('s', '[0-9]{2}', $pattern);

		$pattern = "^$pattern$";

		//if($this->formErrorLang == 'fr')
		//	$mess = "Le champ '$this->last_obj' n'est pas une date valide , format autorisé $format";
		//else
		//	$mess = "Field '$this->last_obj' is not a valid date, format allowed $format";

		$obj_name = $this->formGetUserObjectName();
		$mess = $this->getMessage(11,array($obj_name,$format));
		if(!empty($this->custom_msg))
			$mess = $this->custom_msg;
		$this->regexControl($pattern, $obj, $mess);
		$this->custom_msg = '';
	}

	/**
	 * This method allows to control the value of your form is a correct url.
	 *
	 * @param string $obj
	 * @param string $format
	 * @author H2LSOFT */
	public function url($obj, $format = 'http://|https://|ftp://')
	{
		// verify if the obj is on parameter, per defect or is missed
		if(!$this->rules($obj)) return;
		$format2 = str_replace('/', '\/', $format);
		//$format2 = preg_quote($format);

		$pattern = "^($format2){0,1}[A-Za-z0-9][A-Za-z0-9\-\.]+[A-Za-z0-9]\.[A-Za-z]{2,}[\43-\176]*$";

		//if($this->formErrorLang == 'fr')
		//	$mess = "Le champ '$this->last_obj' n'est pas une url valide";
		//else
		//	$mess = "Field '$this->last_obj' is not a valid url";

		$obj_name = $this->formGetUserObjectName();
		$mess = $this->getMessage(12,array($obj_name));
		if(!empty($this->custom_msg))
			$mess = $this->custom_msg;

		$this->regexControl($pattern, $obj, $mess);
		$this->custom_msg = '';
	}

	/**
	 * method allows you to control the presence, the size and/or the extention of a file when it will be upload.
	 *
	 * Now, you can specify the size by adding Ko or Mo
	 *
	 * @param string $obj
	 * @param boolean $required
	 * @param string $size
	 * @param string $mimes
	 * @param string $ext
	 * @author H2LSOFT */
	public function fileControl($obj, $required = '', $size = '', $mimes = '', $ext = '')
	{
		// check if the obj is on parameter, per defect or is missed
		if(!$this->rules($obj)) return;
		// check if the type of controled obj is upload type


		// check required
		if(!isset($_FILES[$this->last_obj]) && $required)
		{
			if(!$this->errorCustom())
			{
				$obj_name = $this->formGetUserObjectName();
				$msg = $this->getMessage(1, array($obj_name));
				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
			$this->custom_msg = '';
			return;
		}


		if($_FILES)
		{
			if(empty($_FILES[$this->last_obj]))
			{
				// error arguments are missing
				$obj_name = $this->formGetUserObjectName();
				trigger_error($this->getMessage(13, array($obj_name)), E_USER_ERROR);
			}
			// Control of presence
			if(!is_uploaded_file($_FILES[$this->last_obj]['tmp_name']))
			{
				if($required)
				{
					if(!$this->errorCustom())
					{
						$obj_name = $this->formGetUserObjectName();
						$msg = $this->getMessage(1, array($obj_name));

						$this->msg_err[] = $msg;
						$this->objErrorMsg[$this->last_obj][] = $msg;
					}
				}
				$this->custom_msg = '';

				return;
			}

			// Control of the maximum file length
			if(!empty($size) && !is_int($size))
			{
				$taille_origin = $size;

				// replace Ko and Mo
				$size = strtolower($size);
				$size = str_replace(' ', '', $size);
				$size = str_replace('ko', '*1024', $size);
				$size = str_replace('mo', '*1024*1024', $size);
				eval("\$size = $size;");
			}

			if(!empty($size) && ($_FILES[$this->last_obj]['size'] > $size))
			{
				if(!$this->errorCustom())
				{
					$obj_name = $this->formGetUserObjectName();
					$msg = $this->getMessage(26, array($obj_name, $taille_origin));

					$this->msg_err[] = $msg;
					$this->objErrorMsg[$this->last_obj][] = $msg;
				}
				$this->custom_msg = '';
			}

			// check the extension
			$ext_arr = explode(',', $ext);
			$ext_arr = array_map('trim', $ext_arr);
			$ext_arr = array_map('strtolower', $ext_arr);

			// take the extension of the file
			preg_match("/\.([^\.]*$)/i", $_FILES[$this->last_obj]['name'], $elts);

			if(!empty($ext) && (count($elts) == 0 || !in_array(strtolower($elts[1]), $ext_arr)))
			{
				if(!$this->errorCustom())
				{
					$obj_name = $this->formGetUserObjectName();
					$msg = $this->getMessage(14, array($obj_name, $ext));

					$this->msg_err[] = $msg;
					$this->objErrorMsg[$this->last_obj][] = $msg;
				}
				$this->custom_msg = '';
			}

			// Control MIME of file type
			$mimes_arr = explode(',', $mimes);
			$mimes_arr = array_map('trim', $mimes_arr);
			$mimes_arr = array_map('strtolower', $mimes_arr);

			if(!empty($mimes) && !in_array(strtolower($_FILES[$this->last_obj]['type']), $mimes_arr))
			{
				if(!$this->errorCustom())
				{
					$obj_name = $this->formGetUserObjectName();
					$msg = $this->getMessage(15,array($obj_name,$_FILES[$this->last_obj]['type'], $mimes));

					$this->msg_err[] = $msg;
					$this->objErrorMsg[$this->last_obj][] = $msg;
				}
				$this->custom_msg = '';
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * this method allows to modify the language in which the error messages will be posted.
	 *
	 * @param string $lang
	 * @author H2LSOFT */
	public function formSetLang($lang)
	{
		$this->formErrorLang = $lang;
	}

	/**
	 * method allows you to turn an object as error.
	 *
	 * @param string $obj
	 * @param string $message
	 * @author H2LSOFT */
	public function addError($obj = '', $message = '')
	{
		if(!$this->rules($obj))return;

		if(!empty($message))
		{
			if(!$this->errorCustom())
			{
				if(TPLN_OUTPUT_CHARSET == 'utf-8')
					$msg = $message;
				else
					$msg = utf8_decode($message);
				$this->msg_err[] = $message;
			}
		}
		else
		{
			if(!$this->errorCustom())
			{
				if($this->formErrorLang == 'fr')
					$msg = "";
				else
					$msg = "";
			}
		}

		$this->msg_err[] = $msg;
		$this->objErrorMsg[$this->last_obj][] = $msg;
		$this->custom_msg = '';
	}

	/**
	 * method allows you to control the picture width and height uploaded.
	 *
	 * The object name can be forgotten if it has been used in a other method previously.
	 *
	 * @param string $obj
	 * @param int $w
	 * @param int $h
	 * @author H2LSOFT */
	public function imgStrictDimension($obj = '', $w = '', $h = '')
	{
		// check if the obj is on parameter, per defect or is missed
		if(!$this->rules($obj)) return;
		// check if there is exists one restriction at least
		if((empty($w)) && (empty($h)))
		{
			// error the arguments are missing
			//if($this->formErrorLang == 'fr')
			//	die("Argument manquant sur la methode imgStrictDimension contrôlant l'objet $this->last_obj ");
			//else
			//	die("Argument missing on the method imgStrictDimension controlling the $this->last_obj object");

			$obj_name = $this->formGetUserObjectName();
			//die($this->getMessage(16, array($obj_name)));
			trigger_error($this->getMessage(16, array($obj_name)), E_USER_ERROR);
		}
		else // at least onr argument exists

		{
			// check if the type of controled obj is upload type
			if($_FILES)
			{
				if(!isset($_FILES[$this->last_obj]))
				{
					// Erreur the obj is not correct
					//if($this->formErrorLang == 'fr')
					//	die("Erreur : La restriction imgStrictDimension peut pas contrôler l'objet $this->last_obj ou alors l'objet $this->last_obj n'existe pas");
					//else
					//	die("Error :  The restriction imgStrictDimension cannot control the object $this->last_obj or then the $this->last_obj object does not exist");

					$obj_name = $this->formGetUserObjectName();
					//die($this->getMessage(17,array($obj_name,$obj_name)));
					trigger_error($this->getMessage(17,array($obj_name,$obj_name)), E_USER_ERROR);
				}
			}

			// if we upload the picture and if the picture exixts
			if(($_FILES) &&
				(
				// eregi('^image/', $_FILES[$this->last_obj]['type'])
				stripos($_FILES[$this->last_obj]['type'], 'image/') !== false &&
				stripos($_FILES[$this->last_obj]['type'], 'image/') == 0

				) && # ,the type is picture
				($dimension = getimagesize($_FILES[$this->last_obj]['tmp_name'])) # and the picture exixts
			)
			{
				// test of the width
				if(!empty($w) && ($w != $dimension[0]))
				{
					if(!$this->errorCustom()) # if the error has already referenced

					{
						if($this->formErrorLang == 'fr') # we select the language

						{
							if(($dimension[0] - $w) > 0) # calculate the diff�rences
								$relation = 'grande';
							else
								$relation = 'petite';

							//$msg = "La largeur de l'image {$_FILES[$this->last_obj]['name']} est trop $relation de " . abs($dimension[0] - $w) . " pixels (autorisé $w px)";

							$obj_name = $this->formGetUserObjectName();
							$msg = $this->getMessage(27,array($obj_name, $relation, abs($dimension[0] - $w), $w));
							$this->custom_msg = '';
						}

						else
						{
							if(($dimension[0] - $w) > 0)
								$relation = 'big';
							else
								$relation = 'small';

							//$msg = "The width of the image {$_FILES[$this->last_obj]['name']}  is too $relation by " . abs($dimension[0] - $w) . " pixels (allowed $w px)";

							$obj_name = $this->formGetUserObjectName();
							$msg = $this->getMessage(27,array($obj_name, $relation, abs($dimension[0] - $w), $w));
							$this->custom_msg = '';
						}

						$this->msg_err[] = $msg;
						$this->objErrorMsg[$this->last_obj][] = $msg;
					}
				}

				// test of width
				if(!empty($h) && ($h != $dimension[1]))
				{
					if(!$this->errorCustom())
					{
						if($this->formErrorLang == 'fr')
						{
							if(($dimension[1] - $h) > 0)
								$relation = 'grande';
							else
								$relation = 'petite';

							//$msg = "La hauteur de l'image {$_FILES[$this->last_obj]['name']} est trop $relation de " . abs($dimension[1] - $h) . " pixels (autorisé $h px)";

							$obj_name = $this->formGetUserObjectName();
							$msg = $this->getMessage(28,array($obj_name, $relation, abs($dimension[1] - $h),$h));
							$this->custom_msg = '';
						}
						else
						{
							if(($dimension[1] - $h) > 0)
								$relation = 'big';
							else
								$relation = 'small';

							//$msg = "The height of the image {$_FILES[$this->last_obj]['name']}  is too $relation by " . abs($dimension[1] - $h) . " pixels (allowed $h px)";

							$obj_name = $this->formGetUserObjectName();
							$msg = $this->getMessage(28,array($obj_name, $relation, abs($dimension[1] - $h),$h));
							$this->custom_msg = '';
						}

						$this->msg_err[] = $msg;
						$this->objErrorMsg[$this->last_obj][] = $msg;
					}
				}
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * This method allows to control the width of uploaded image.
	 *
	 * The list of supported operator is : - '<' - '<=' - '=' - '>' - '>='
	 *
	 * @param string $obj
	 * @param string $operator
	 * @param int $width
	 * @author H2LSOFT */
	public function imgControlWidth($obj = '', $operator, $width)
	{
		// check if the obj is on parameter, per defect or is missed
		if(!$this->rules($obj)) return;

		// check if there is exists one restriction at least
		if(!in_array($operator, array('<', '<=', '=', '>', '>=')))
		{
			// error the arguments are missing
			//if($this->formErrorLang == 'fr')
			//	die("Operateur non valide sur la methode imgControlWidth controlant l'objet $this->last_obj ");
			//else
			//	die("Operator not valid on method imgControlWidth controling the $this->last_obj object");

			$obj_name = $this->formGetUserObjectName();
			//die($this->getMessage(18,array($obj_name)));
			trigger_error($this->getMessage(18,array($obj_name)), E_USER_ERROR);

		}

		if(!is_int($width))
		{
			//if($this->formErrorLang == 'fr')
			//	die("Largeur non valide sur la methode imgControlWidth contrôlant l'objet $this->last_obj ");
			//else
			//	die("Width not valid on method imgControlWidth controling the $this->last_obj object");

			$obj_name = $this->formGetUserObjectName();
			//die($this->getMessage(19,array($obj_name)));
			trigger_error($this->getMessage(19,array($obj_name)), E_USER_ERROR);

		}

		// check if the type of controled obj is upload type
		if($_FILES)
		{
			if(!isset($_FILES[$this->last_obj]))
			{
				// error the ojbect is not correct
				//if($this->formErrorLang == 'fr')
				//	die("Erreur : La restriction imgControlWidth peut pas contrôler l'objet $this->last_obj ou alors l'objet $this->last_obj n'existe pas");
				//else
				//	die("Error :  The restriction imgControlWidth can not control the object $this->last_obj or then the $this->last_obj object does not exist");

				$obj_name = $this->formGetUserObjectName();
				//die($this->getMessage(20,array($obj_name, $obj_name)));
				trigger_error($this->getMessage(20,array($obj_name, $obj_name)), E_USER_ERROR);
			}
		}

		// if we upload a picture and if the picture exixts
		if(($_FILES) && # if we upload
			(
			// eregi('^image/', $_FILES[$this->last_obj]['type'])
			stripos($_FILES[$this->last_obj]['type'], 'image/') !== false &&
			stripos($_FILES[$this->last_obj]['type'], 'image/') == 0
			) && # the type is picture
			($dimension = getimagesize($_FILES[$this->last_obj]['tmp_name'])) # and the picture exists
		)
		{
			// test of width
			$res = false;

			if($operator == '<' && $dimension[0] < $width)$res = true;
			elseif($operator == '<=' && $dimension[0] <= $width)$res = true;
			elseif($operator == '=' && $dimension[0] = $width)$res = true;
			elseif($operator == '>' && $dimension[0] > $width)$res = true;
			elseif($operator == '>=' && $dimension[0] >= $width)$res = true;

			if(!$res)
			{
				if(!$this->errorCustom()) # if the error has already referenced

				{
					//if($this->formErrorLang == 'fr') # we select the language
					//	$msg = "La largeur de l'image {$_FILES[$this->last_obj]['name']} doit être $operator à $width pixels";
					//else
					//	$msg = "The width of the image {$_FILES[$this->last_obj]['name']} must be $operator at $width pixels";

					$obj_name = $this->formGetUserObjectName();
					$msg = $this->getMessage(21, array($obj_name, $operator, $width));

					$this->msg_err[] = $msg;
					$this->objErrorMsg[$this->last_obj][] = $msg;
					$this->custom_msg = '';
				}
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * This method allows you to control the height of uploaded image.
	 *
	 * The list of supported operator is : - '<' - '<=' - '=' - '>' - '>='
	 *
	 * @param string $obj
	 * @param string $operator
	 * @param int $width
	 * @author H2LSOFT */
	public function imgControlHeight($obj = '', $operator, $width)
	{
		// check if the obj is on parameter, per defect or is missed
		if(!$this->rules($obj)) return;
		// check if there is exists one restriction at least
		if(!in_array($operator, array('<', '<=', '=', '>', '>=')))
		{
			// error the arguments are missing
			//if($this->formErrorLang == 'fr')
			//	die("Operateur non valide sur la methode imgControlHeight contrôlant l'objet $this->last_obj ");
			//else
			//	die("Operator not valid on method imgControlHeight controling the $this->last_obj object");

			$obj_name = $this->formGetUserObjectName();
			//die($this->getMessage(18, array($obj_name)));
			trigger_error($this->getMessage(18, array($obj_name)), E_USER_ERROR);
		}

		if(!is_int($width))
		{
			//if($this->formErrorLang == 'fr')
			//	die("Hauteur non valide sur la methode imgControlHeight contrôlant l'objet $this->last_obj ");
			//else
			//	die("Height not valid on method imgControlHeight controling the $this->last_obj object");

			$obj_name = $this->formGetUserObjectName();
			//die($this->getMessage(22, array($obj_name)));
			trigger_error($this->getMessage(22, array($obj_name)), E_USER_ERROR);
		}
		// check if the type of controled obj is upload type
		if($_FILES)
		{
			if(!isset($_FILES[$this->last_obj]))
			{
				// error the ojbect is not correct
				//if($this->formErrorLang == 'fr')
				//	die("Erreur : La restriction imgControlHeight ne peut pas contrôler l'objet $this->last_obj ou alors l'objet $this->last_obj n'existe pas");
				//else
				//	die("Error :  The restriction imgControlHeight can not control the object $this->last_obj or then the $this->last_obj object does not exist");

				$obj_name = $this->formGetUserObjectName();
				//die($this->getMessage(20, array($obj_name, $this->last_obj)));
				trigger_error($this->getMessage(20, array($obj_name, $this->last_obj)), E_USER_ERROR);

			}
		}
		// if we upload a picture and if the picture exixts
		if(($_FILES) && # if we upload
			(
			// eregi('^image/', $_FILES[$this->last_obj]['type'])
			stripos($_FILES[$this->last_obj]['type'], 'image/') !== false &&
			stripos($_FILES[$this->last_obj]['type'], 'image/') == 0
			) && # ,the type is picture
			($dimension = getimagesize($_FILES[$this->last_obj]['tmp_name'])) # and the picture exists
		)
		{
			// test of width
			$res = false;

			if($operator == '<' && $dimension[1] < $width)$res = true;
			elseif($operator == '<=' && $dimension[1] <= $width)$res = true;
			elseif($operator == '=' && $dimension[1] = $width)$res = true;
			elseif($operator == '>' && $dimension[1] > $width)$res = true;
			elseif($operator == '>=' && $dimension[1] >= $width)$res = true;

			if(!$res)
			{
				if(!$this->errorCustom()) # if the error has already referenced

				{
					//if($this->formErrorLang == 'fr') # we select the language
					//	$msg = "La Hauteur de l'image {$_FILES[$this->last_obj]['name']} doit être $operator à $width pixels";
					//else
					//	$msg = "The Height of the image {$_FILES[$this->last_obj]['name']} must be $operator at $width pixels";

					$obj_name = $this->formGetUserObjectName();
					$msg = $this->getMessage(23, array($obj_name, $operator, $width));

					$this->msg_err[] = $msg;
					$this->objErrorMsg[$this->last_obj][] = $msg;
					$this->custom_msg = '';
				}
			}
		}
		$this->custom_msg = '';
	}

	/**
	 * treatment of the formulaire directly in PHP
	 *
	 * @param string $elements
	 * @param string $source
	 * @author H2LSOFT */
	public function formParse($elements, $source)
	{
		//$errs = array_keys($this->objErrorMsg);
		$errs =  $this->objError;

		// capture the relevant formulaire
		if(empty($this->formName))
			$motif = "<form [^>]* [^>]*>(.*)<\/form>";
		else
			$motif = "<form [^>]* name=\"$this->formName\"[^>]*>(.*)<\/form>";

		preg_match("/$motif/mis", $source, $arr);
		$form_init = $arr[0];
		$form_html = $arr[1];

		// take all of inputs
		$inputs['__ALL'] = array();
		$motif = "<input[^>]*>";
		if(preg_match_all("/$motif/i", $form_html, $arr))
			$inputs['__ALL'] = $arr[0];

		// take all of attributs
		$_inputs = array(); // contains the name of inputs
		for($i = 0; $i < count($inputs['__ALL']); $i++)
		{
			// take the name
			$name = $this->extractStr($inputs['__ALL'][$i], 'name="', '"');
			$type = $this->extractStr($inputs['__ALL'][$i], 'type="', '"');
			$value_set = false;
			if(strpos($inputs['__ALL'][$i], 'value="') !== false)
				$value_set = true;
			$value = $this->extractStr($inputs['__ALL'][$i], 'value="', '"');
			$style_set = false;
			if(strpos($inputs['__ALL'][$i], 'style="') !== false)
				$style_set = true;
			$style = $this->extractStr($inputs['__ALL'][$i], 'style="', '"');
			$xhtml = false;

			if($inputs['__ALL'][$i][strlen($inputs['__ALL'][$i])-2] == '/')$xhtml = true;
			// add the input
			if(!empty($name))
			{
				if(!in_array($name, $_inputs))$_inputs[] = $name;
				$a = array('type' => $type, 'value_set' => $value_set, 'value' => $value, 'style_set' => $style_set, 'style' => $style, 'xhtml' => $xhtml, 'html' => $inputs['__ALL'][$i]);
				$input[$name][] = $a;
			}
		}

		// take alll of selects
		// $selects['__ALL'] = array();
		//$motif = "<select[^>]*>(.*)<\/select>";
		//if(preg_match_all("/$motif/mis", $form_html, $arr))
		//$selects['__ALL'] = $arr[0];
		$selects['__ALL'] = array();
		$tmpi = $form_html;
		$arr = array();
		do
		{
			$arri = $this->extractStr($tmpi, '<select', '</select>', true);
			$tmpi = substr($tmpi, strpos($tmpi, $arri)+ strlen($arri));
			if(!empty($arri))$selects['__ALL'][] = $arri;
		}
		while($arri);

		// take all of attributs
		$_selects = array(); // contains the name of selects
		for($i = 0; $i < count($selects['__ALL']); $i++)
		{
			// take the name
			$name = $this->extractStr($selects['__ALL'][$i], 'name="', '"');
			$style_set = false;
			if(strpos($selects['__ALL'][$i], 'style="') !== false)
				$style_set = true;
			$style = $this->extractStr($selects['__ALL'][$i], 'style="', '"');

			// take the style of options and values
			//$motif = "<option[^>]*>(.*)<\/option>";
			//if(preg_match_all("/$motif/im", $selects['__ALL'][$i], $arr))
			//$options['__ALL'] = $arr[0];
			$options['__ALL'] = array();

			$tmpi = $selects['__ALL'][$i];
			$arr = array();
			do
			{
				$arri = $this->extractStr($tmpi, '<option', '</option>', true);
				$tmpi = substr($tmpi, strpos($tmpi, $arri)+ strlen($arri));
				if(!empty($arri))$options['__ALL'][] = $arri;
			}
			while($arri);

			$option = array();
			for($j = 0; $j < count($options['__ALL']); $j++)
			{
				$value_op = $this->extractStr($options['__ALL'][$j], 'value="', '"');
				$style_op_set = false;
				if(strpos($options['__ALL'][$j], 'style="') !== false)
					$style_op_set = true;
				$style_op = $this->extractStr($options['__ALL'][$j], 'style="', '"');
				$option[] = array('value' => $value_op, 'style_set' => $style_op_set, 'style' => $style_op, 'html' => $options['__ALL'][$j], 'parsed' => $options['__ALL'][$j]);
			}

			// add the select
			if(!empty($name))
			{
				if(!in_array($name, $_selects))$_selects[] = $name;
				$select[$name][] = array('style' => $style, 'style_set' => $style_set, 'option' => $option, 'html' => $selects['__ALL'][$i]);
			}
		}

		// take the textareas
		$textareas['__ALL'] = array();
		//$motif = "<textarea[^>]*>(.*)<\/textarea>";
		//if(preg_match_all("/$motif/i", $form_html, $arr))
		//$textareas['__ALL'] = $arr[0];
		$tmpi = $form_html;
		$arr = array();
		do
		{
			$arri = $this->extractStr($tmpi, '<textarea', '</textarea>', true);
			$tmpi = substr($tmpi, strpos($tmpi, $arri)+ strlen($arri));
			if(!empty($arri))$textareas['__ALL'][] = $arri;
		}
		while($arri);

		// take all of attributs
		$_textareas = array(); // contains name of selects
		for($i = 0; $i < count($textareas['__ALL']); $i++)
		{
			// take the name
			$name = $this->extractStr($textareas['__ALL'][$i], 'name="', '"');
			$style_set = false;
			if(strpos($textareas['__ALL'][$i], 'style="') !== false)
				$style_set = true;
			$style = $this->extractStr($textareas['__ALL'][$i], 'style="', '"');
			$value = $this->extractStr($textareas['__ALL'][$i], '>', '</textarea>');

			// add the input
			if(!empty($name))
			{
				if(!in_array($name, $_textareas))$_textareas[] = $name;
				$textarea[$name][] = array('value' => $value, 'style_set' => $style_set, 'style' => $style, 'html' => $textareas['__ALL'][$i]);
			}
		}

		// assign our values ***************************************************************************
		$form_parsed = $form_init;
		$border_color = "border:1px solid ".$this->formErrorColor.";";
		$bg_color = "background-color:".$this->formErrorColor.";";

		foreach($elements as $key => $val)
		{
			$name = $key;
			$name_init = $key;
			$multiple_values = false;
			$values = array();
			if(is_array($elements[$key]))
			{
				$multiple_values = true;
				$name = $name . '[]';
			}

			// input ?
			if(in_array($name, $_inputs))
			{
				if(in_array($input[$name][0]['type'], array('text', 'hidden', 'password', 'file')))
				{
					$t = $input[$name][0]['html'];
					// value existante or not ?
					if(!$input[$name][0]['value_set'])
					{
						// XHTML ?
						if($input[$name][0]['xhtml'])
							$t = str_replace('/>', ' value="' . $val . '" />', $t);
						else
							$t = str_replace('>', ' value="' . $val . '">', $t);
					}
					else
					{
						$t = str_replace('value="' . $input[$name][0]['value'] . '"', ' value="' . $val . '"', $t);
					}
					// input text error? ****************************************************************************
					if(in_array($name, $errs) && ($input[$name][0]['type'] == 'text' || $input[$name][0]['type'] == 'password' || $input[$name][0]['type'] == 'file'))
					{
						$t2 = '';
						if($this->error_display_mode == 'I')
						{
							$t2 = '<span style="color:'.$this->formErrorColor.'; font-weight:bold;">';
							for($j = 0; $j < count($this->objErrorMsg[$name]); $j++)
							{
								$t2 .= $this->objErrorMsg[$name][$j];
								$t2 .= '<br>';
							}
							$t2 .= "</span>";
						}

						$style = trim($input[$name][0]['style']);
						if(!empty($style) && $style[strlen($style)-1] != ';')$style .= '; ';
						// Style to place ?
						if(!$input[$name][0]['style_set'])
						{
							if($input[$name][0]['xhtml'])
								$t = str_replace(' />', ' style="' . $border_color . '" />', $t);
							else
								$t = str_replace('>', ' style="' . $border_color . '" >', $t);
						}
						else
						{
							if($input[$name][0]['xhtml'])
								$t = str_replace(' style="' . $input[$name][0]['style'] . '"', ' style="' . $style . $border_color . '"', $t);
							else
								$t = str_replace(' style="' . $input[$name][0]['style'] . '"', ' style="' . $style . $border_color . '"', $t);
						}
						$t = $t2 . $t;
					}
					// *******************************************************************************************
					$form_parsed = str_replace($input[$name][0]['html'], $t, $form_parsed);
				}
				// radio
				elseif($input[$name][0]['type'] == 'radio')
				{
					// group of radios have the same name
					for($i = 0; $i < count($input[$name]); $i++)
					{
						$t = $input[$name][$i]['html'];

						if($input[$name][$i]['value'] == $val)
						{
							if(strpos($input[$name][$i]['html'], ' checked') === false)
							{
								// XHTML ?
								if($input[$name][$i]['xhtml'])
									$t = str_replace(' />', ' checked />', $t);
								else
									$t = str_replace('>', ' checked >', $t);
							}
						}
						else // take off the checked

						{
							$t = str_replace(' checked', ' ', $t);
						}
						// radio error? ****************************************************************************
						if(in_array($name, $errs))
						{
							$t2 = '';
							if($i == 0 && $this->error_display_mode == 'I')
							{
								$t2 = '<span style="color:'.$this->formErrorColor.'; font-weight:bold;">';
								for($k = 0; $k < count($this->objErrorMsg[$name]); $k++)
								{
									if($k > 0)$t .= '';
									$t2 .= $this->objErrorMsg[$name][$k] . '<br />';
								}
								$t2 .= "</span>";
							}

							$style = trim($input[$name][$i]['style']);
							if(!empty($style) && $style[strlen($style)-1] != ';')$style .= '; ';
							// Style to place ?
							if(!$input[$name][$i]['style_set'])
							{
								if($input[$name][$i]['xhtml'])
									$t = str_replace(' />', ' style="' . $border_color . '" />', $t);
								else
									$t = str_replace('>', ' style="' . $border_color . '" />', $t);
							}
							else
							{
								if($input[$name][$i]['xhtml'])
									$t = str_replace(' style="' . $input[$name][$i]['style'] . '"', ' style="' . $style . $border_color . '"', $t);
								else
									$t = str_replace(' style="' . $input[$name][$i]['style'] . '"', ' style="' . $style . $border_color . '"', $t);
							}
							$t = $t2 . $t;
						}
						// *******************************************************************************************
						$form_parsed = $this->str_replace_count($input[$name][$i]['html'], $t, $form_parsed, 1);
					}
				} // simple checkbox
				elseif($input[$name][0]['type'] == 'checkbox')
				{
					// multiple values ?
					if($multiple_values)
					{
						$values = array();
						for($j = 0; $j < count($elements[$name_init]); $j++)
							$values[] = $elements[$name_init][$j];
					}

					// group of checkboxs which have the same name
					for($i = 0; $i < count($input[$name]); $i++)
					{
						$t = $input[$name][$i]['html'];
						if((!$multiple_values && $input[$name][$i]['value'] == $val) || ($multiple_values && in_array($input[$name][$i]['value'], $values)))
						{
							if(strpos($input[$name][$i]['html'], ' checked') === false)
							{
								// XHTML ?
								if($input[$name][$i]['xhtml'])
									$t = str_replace(' />', ' checked />', $t);
								else
									$t = str_replace('>', ' checked >', $t);
							}
						}
						else // take off the checked

						{
							$t = str_replace(' checked', ' ', $t);
						}

						// checkbox error ? ****************************************************************************
						if(in_array($name, $errs))
						{
							$t2 = '';
							if($i == 0 && $this->error_display_mode == 'I')
							{
								$t2 = '<span style="color:'.$this->formErrorColor.'; font-weight:bold;">';
								for($k = 0; $k < count($this->objErrorMsg[$name]); $k++)
								{
									if($k > 0)$t .= '';
									$t2 .= $this->objErrorMsg[$name][$k] . '<br />';
								}
								$t2 .= "</span>";
							}

							$style = trim($input[$name][$i]['style']);
							if(!empty($style) && $style[strlen($style)-1] != ';')$style .= '; ';
							// Style to place ?
							if(!$input[$name][$i]['style_set'])
							{
								if($input[$name][$i]['xhtml'])
									$t = str_replace(' />', ' style="' . $border_color . '" />', $t);
								else
									$t = str_replace('>', ' style="' . $border_color . '" >', $t);
							}
							else
							{
								if($input[$name][$i]['xhtml'])
									$t = str_replace(' style="' . $input[$name][$i]['style'] . '"', ' style="' . $style . $border_color . '"', $t);
								else
									$t = str_replace(' style="' . $input[$name][$i]['style'] . '"', ' style="' . $style . $border_color . '"', $t);
							}
							$t = $t2 . $t;
						}
						// *******************************************************************************************
						$form_parsed = $this->str_replace_count($input[$name][$i]['html'], $t, $form_parsed, 1);
					}
				}
			}
			// textarea ?
			elseif(in_array($name, $_textareas))
			{
				$t = $textarea[$name][0]['html'];
				$t = str_replace('>' . $textarea[$name][0]['value'] . '</textarea>', '>' . $val . '</textarea>', $t);

				// textarea error ? ****************************************************************************
				if(in_array($name, $errs))
				{
					$style = trim($textarea[$name][0]['style']);
					if(!empty($style) && $style[strlen($style)-1] != ';')$style .= '; ';
					// Style to place ?
					if(!$textarea[$name][0]['style_set'])
						$t = $this->str_replace_count('>', ' style="' . $border_color . '" >', $t, 1);
					else
						$t = $this->str_replace_count(' style="' . $textarea[$name][0]['style'] . '"', ' style="' . $style . $border_color . '"', $t, 1);

					if($this->error_display_mode == 'I')
					{
						$t2 = '<span style="color:'.$this->formErrorColor.'; font-weight:bold;">';
						for($j = 0; $j < count($this->objErrorMsg[$name]); $j++)
						{
							$t2 .= $this->objErrorMsg[$name][$j];
							$t2 .= '<br />';
						}
						$t2 .= "</span>";
						$t = $t2 . $t;
					}
				}
				// *******************************************************************************************
				$form_parsed = str_replace($textarea[$name][0]['html'], $t, $form_parsed);
			}
			// select ?
			elseif(in_array($name, $_selects))
			{
				// simple select && multiple
				for($i = 0; $i < count($select[$name]); $i++)
				{
					// multiple values  ?
					if($multiple_values)
					{
						$values = array();
						for($j = 0; $j < count($elements[$name_init]); $j++)
							$values[] = $elements[$name_init][$j];
					}
					// selected
					for($j = 0; $j < count($select[$name][$i]['option']); $j++)
					{
						if((!$multiple_values && $select[$name][$i]['option'][$j]['value'] == $val) || ($multiple_values && in_array($select[$name][$i]['option'][$j]['value'], $values)))
						{
							if(strpos($select[$name][$i]['option'][$j]['html'], ' selected') === false)
								$select[$name][$i]['option'][$j]['parsed'] = $this->str_replace_count('>', ' selected>', $select[$name][$i]['option'][$j]['html'], 1);
						}
						else // remove the selected

						{
							$select[$name][$i]['option'][$j]['parsed'] = $this->str_replace_count(' selected', ' ', $select[$name][$i]['option'][$j]['html'], 1);
						}
					}
					// replace the correct select
					$t = $select[$name][$i]['html'];
					for($j = 0; $j < count($select[$name][$i]['option']); $j++)
						$t = $this->str_replace_count($select[$name][$i]['option'][$j]['html'], $select[$name][$i]['option'][$j]['parsed'], $t, 1);

					// select error ? ****************************************************************************
					if(in_array($name, $errs))
					{
						$style = trim($select[$name][$i]['style']);
						if(!empty($style) && $style[strlen($style)-1] != ';')$style .= '; ';
						// Style to place ?
						if(!$select[$name][$i]['style_set'])
							$t = $this->str_replace_count('>', ' style="' . $bg_color . '" >', $t, 1);
						else
							$t = $this->str_replace_count(' style="' . $select[$name][$i]['style'] . '"', ' style="' . $style . $bg_color . '"', $t, 1);

						if($this->error_display_mode == 'I')
						{
							$t2 = '<span style="color:'.$this->formErrorColor.'; font-weight:bold;">';
							for($j = 0; $j < count($this->objErrorMsg[$name]); $j++)
							{
								$t2 .= $this->objErrorMsg[$name][$j] . '<br />';
							}
							$t2 .= "</span>";
							$t = $t2 . $t;
						}
					}
					// *******************************************************************************************
					$form_parsed = str_replace($select[$name][$i]['html'], $t, $form_parsed);
				}
			}
		}
		return $form_parsed;
	}

	/**
	 * This method allows to get total errors, it must be palced before the method formIsValid()
	 *
	 * @return int
	 * @author H2LSOFT */
	public function formGetTotalError()
	{
		return count(array_merge(array_unique($this->msg_err), array()));
	}


	/**
	 * No protection for these values
	 * @var array
	 */
	public $formInputProtectionException = array();

	/**
	 *	Default charset for input protection
	 * @var string
	 */
	public $formInputHtmlEntitiesCharset = TPLN_OUTPUT_CHARSET;



	/**
	 * protect data agains user attacks
	 *
	 * @param array $arr
	 * @param bool $x_html_entities
	 * @param bool $x_protect_vars
	 */
	public function formXEntities($arr, $x_html_entities=true, $x_protect_vars=true)
	{
		$clean_arr = array();
		foreach($arr as $key => $val)
		{
			if(is_array($val))
			{
				$clean_arr[$key] = $this->formXEntities($arr[$key], $x_html_entities, $x_protect_vars);
			}
			else
			{
				// html entities
				if($x_html_entities && !in_array($key, $this->formInputProtectionException))
				{
					// $val = htmlentities($val, ENT_COMPAT, $this->formInputHtmlEntitiesCharset);
					$val = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $val);
				}

				// x protect data
				if(!in_array($key, $this->formInputProtectionException) && $x_html_entities)
				{
					$val = str_replace(array('{ ', ' }'), array('{', '}'), $val); # clean
					$val = str_replace(array('{', '}'), array('{ ', ' }'), $val);
				}

				$clean_arr[$key] = $val;
			}
		}

		return $clean_arr;
	}


	/**
	 * method allows to post the all prospective errors. The parameter is a boolean which allows to generate javascript.
	 *
	 * The javascript will allow to keep the data in the form during a post.
	 * @author H2LSOFT */
	public function formIsValid() // destroy the error bolck if it has not a post
	{
		// check of captcha
		if($this->captcha)
			$this->autoVerifyCaptcha();

		// capture the relevant formulaire
		if(empty($this->formName))
			$motif = "<form [^>]* [^>]*>(.*)<\/form>";
		else
			$motif = "<form [^>]* name=\"$this->formName\"[^>]*>(.*)<\/form>";
		preg_match("/$motif/ims", $this->f[$this->f_no]['buffer'], $arr);
		$form_html = $arr[0];

		if(!$_POST)
		{
			// if the block form contains form_error
			$form_error_inside = false;
			if(strpos($form_html, '<bloc::form_error>') !== false)
				$form_error_inside = true;

			if($this->init)
			{
				$form_parsed = $this->formParse($this->row_val, $this->f[$this->f_no]['buffer']);
				$this->f[$this->f_no]['buffer'] = str_replace($form_html, $form_parsed, $this->f[$this->f_no]['buffer']);
			}

			// if($form_error_inside && $this->blocExists('form_error'))
			if($this->blocExists('form_error'))
				$this->EraseBloc('form_error');

			if($this->blocExists('form_valid'))
				$this->EraseBloc('form_valid');
		}
		else
		{
			if(count($this->msg_err) > 0)
			{
				// errors treantement
				$form_parsed = $this->formParse($_POST, $this->f[$this->f_no]['buffer']);
				$this->f[$this->f_no]['buffer'] = str_replace($form_html, $form_parsed, $this->f[$this->f_no]['buffer']);

				// form valid ?
				if($this->blocExists('form_valid'))
					$this->EraseBloc('form_valid');

				// html coding errors messages
				// $this->msg_err = array_unique($this->msg_err);
				$this->msg_err = array_merge(array_unique($this->msg_err), array());

				foreach($this->msg_err as $key => $val)
					$this->msg_err[$key] = htmlentities($val, ENT_COMPAT, $this->formInputHtmlEntitiesCharset);

				if($this->error_display_mode == 'T')
				{
					for($i = 0; $i < count($this->msg_err); $i++)
					{
						$m = $this->msg_err[$i];

						if(!empty($m))
						{
							$this->Parse('form_error.msg', $m);
							$this->Loop('form_error');
						}
					}
				}
				elseif($this->error_display_mode == 'I')
				{
					if($this->blocExists('form_error'))
						$this->EraseBloc('form_error');
				}

				return false;
			}
			else
			{
				$form_error_inside_form = false;
				if($this->blocExists('form_valid'))
				{
					$f_v = $this->getBlocInFile('form_valid');
					// if the block form:error is in the form
					if(strpos($form_html, '<bloc::form_error>') !== false)$form_error_inside_form = true;
					// replace directly in the buffer
					$this->f[$this->f_no]['buffer'] = str_replace($form_html, $f_v, $this->f[$this->f_no]['buffer']);
				}

				// the block form_error is outside, it is automatically remplaced
				if(!$form_error_inside_form && $this->blocExists('form_error'))
					$this->EraseBloc('form_error');

				if($this->blocExists('form_valid'))
					$this->EraseBloc('form_valid');

				// delete the values of captcha
				$this->delCaptcha();

				return true;
			}
		}
	}

	/**
	 * This method allows to add a captcha verification useful to fight against spam attacks
	 *
	 * @return string
	 * @author H2LSOFT */
	public function getCaptcha()
	{
		$this->captcha = 1;

		// generate the number id
		if(!$_POST)$this->generateCaptcha();

		$sn = session_name();
		$sn  = ($sn == 'PHPSESSID') ? '' : '?sn='.base64_encode($sn);
		$str = '<img src="'.TPLN_WEB_PATH.'/plugin/form/captcha.php'.$sn.'" alt="Security code" style="border:0px;" /><br />'."\n";
		$str .= '<input id="tpln_captcha" name="tpln_captcha" type="text" maxlength="5" />';

		return $str;
	}

	/**
	 * delete captcha
	 *
	 * @return boolean
	 * @author H2LSOFT */
	protected function delCaptcha()
	{
		if(!isset($_SESSION))return;

		$sn = session_name();
		if($sn == 'PHPSESSID')$sn = '';
		if(empty($sn))
		{
			unset($_SESSION['tpln_captcha']);
			unset($_SESSION['tpln_captcha_nb']);
		}
		else
		{
			unset($_SESSION[$sn]['tpln_captcha']);
			unset($_SESSION[$sn]['tpln_captcha_nb']);
		}
	}

	var $captcha_max = 3;

	/**
	 * This method allows to define a number of error for the user, by default 0: illimited
	 *
	 * @param int $nb
	 * @author H2LSOFT */
	public function setCaptchaMax($nb)
	{
		$this->captcha_max = $nb;
	}

	/**
	 * generate captcha
	 *
	 * @author H2LSOFT */
	public function generateCaptcha()
	{
		$sn = session_name();
		if($sn == 'PHPSESSID')$sn = '';

		if(empty($sn))
		{
			$_SESSION['tpln_captcha'] = substr(md5(uniqid(time())), 0, 5);
			if(!isset($_SESSION['tpln_captcha_nb']))
				$_SESSION['tpln_captcha_nb'] = 0;
			else
				$_SESSION['tpln_captcha_nb']++;

			if($_SESSION['tpln_captcha_nb'] == $this->captcha_max)
			{
				unset($_SESSION['tpln_captcha']);
				unset($_SESSION['tpln_captcha_nb']);
				//die('Error: You can not publish more than '.$this->captcha_max.' times');
				//die($this->getMessage(24,array($this->captcha_max)));
				trigger_error($this->getMessage(24,array($this->captcha_max)), E_USER_ERROR);
			}
		}
		else
		{
			$_SESSION[$sn]['tpln_captcha'] = substr(md5(uniqid(time())), 0, 5);
			if(!isset($_SESSION[$sn]['tpln_captcha_nb']))
				$_SESSION[$sn]['tpln_captcha_nb'] = 0;
			else
				$_SESSION[$sn]['tpln_captcha_nb']++;

			if($_SESSION[$sn]['tpln_captcha_nb'] == $this->captcha_max)
			{
				unset($_SESSION[$sn]['tpln_captcha']);
				unset($_SESSION[$sn]['tpln_captcha_nb']);
				//die('Error: You can not publish more than '.$this->captcha_max.' times');
				//die($this->getMessage(24,array($this->captcha_max)));
				trigger_error($this->getMessage(24,array($this->captcha_max)), E_USER_ERROR);
			}
		}
	}

	/**
	 * this method verifies automatically captcha
	 *
	 * @author H2LSOFT */
	protected function autoVerifyCaptcha()
	{
		$obj = 'tpln_captcha';

		// verify if the obj is on parameter, per defect or missed
		if(!$this->rules($obj)) return;

		$sn = session_name();

		if($sn == 'PHPSESSID')$sn = '';
		if(empty($sn))
			$session_tpln_captcha = @$_SESSION['tpln_captcha'];
		else
			$session_tpln_captcha = @$_SESSION[$sn]['tpln_captcha'];

		if(strcmp($session_tpln_captcha, $_POST['tpln_captcha']) != 0)
		{
			if(!$this->errorCustom())
			{
				//if($this->formErrorLang == 'fr')
				//	$msg = "Le code de sécurité n'est pas valide";
				//else
				//	$msg = "Your security code is not valid";

				$msg = $this->getMessage(25);

				$this->msg_err[] = $msg;
				$this->objErrorMsg[$this->last_obj][] = $msg;
			}
		}

		$this->generateCaptcha(); // force the generation of captcha
		$_POST['tpln_captcha'] = ''; // delete the field
	}

}



?>