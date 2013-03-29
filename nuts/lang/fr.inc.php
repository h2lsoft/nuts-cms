<?php

setlocale(LC_ALL, 'fr_FR');
$nuts->formSetLang('fr');

// login
$nuts_login_lang_msg[0] = "Administration";
$nuts_login_lang_msg[1] = "Login";
$nuts_login_lang_msg[2] = "Mot de passe";
$nuts_login_lang_msg[3] = "Identifiants Login/Mot de passe invalide";
$nuts_login_lang_msg[4] = "Se souvenir de moi";


$nuts_lang_msg[0] = "Bonjour";
$nuts_lang_msg[1] = strftime("%a %m %b %y, %H:%M");
$nuts_lang_msg[2] = "Déconnexion";
$nuts_lang_msg[3] = "Accueil";

// widget::list
$nuts_lang_msg[4] = "Enregistrement de {_First} à {_Last} sur {_Count}";
$nuts_lang_msg[5] = "Page";
$nuts_lang_msg[6] = "Aucun enregistrement trouvé";
$nuts_lang_msg[7] = "Ajouter";
$nuts_lang_msg[8] = "Visualiser cet enregistrement";
$nuts_lang_msg[9] = "Modifier cet enregistrement";
$nuts_lang_msg[10] = "Supprimer cet enregistrement";
$nuts_lang_msg[11] = "Rechercher";
$nuts_lang_msg[12] = "commence par";
$nuts_lang_msg[13] = "ne commence pas par";
$nuts_lang_msg[14] = "contient";
$nuts_lang_msg[15] = "ne contient pas";
$nuts_lang_msg[16] = "Rechercher";
$nuts_lang_msg[17] = "Annuler";
$nuts_lang_msg[18] = "Précédent";
$nuts_lang_msg[19] = "Suivant";
$nuts_lang_msg[20] = "Ajouter un nouvel enregistrement";

// widget::form
$nuts_lang_msg[21] = "Enregistrer";
$nuts_lang_msg[22] = "Annuler";
$nuts_lang_msg[23] = "Chargement en cours...";
$nuts_lang_msg[24] = "Votre enregistrement a bien été ajouté";
$nuts_lang_msg[25] = "Votre enregistrement a bien été modifié";
$nuts_lang_msg[26] = "Fermer";
$nuts_lang_msg[27] = "Modifier cet enregistrement";

// widget::view
$nuts_lang_msg[28] = "Visualisation";

// widget::delete
$nuts_lang_msg[29] = "Suppression";
$nuts_lang_msg[30] = "Oui";
$nuts_lang_msg[31] = "Non";
$nuts_lang_msg[32] = "Vous devez supprimer tous les enregistrements dans `%s` avec %sID = %s";
$nuts_lang_msg[33] = "Voulez-vous supprimer cet enregistrement ?";
$nuts_lang_msg[34] = "Votre enregistrement a bien été supprimé";

$nuts_lang_msg[35] = "Le champ `%s` existe, merci de le modifier";
$nuts_lang_msg[36] = "Vous ne pouvez pas vous supprimer vous-même";

$nuts_lang_msg[37] = "Erreur, merci de recommencer";
$nuts_lang_msg[38] = "Vos données ont été sauvegardées";

$nuts_lang_msg[39] = "Vous ne pouvez pas supprimer le groupe `SuperAdmin`";


// widget::form image
$nuts_lang_msg[40] = "Supprimer";


// page manager
$nuts_lang_msg[41] = "Menu principal";

// all
$nuts_lang_msg[42] = "est une solution open source ";
$nuts_lang_msg[43] = "optimisé pour";

$nuts_lang_msg[44] = "Mot de passe oublié ?";
$nuts_lang_msg[45] = "Votre email";
$nuts_lang_msg[46] = "Votre email n'a pas été trouvée ou bien celle ci est invalide";
$nuts_lang_msg[47] = "Vos informations ont été envoyées par email";
$nuts_lang_msg[48]['subject'] = "Vos informations";
$nuts_lang_msg[48]['body'] = "
Bonjour {FirstName} {LastName},

Voici vos identifiants de connexion :

Identifiant: {Login}
Mot de passe: {Password}

Pour vous connecter: {WEBSITE_URL}/nuts/login.php";

$nuts_lang_msg[49] = "Enregistrer";
$nuts_lang_msg[50] = "Rafraîchir";
$nuts_lang_msg[51] = "Exporter Excel";
$nuts_lang_msg[52] = "Imprimer";

$nuts_lang_msg[53] = "Liste large/scroll";
$nuts_lang_msg[54] = "L'utilisateur `%s` vient d'éditer cet enregistrement, merci d'attendre 2 minutes avant d'enregistrer";
$nuts_lang_msg[55] = "Votre accès est bloqué (5 échecs de login),<br /> merci de contacter votre administrateur système";
$nuts_lang_msg[56] = "Envoyer";
$nuts_lang_msg[57] = "Enr./page";


$nuts_lang_msg[58] = "Soumettre un bug";
$nuts_lang_msg[59] = "Suggestions";
$nuts_lang_msg[60] = "Site internet officiel";

$nuts_lang_msg[61] = "Erreur: merci de choisir un fichier";
$nuts_lang_msg[62] = "Voulez-vous fermer cette fenêtre ?";

$nuts_lang_msg[63] = "Voulez-vous vous déconnecter ?";
$nuts_lang_msg[64] = "Consulter ma messagerie privée";
$nuts_lang_msg[65] = "vous n'avez pas de message";
$nuts_lang_msg[66] = "Vous avez [MAIL_NB] message";
$nuts_lang_msg[67] = "Utilisateur(s) en ligne";
$nuts_lang_msg[68] = "Aller sur site internet";
$nuts_lang_msg[69] = "Ajouter fichiers ...";
$nuts_lang_msg[70] = "Effacer la liste";
$nuts_lang_msg[71] = "Echec";

$nuts_lang_msg[72] = "Galerie";
$nuts_lang_msg[73] = "Média";


$nuts_lang_msg[74] = "Copier cet enregistrement";
$nuts_lang_msg[75] = "Défaut";

$nuts_lang_msg[76] = "Voulez-vous vous déconnecter ?";
$nuts_lang_msg[77] = "Aucun plugin trouvé";

$nuts_lang_msg[78] = "Formulaires";
$nuts_lang_msg[79] = "Sondages";
$nuts_lang_msg[80] = "Groupe";
$nuts_lang_msg[81] = "Sous-groupe";

$nuts_lang_msg[82] = "Créer une page";
$nuts_lang_msg[83] = "Créer une news";

$nuts_lang_msg[84]['subject'] = "IP blocked";
$nuts_lang_msg[84]['body'] = "
Hi,

IP `{IP}` is blocked by system after 5 times error login

To unblock this IP: {WEBSITE_URL}/nuts/?mod=_control-center&do=exec";


$nuts_lang_msg[85] = "Visualiser";
$nuts_lang_msg[86] = "Prévisualiser";
$nuts_lang_msg[87] = "Parcourir";

$nuts_lang_msg[88] = "Mes recherches";

$nuts_lang_msg[89] = "Modifier";
$nuts_lang_msg[90] = "Mon profil";

$nuts_lang_msg[91] = "<u>Raccourcis clavier :</u><br /><br />
Alt+N : Aller à la page suivante<br />
Alt+B : Aller à la page précédente<br />
Alt+R : Rafraîchir<br />
Alt+A : Ajouter un enregistrement<br />
Alt+S : Ouvrir/Fermer la recherche";

$nuts_lang_msg[92] = "Vous avez été déconnecté car votre adresse IP est différente";
$nuts_lang_msg[93] = "Mes notes";
$nuts_lang_msg[94] = "Modifier mon profil";
$nuts_lang_msg[95] = "Site internet";


$nuts_lang_msg[96] = "dans (séparé par `,`)";
$nuts_lang_msg[97] = "pas dans (séparé par `,`)";


// main menu ***********************************************************************
$mods_group[0]['name'] = 'Administration';
$mods_group[0]['color'] = '#00AAFF';

$mods_group[1]['name'] = 'Website';
$mods_group[1]['color'] = '#FF7633';

$mods_group[2]['name'] = 'Communication';
$mods_group[2]['color'] = '#ffcc00';

$mods_group[3]['name'] = 'Plugins';
$mods_group[3]['color'] = '#00BD0F';

$mods_group[4]['name'] = 'Outils';
$mods_group[4]['color'] = '#5FCE00';


// editor options ***********************************************************************
$nuts_editor_options[0] = 'Simple';
$nuts_editor_options[1] = 'Full';

// lang options ***********************************************************************
$nuts_language_options[0] = array('en', 'Anglais');
$nuts_language_options[1] = array('fr', 'Français');

// timezone options ***********************************************************************
$nuts_timezone_options[] = array('value' => '-12', 'label' => 'GMT - 12 Hours');
$nuts_timezone_options[] = array('value' => '-11', 'label' => 'GMT - 11 Hours');
$nuts_timezone_options[] = array('value' => '-10', 'label' => 'GMT - 10 Hours');
$nuts_timezone_options[] = array('value' => '-9', 'label' => 'GMT - 9 Hours');
$nuts_timezone_options[] = array('value' => '-8', 'label' => 'GMT - 8 Hours');
$nuts_timezone_options[] = array('value' => '-7', 'label' => 'GMT - 7 Hours');
$nuts_timezone_options[] = array('value' => '-6', 'label' => 'GMT - 6 Hours');
$nuts_timezone_options[] = array('value' => '-5', 'label' => 'GMT - 5 Hours');
$nuts_timezone_options[] = array('value' => '-4', 'label' => 'GMT - 4 Hours');
$nuts_timezone_options[] = array('value' => '-3.5', 'label' => 'GMT - 3:30 Hours');
$nuts_timezone_options[] = array('value' => '-3', 'label' => 'GMT - 3 Hours');
$nuts_timezone_options[] = array('value' => '-2', 'label' => 'GMT - 2 Hours');
$nuts_timezone_options[] = array('value' => '-1', 'label' => 'GMT - 1 Hours');
$nuts_timezone_options[] = array('value' => '0', 'label' => 'GMT - 0 Hours');
$nuts_timezone_options[] = array('value' => '+1', 'label' => 'GMT + 1 Hours');
$nuts_timezone_options[] = array('value' => '+2', 'label' => 'GMT + 2 Hours');
$nuts_timezone_options[] = array('value' => '+3', 'label' => 'GMT + 3 Hours');
$nuts_timezone_options[] = array('value' => '+3.5', 'label' => 'GMT + 3:30 Hours');
$nuts_timezone_options[] = array('value' => '+4', 'label' => 'GMT + 4 Hours');
$nuts_timezone_options[] = array('value' => '+4.5', 'label' => 'GMT + 4:30 Hours');
$nuts_timezone_options[] = array('value' => '+5', 'label' => 'GMT + 5 Hours');
$nuts_timezone_options[] = array('value' => '+5.5', 'label' => 'GMT + 5:30 Hours');
$nuts_timezone_options[] = array('value' => '+6', 'label' => 'GMT + 6 Hours');
$nuts_timezone_options[] = array('value' => '+6.5', 'label' => 'GMT + 6:30 Hours');
$nuts_timezone_options[] = array('value' => '+7', 'label' => 'GMT + 7 Hours');
$nuts_timezone_options[] = array('value' => '+8', 'label' => 'GMT + 8 Hours');
$nuts_timezone_options[] = array('value' => '+9', 'label' => 'GMT + 9 Hours');
$nuts_timezone_options[] = array('value' => '+9.5', 'label' => 'GMT + 9:30 Hours');
$nuts_timezone_options[] = array('value' => '+10', 'label' => 'GMT + 10 Hours');
$nuts_timezone_options[] = array('value' => '+11', 'label' => 'GMT + 11 Hours');
$nuts_timezone_options[] = array('value' => '+12', 'label' => 'GMT + 12 Hours');
$nuts_timezone_options[] = array('value' => '+13', 'label' => 'GMT + 13 Hours');

// zone options ***********************************************************************
$nuts_zone_options[] = array('value' => 'MENU', 'label' => 'Menu');
$nuts_zone_options[] = array('value' => 'UNIVERSAL TEXT', 'label' => 'Texte Universel');
$nuts_zone_options[] = array('value' => 'TEXT', 'label' => 'Texte');

// nuts lang ***********************************************************************
$nuts_lang_options[] = array('value' => 'fr', 'label' => 'Français');
$nuts_lang_options[] = array('value' => 'en', 'label' => 'Anglais');
$nuts_lang_options[] = array('value' => 'de', 'label' => 'Allemand');
$nuts_lang_options[] = array('value' => 'it', 'label' => 'Italien');
$nuts_lang_options[] = array('value' => 'es', 'label' => 'Espangnol');
$nuts_lang_options[] = array('value' => 'pt', 'label' => 'Portugais');
$nuts_lang_options[] = array('value' => 'sw', 'label' => 'Suédois');
$nuts_lang_options[] = array('value' => 'da', 'label' => 'Danish');
$nuts_lang_options[] = array('value' => 'fi', 'label' => 'Finlandais');
$nuts_lang_options[] = array('value' => 'jp', 'label' => 'Japonais');
$nuts_lang_options[] = array('value' => 'ru', 'label' => 'Russe');
$nuts_lang_options[] = array('value' => 'gr', 'label' => 'Grèque');
$nuts_lang_options[] = array('value' => 'hu', 'label' => 'Hongrois');
$nuts_lang_options[] = array('value' => 'pl', 'label' => 'Polonais');




?>