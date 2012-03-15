<?php

if(isset($plugin))$plugin->formSetLang('fr');
$comments_date_format = "d/m/Y H:i";


// translation
$p_lng['CommentsFor'] = "Commentaires pour";
$p_lng['Cite'] = "Citer";
$p_lng['CiteComment'] = "Citer ce commentaire";
$p_lng['Reply'] = "Répondre";
$p_lng['ReplyComment'] = "Répondre à ce commentaire";
$p_lng['AddNewPostFor'] = "Ajouter un nouveau message pour";
$p_lng['Name'] = "Nom";
$p_lng['Email'] = "Email";
$p_lng['NotPublished'] = "non publié";
$p_lng['Website'] = "Site Web";
$p_lng['SubmitComment'] = "Envoyez votre commentaire";
$p_lng['FormValid'] = "Votre message a été posté avec succès, il sera bientôt visible.";
$p_lng['Comment'] = "Commentaire";
$p_lng['AllowedTags'] = "Balises XHTML autorisées :";
$p_lng['SecurityCode'] = "Code de sécurité";



// mail message
$p_lng['MailSubject'] = 'nouveau commentaire pour';
$p_lng['AdminMailMessage'] = <<<EOF
<table border="0" cellpadding="5" cellspacing="0">
<tr>
	<td style="width:10px; white-space:nowrap">
		<img src="{Gravatar}" align="middle" />
	</td>
	<td valign="top">
		<b>{Name} ({Email}) a écrit à propos de `<a href="{URI}">{H1}</a>`: </b><br>
		<b>Website:</b> <a href="{Website}" title="Visiter le site">{Website}</a><br>
		<b>IP:</b> <a href="http://www.geoiptool.com/en/?IP={IP}" title="Vérifier cette ip">{IP}</a>
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
<hr /><b>Options:</b> (le commentaire actuel n'est pas visible)
<a href="{UriShow}">Accepter</a> | <a href="{UriDelete}">Effacer</a>
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
		<b>{Name} ({Email}) a écrit à propos de `<a href="{URI}">{H1}</a>`: </b>
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
<a href="{URI}#comment{CommentID}">Voir</a> | <a href="{UriUnsuscribe}">Se désinscrire</a>
<br />
EOF;






?>