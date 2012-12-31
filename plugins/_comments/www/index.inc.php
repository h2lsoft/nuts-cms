<?php

include("{$plugin->plugin_path}/config.inc.php");

/* @var $plugin Page */
include(WEBSITE_PATH."/plugins/_page-manager/config.inc.php");
$hfs = array_map('trim', explode(",", $hidden_fields));

if(!isset($plugin->vars['Comments']) || $plugin->vars['Comments'] == 'NO' || in_array('Comments', $hfs)){
	$plugin->setNutsContent('');
}
else
{
	// language
	if(!file_exists($plugin->plugin_path.'/www/'.$plugin->vars['Language'].'.inc.php'))
		include($plugin->plugin_path.'/www/en.inc.php');
	else
		include($plugin->plugin_path.'/www/'.$plugin->vars['Language'].'.inc.php');

	// form configuration
	$plugin->formSetDisplayMode('T');
	$plugin->formSetName("form_post_comment");
	$plugin->formSetObjectNames(array('Name' => $p_lng['Name'], 'Email' => $p_lng['Email'], 'Website' => $p_lng['Website'], 'Comment' => $p_lng['Comment']));

	// captcha
	if($comments_captcha)
	{
		if(!session_id())session_start();
		$nuts->setCaptchaMax(-1);
		$GLOBALS['tpln_captcha'] = $nuts->getCaptcha();
	}

	// display & apply translation
	$plugin->openPluginTemplate();
	if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_comments/style.css');

	if(!$comments_captcha)
		$plugin->eraseBloc ('captcha');

	foreach($p_lng as $key => $val)
	{
		if(!in_array($key, array('Comment', 'AdminMailMessage', 'SuscriberMailMessage', 'MailSubject')))
			$plugin->parse($key, $val);
	}

	// loops comments
    $urix = sqlX($_SERVER['SCRIPT_URL']);
	$sql = "SELECT
	                *,
	                UNIX_TIMESTAMP(Date) AS tDate
	        FROM
	                NutsPageComment
	        WHERE
	                Deleted = 'NO' AND
                    Visible = 'YES' AND
                    (
                        NutsPageID = {$plugin->vars['ID']} OR
                        NutsPageID = 0 AND Url = '$urix'
                    )
            ORDER BY
                    Date";
	$plugin->doQuery($sql);
	if($plugin->dbNumRows() == 0)
	{
		$plugin->eraseBloc("nuts_comments");
	}
	else
	{
		$plugin->parse("CommentNb", $plugin->dbNumRows());

		$k = 1;
		$tos = explode(',', $comments_admin_email);
		while($row = $plugin->dbFetch())
		{
			$plugin->parse("nuts_comments.ID", $row["ID"]);

			$comment_admin_class = '';
			if($row["NutsUserID"] != 0 || in_array($row["Email"], $tos))
				$comment_admin_class = " comment_admin";
			$plugin->parse("nuts_comments.comment_admin_class", $comment_admin_class);

			$author = $row["Name"];
			if(!empty($row["Website"]))
				$author = '<a href="'.$row["Website"].'">'.$author.'</a>';

			$plugin->parse("nuts_comments.CAuthor", addslashes($row["Name"]));
			$plugin->parse("nuts_comments.CommentAuthor", $author);
			$plugin->parse("nuts_comments.CommentDate", date($comments_date_format, $row['tDate']));
			$plugin->parse("nuts_comments.CommentText", $row['Message']);
			$plugin->parse("nuts_comments.CommentID", $k);

			// gravatar
			$email = $row["Email"];
			$default = (empty($comments_avatar_default_image_url)) ? WEBSITE_URL.'/plugins/_comments/www/anonymous.gif': $comments_avatar_default_image_url;
			$size = $comments_avatar_size;
			$grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?d=".urlencode($default)."&s=".$size;
			$plugin->parse("nuts_comments.Avatar", $grav_url);

			$plugin->loop("nuts_comments");
			$k++;
		}
	}

	// form
	$plugin->notEmpty("Name");
	$plugin->notEmpty("Email");
	$plugin->email("Email");
	$plugin->notEmpty("Comment");
	$plugin->url("Website");

	// words forbidden
	if($_POST)
	{
		$f_words = explode(", ", $comments_forbidden_words);
		$f_words = array_map('trim', $f_words);
		$err_lang = ($plugin->vars['Language'] == 'fr') ? "Le mot `%s` est un mot interdit" : "Word `%s` is a forbidden word";
		foreach($f_words as $f_word)
		{
			if(!empty($f_word) && stripos($_POST['Comment'], $f_word) !== false)
			{
				$err_lang = vsprintf($err_lang, array($f_word));
				$plugin->addError("Comment", $err_lang);
				break;
			}
		}
	}



	$html_tags = ' - ';
	if(!empty($comments_message_allowed_tags))
	{
		$html_tags = join(', ', $comments_message_allowed_tags);
	}
	$plugin->parse('html_tags', $html_tags);

	if($plugin->formIsValid())
	{
		$plugin->sanitizePost();

		$_POST['Name'] = ucfirst($_POST['Name']);
		$_POST['Email'] = strtolower($_POST['Email']);
		$_POST['Website'] = strtolower($_POST['Website']);

		if(!empty($_POST['Website']) && !preg_match('/^http/', $_POST['Website']))
			$_POST['Website'] = 'http://'.$_POST['Website'];

		// save in database moderation or not + Comment parsing
		$comment = trim($_POST['Comment']);
		$comment = ucfirst($comment);
		foreach($comments_message_allowed_tags as $tag)
		{
			// found opended tag
			if(stripos($comment, $tag) !== false)
			{
				$tag_closed = str_replace('[', '[/', $tag);
				if(stripos($comment, $tag_closed) === false)
					$comment .= $tag_closed;

				$real_tag = str_ireplace(array('[', ']'), array('<', '>'), $tag);
				$comment = str_ireplace($tag, $real_tag, $comment);

				$real_tag = str_ireplace(array('[', ']'), array('<', '>'), $tag_closed);
				$comment = str_ireplace($tag_closed, $real_tag, $comment);
			}
		}

		$comment = trim($comment);
		$comment = nl2br($comment);

		// make clikable
		$comment = $plugin->clickable($comment, true);

		$IP = $plugin->getIP();
		$IP_long = ip2long($IP);

        $current_pageID = $plugin->vars['ID'];
        $current_url = '';

        // dynamic page
        if($plugin->vars['Sitemap'] == 'NO')
        {
            $current_pageID = 0;
            $current_url = $_SERVER['SCRIPT_URL'];
        }



		$CID = $plugin->dbInsert('NutsPageComment', array(
															'Date' => 'NOW()',
															'NutsUserID' => @$_SESSION['NutsUserID'],
															'NutsPageID' => $current_pageID,
															'Url' => $current_url,
															'Name' => $_POST['Name'],
															'Email' => $_POST['Email'],
															'Website' => $_POST['Website'],
															'Message' => $comment,
															'Suscribe' => 'YES',
															'IP' => $IP_long,
															'Visible' => 'NO'), array(), true);
		// send email to administrators
		$plugin->mailCharset('UTF8');
		$plugin->mailFrom($comments_email_notify_from);
		$plugin->mailSubject($comments_email_subject.' '.$p_lng['MailSubject']." `{$plugin->vars['H1']}` (#{$plugin->vars['ID']})");

		include(WEBSITE_PATH.'/plugins/_email/config.inc.php');


		$tos = explode(',', $comments_admin_email);
		foreach($tos as $to)
		{
			$to = trim(strtolower($to));

			$message = $p_lng['AdminMailMessage'];
			$message = str_replace('{Name}', $_POST['Name'], $message);
			$message = str_replace('{Email}', $_POST['Email'], $message);
			$message = str_replace('{URI}', $_SERVER['HTTP_REFERER'], $message);
			$message = str_replace('{H1}', $plugin->vars['H1'], $message);
			$message = str_replace('{IP}', long2ip($IP_long), $message);
			$message = str_replace('{Website}', $_POST['Website'], $message);

			// gravatar
			$email = $_POST['Email'];
			$default = (empty($comments_avatar_default_image_url)) ? WEBSITE_URL.'/plugins/_comments/www/anonymous.gif': $comments_avatar_default_image_url;
			$size = $comments_avatar_size;
			$grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?d=".urlencode($default)."&s=".$size;
			$message = str_replace('{Gravatar}', $grav_url, $message);

			$uri = WEBSITE_URL.'/plugins/_comments/www/exec.php?action=';

			// show
			$action= base64_encode("do=show&email_admin=$to&show&ID=$CID&lang={$plugin->vars['Language']}&NutsPageID={$current_pageID}&Email={$_POST['Email']}");
			$message = str_replace('{UriShow}', $uri.strrev($action), $message);

			// delete
			$action= base64_encode("do=delete&email_admin=$to&delete&ID=$CID&lang={$plugin->vars['Language']}&NutsPageID={$current_pageID}&Email={$_POST['Email']}");
			$message = str_replace('{UriDelete}', $uri.strrev($action), $message);

			$message = trim($message);
			// $message = nl2br($message);
			$message = str_replace('{Comment}', $comment, $message);

			// send message
			$plugin->mailTo($to);
			$message = str_replace('[BODY]', $message, $HTML_TEMPLATE);
			$plugin->mailBody($message, 'HTML');
			$plugin->mailSend();
		}
	}

	$plugin->setNutsContent();
}





?>