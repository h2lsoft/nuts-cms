<?php
/**
 * TPLN mail Plugin
 * @package Template Engine
 */
class Mail extends Rss
{
	protected $from = '';
	protected $fromLabel = '';
	protected $replyTo = '';
	protected $to = '';
	protected $cc = '';
	protected $bcc = '';
	protected $subject = '';
	protected $body = '';
	protected $format = '';
	public $files = array();
	protected $charset = 'iso-8859-1';
	protected $urgent = false;
	protected $confirm  = false;
	public $mailErr = '';

	/**
	 * this method verifies if the email adress is correct
	 *
	 * @param string $address
	 *
	 * @return boolean
	 * @author H2LSOFT */
	public function isMail($address)
	{
		if (preg_match('`([[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,4}))`', $address))
			return true;
		else
			return false;
	}

	/**
	 * This method allows to define a mail with a urgent priority.
	 *
	 * @param boolean $bool
	 * @author H2LSOFT */
	public function mailUrgent($bool)
	{
		$this->urgent = $bool;
	}

	/**
	 * This method allows to change encode by default ISO-8859-15 or UTF8 of the mail.
	 *
	 * @param string $str
	 * @author H2LSOFT */
	public function mailCharset($str)
	{
		$this->charset = $str;
	}

	/**
	 * This method allows to add a confirmation of answer to the email.
	 *
	 * @param boolean $bool
	 *
	 * @since 2.7
	 * @author H2LSOFT */
	public function mailConfirm($bool)
	{
		$this->confirm = $bool;
	}

	/**
	 * This method allows to define the sender of the mail.
	 *
	 * @param string $str
	 * @param string $label
	 * @author H2LSOFT */
	public function mailFrom($str, $label='')
	{
		$this->from = $str;
		$this->fromLabel = $label;
	}

	/**
	 *This method allows to define a email address for response.
	 *
	 * @param string $str
	 * @author H2LSOFT */
	public function mailReplyTo($str)
	{
		$this->replyTo = $str;
	}

	/**
	 * This method allows to define one of more email address of recipient.
	 *
	 * @param string $str
	 * @author H2LSOFT */
	public function mailTo($str)
	{
		$this->to = $str;
	}

	/**
	 * This method allows to send a conform copy of the mail.
	 *
	 * @param string $str
	 * @author H2LSOFT */
	public function mailCC($str)
	{
		$this->cc = $str;
	}

	/**
	 * This method allows to send a conform copy of the mail.
	 *
	 * @param string $str
	 * @author H2LSOFT */
	public function mailBCC($str)
	{
		$this->bcc = $str;
	}

	/**
	 * This methode allows to define the object of the email.
	 *
	 * @param string $str
	 * @author H2LSOFT */
	public function mailSubject($str)
	{
		$this->subject = $str;
	}

	/**
	 * This method allows to define the body of the message.
	 *
	 * @param string $str
	 * @param string $format HTML or TXT
	 * @author H2LSOFT */
	public function mailBody($str, $format='TXT')
	{
		$this->body = $str;
		$this->format = $format;
	}

	/**
	 * This method allows to attach files to the email.
	 *
	 * if $type parameter is not filled, it will affect a type regarding file extension
	 *
	 * @param string $src source content of file
	 * @param string $name file name to display
	 * @param string $type mime to apply (empty = automatic)
	 * @author H2LSOFT */
	public function mailAttachFile($src, $name, $type='')
	{
		// type of file is not filled
		if(empty($type))
		{
			// try to recognize the extention
			switch(strrchr(basename($name), "."))
			{
				case ".gz": $type = "application/x-gzip"; break;
				case ".tgz": $type = "application/x-gzip"; break;
				case ".zip": $type = "application/zip"; break;
				case ".pdf": $type = "application/pdf"; break;
				case ".png": $type = "image/png"; break;
				case ".gif": $type = "image/gif"; break;
				case ".jpg": $type = "image/jpeg"; break;
				case ".txt": $type = "text/plain"; break;
				case ".htm": $type = "text/html"; break;
				case ".html": $type = "text/html"; break;
				default: $type = "application/octet-stream";break;
			}
		}
		$this->files[] = array(
			'src' => $src,
			'name' => $name,
			'type' => $type
		);
	}

	/**
	 * This method allows to return the last error message
	 *
	 * @return string
	 * @author H2LSOFT */
	public function mailError()
	{
		return 'Error: '.$this->mailErr;
	}

	/**
	 * This method allows to send an email.
	 *
	 * @return boolean
	 * @author H2LSOFT */
	public function mailSend()
	{
		// obligatory FROM
		if(!$this->isMail($this->from))
		{
			$this->mailErr = "`$this->from` is not a valid email address";
			return false;
		}

		// obligatory TO
		$arr = explode(',', $this->to);
		foreach($arr as $to)
		{
			$to = trim($to);
			if(!$this->isMail($to))
			{
				$this->mailErr = "`$to` is not a valid email address in To";
				return false;
			}
		}

		// check REPLYTO
		if(!empty($this->replyTo))
		{
			if(!$this->isMail($this->replyTo))
			{
				$this->mailErr = "`$this->replyTo` is not a valid email address";
				return false;
			}
		}

		// headers
		$mail_mime = ($this->format == 'HTML') ? 'text/html' : 'text/plain';
		$headers = "MIME-Version: 1.0\n";

		if(!empty($this->fromLabel))$this->from = "$this->fromLabel <$this->from>";
		$headers .= "From: $this->from"."\n";
		$headers .= "Return-Path: $this->from"."\n";
		if(!empty($this->replyTo))$headers .= "Reply-To: $this->replyTo"."\n";
		if(!empty($this->cc))$headers .= "Cc: $this->cc"."\n";
		if(!empty($this->bcc))$headers .= "Bcc: $this->bcc"."\n";
		if(!empty($this->confirm))$headers .= "Disposition-Notification-To: $this->from"."\n";
		$headers .= 'X-Mailer: PHP/'.phpversion()."\n";
		if($this->urgent)
		{
			$headers .= "X-Priority: 1 (Higuest)\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= "Importance: High\n";
		}

		$body = $this->body;

		// files
		if(!count($this->files))
		{
			$headers .= "Content-type: {$mail_mime}; charset={$this->charset}\n";
		}
		else
		{
			$c_files = $this->files;
			$boundary = "b".md5(uniqid(time()));
			$headers .= "Content-Type: multipart/mixed; boundary=$boundary\n\n";

			// add body parts
			$body = "\n\n--$boundary\n";
			$body .= "Content-Type: {$mail_mime}; charset=\"{$this->charset}\"\n";
			$body .= "Content-Transfer-Encoding: 7-bit\n\n";
			$body .= trim($this->body)."\n";
			$body .= "\n\n\n\n\n\n\n\n\n";

			// construct the mime with towards
			for($i=0; $i < count($c_files); $i++)
			{
				// construct the message
				$message = chunk_split(base64_encode($c_files[$i]['src']));

				$body .= "\n";
				$body .= "--$boundary\n";
				$body .= "Content-Type:".$c_files[$i]['type']."; name=\"".$c_files[$i]['name']."\"\n";
				$body .= "Content-Transfer-Encoding: base64\n";
				$body .= "Content-Disposition: attachment;\n";
				$body .= "\n";
				$body .= "$message\n\n";
			}
		}

		// send
		if(!@mail($this->to, $this->subject, $body, $headers))
		{
			$phperror = error_get_last();
			$phperror = $phperror['message'];
			$this->mailErr = "Email has not been sent ($phperror)";
			return false;
		}

		return true;
	}

}

?>