<?php

if(isset($plugin))$plugin->formSetLang('en');
$comments_date_format = "m/d/Y H:i";


// translation
$p_lng['CommentsFor'] = "Comments for";
$p_lng['Cite'] = "Cite";
$p_lng['CiteComment'] = "Cite this comment";
$p_lng['Reply'] = "Reply";
$p_lng['ReplyComment'] = "Reply to this comment";
$p_lng['AddNewPostFor'] = "Add new post for";
$p_lng['Name'] = "Name";
$p_lng['Email'] = "Email";
$p_lng['NotPublished'] = "not published";
$p_lng['Website'] = "Website";
$p_lng['SubmitComment'] = "Submit comment";
$p_lng['FormValid'] = "Your post was successfully posted, it will be visible soon.";
$p_lng['Comment'] = "Comment";
$p_lng['AllowedTags'] = "Allowed xhtml tags :";
$p_lng['SecurityCode'] = "Security code";


// mail message
$p_lng['MailSubject'] = 'new comment for';
$p_lng['AdminMailMessage'] = <<<EOF
<table border="0" cellpadding="5" cellspacing="0">
<tr>
	<td style="width:10px; white-space:nowrap">
		<img src="{Gravatar}" align="middle" />
	</td>
	<td valign="top">
		<b>{Name} ({Email}) writes about `<a href="{URI}">{H1}</a>`: </b><br>
		<b>Website:</b> <a href="{Website}" title="Visit website">{Website}</a><br>
		<b>IP:</b> <a href="http://www.geoiptool.com/en/?IP={IP}" title="Check Ip">{IP}</a>
	</td>
</tr>
<tr>
	<td colspan="2" valign="top">
		<br>
		<br>
		{Comment}
	</td>
</tr>
</table>
<br>
<br>
<hr /><b>Options:</b> (Current comment is not visible)
<a href="{UriShow}">Accept</a> | <a href="{UriDelete}">Delete</a>
<br />
EOF;


// mail message for suscribers
$p_lng['SuscriberMailMessage'] = <<<EOF
<table border="0" cellpadding="5" cellspacing="0">
<tr>
	<td style="width:10px; white-space:nowrap">
		<img src="{Gravatar}" align="middle" />
	</td>
	<td valign="top">
		<b>{Name} ({Email}) writes about `<a href="{URI}">{H1}</a>`: </b>
	</td>
</tr>
<tr>
	<td colspan="2" valign="top">
		<br>
		<br>
		{Comment}
	</td>
</tr>
</table>
<br>
<br>
<hr /><b>Options:</b>
<a href="{URI}#comment{CommentID}">View</a> | <a href="{UriUnsuscribe}">Unsuscribe</a>
<br />
EOF;






?>