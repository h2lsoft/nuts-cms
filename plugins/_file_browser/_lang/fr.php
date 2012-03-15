<?php

// French Language Module for v2.3 (translated by Olivier Pariseau & the QuiX project)

$GLOBALS["charset"] = "utf-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "d/m/Y H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "ERREUR(S)",
	"back"			=> "Page precedente",

	// root
	"home"			=> "Le repertoire home n'existe pas, verifiez vos preferences.",
	"abovehome"		=> "Le repertoire courant n'a pas l'air d'etre au-dessus du repertoire home.",
	"targetabovehome"	=> "Le repertoire cible n'a pas l'air d'etre au-dessus du repertoire home.",

	// exist
	"direxist"		=> "Ce repertoire n'existe pas.",
	//"filedoesexist"	=> "Ce fichier existe deja.",
	"fileexist"		=> "Ce fichier n'existe pas.",
	"itemdoesexist"		=> "Cet item existe deja.",
	"itemexist"		=> "Cet item n'existe pas.",
	"targetexist"		=> "Le repertoire cible n'existe pas.",
	"targetdoesexist"	=> "L'item cible existe deja.",

	// open
	"opendir"		=> "Impossible d'ouvrir le repertoire.",
	"readdir"		=> "Impossible de lire le repertoire.",

	// access
	"accessdir"		=> "Vous n'etes pas autorise a acceder a ce repertoire.",
	"accessfile"		=> "Vous n'etes pas autorise a acceder a ce fichier.",
	"accessitem"		=> "Vous n'etes pas autorise a acceder a cet item.",
	"accessfunc"		=> "Vous ne pouvez pas utiliser cette fonction.",
	"accesstarget"		=> "Vous n'etes pas autorise a acceder au repertoire cible.",

	// actions
	"permread"		=> "Lecture des permissions echouee.",
	"permchange"		=> "Changement des permissions echoue.",
	"openfile"		=> "Ouverture du fichier echouee.",
	"savefile"		=> "Sauvegarde du fichier echouee.",
	"createfile"		=> "Creation du fichier echouee.",
	"createdir"		=> "Creation du repertoire echouee.",
	"uploadfile"		=> "Envoie du fichier echoue.",
	"copyitem"		=> "La copie a echouee.",
	"moveitem"		=> "Le deplacement a echoue.",
	"delitem"		=> "La supression a echouee.",
	"chpass"		=> "Le changement de mot de passe a echoue.",
	"deluser"		=> "La supression de l'usager a echouee.",
	"adduser"		=> "L'ajout de l'usager a echouee.",
	"saveuser"		=> "La sauvegarde de l'usager a echouee.",
	"searchnothing"		=> "Vous devez entrez quelquechose e chercher.",

	// misc
	"miscnofunc"		=> "Fonctionalite non disponible.",
	"miscfilesize"		=> "La taille du fichier excede la taille maximale autorisee.",
	"miscfilepart"		=> "L'envoi du fichier n'a pas ete complete.",
	"miscnoname"		=> "Vous devez entrer un nom.",
	"miscselitems"		=> "Vous n'avez selectionne aucuns item(s).",
	"miscdelitems"		=> "Etes-vous certain de vouloir supprimer ces \"+num+\" item(s)?",
	"miscdeluser"		=> "Etes-vous certain de vouloir supprimer l'usager '\"+user+\"'?",
	"miscnopassdiff"	=> "Le nouveau mot de passe est indentique au precedent.",
	"miscnopassmatch"	=> "Les mots de passe different.",
	"miscfieldmissed"	=> "Un champs requis n'a pas ete rempli.",
	"miscnouserpass"	=> "Nom d'usager ou mot de passe invalide.",
	"miscselfremove"	=> "Vous ne pouvez pas supprimer votre compte.",
	"miscuserexist"		=> "Ce nom d'usager existe deje.",
	"miscnofinduser"	=> "Usager non trouve.",
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "CHANGER LES PERMISSIONS",
	"editlink"		=> "EDITER",
	"downlink"		=> "TELECHARGER",
	"uplink"		=> "PARENT",
	"homelink"		=> "HOME",
	"reloadlink"		=> "RAFRAICHIR",
	"copylink"		=> "COPIER",
	"movelink"		=> "DEPLACER",
	"dellink"		=> "SUPPRIMER",
	"comprlink"		=> "ARCHIVER",
	"adminlink"		=> "ADMINISTRATION",
	"logoutlink"		=> "DECONNECTER",
	"uploadlink"		=> "ENVOYER",
	"searchlink"		=> "RECHERCHER",

	// list
	"nameheader"		=> "Nom",
	"sizeheader"		=> "Taille",
	"typeheader"		=> "Type",
	"modifheader"		=> "Modifie",
	"permheader"		=> "Perm.",
	"actionheader"		=> "Actions",
	"pathheader"		=> "Chemin",

	// buttons
	"btncancel"		=> "Annuler",
	"btnsave"		=> "Sauver",
	"btnchange"		=> "Changer",
	"btnreset"		=> "Reinitialiser",
	"btnclose"		=> "Fermer",
	"btncreate"		=> "Creer",
	"btnsearch"		=> "Chercher",
	"btnupload"		=> "Envoyer",
	"btncopy"		=> "Copier",
	"btnmove"		=> "Deplacer",
	"btnlogin"		=> "Connecter",
	"btnlogout"		=> "Deconnecter",
	"btnadd"		=> "Ajouter",
	"btnedit"		=> "Editer",
	"btnremove"		=> "Supprimer",

	// actions
	"actdir"		=> "Repertoire",
	"actperms"		=> "Changer les permissions",
	"actedit"		=> "Editer le fichier",
	"actsearchresults"	=> "Resultats de la recherche",
	"actcopyitems"		=> "Copier le(s) item(s)",
	"actcopyfrom"		=> "Copier de /%s e /%s ",
	"actmoveitems"		=> "Deplacer le(s) item(s)",
	"actmovefrom"		=> "Deplacer de /%s e /%s ",
	"actlogin"		=> "Connecter",
	"actloginheader"	=> "Connecter pour utiliser QuiXplorer",
	"actadmin"		=> "Administration",
	"actchpwd"		=> "Changer le mot de passe",
	"actusers"		=> "Usagers",
	"actarchive"		=> "Archiver le(s) item(s)",
	"actupload"		=> "Envoyer le(s) fichier(s)",

	// misc
	"miscitems"		=> "Item(s)",
	"miscfree"		=> "Disponible",
	"miscusername"		=> "Usager",
	"miscpassword"		=> "Mot de passe",
	"miscoldpass"		=> "Ancien mot de passe",
	"miscnewpass"		=> "Nouveau mot de passe",
	"miscconfpass"		=> "Confirmer le mot de passe",
	"miscconfnewpass"	=> "Confirmer le nouveau mot de passe",
	"miscchpass"		=> "Changer le mot de passe",
	"mischomedir"		=> "Repertoire home",
	"mischomeurl"		=> "URL home",
	"miscshowhidden"	=> "Voir les items caches",
	"mischidepattern"	=> "Cacher pattern",
	"miscperms"		=> "Permissions",
	"miscuseritems"		=> "(nom, repertoire home, Voir les items caches, permissions, actif)",
	"miscadduser"		=> "ajouter un usager",
	"miscedituser"		=> "editer l'usager '%s'",
	"miscactive"		=> "Actif",
	"misclang"		=> "Langage",
	"miscnoresult"		=> "Aucun resultats.",
	"miscsubdirs"		=> "Rechercher dans les sous-repertoires",
	"miscpermnames"		=> array("Lecture seulement","Modifier","Changement le mot de passe","Modifier & Changer le mot de passe",
					"Administrateur"),
	"miscyesno"		=> array("Oui","Non","O","N"),
	"miscchmod"		=> array("Proprietaire", "Groupe", "Publique"),
);
?>