<?php

// Error fatal [:FILE:], [:BLOC:], [:ITEM:] are replaced
$_err['0'] 	= "Fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font> non trouvé";
$_err['1'] 	= "Item <font color=\"#0000FF\">{[:ITEM:]}</font> non trouvé dans le fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['1.1'] 	= "Item <font color=\"#0000FF\">{[:ITEM:]}</font> non trouvé dans le bloc <font color=\"#008000\">&lt;bloc::[:BLOC:]&gt;</font> du fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['2'] 	= "Bloc <font color=\"#008000\">&lt;bloc::[:BLOC:]&gt;</font> non trouvé dans le fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['2.1'] 	= "Bloc <font color=\"#008000\">&lt;bloc::[:BLOC:]&gt;</font>est en double dans le fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['3'] 	= "Bloc <font color=\"#008000\">&lt;bloc::[:BLOC:]&gt;</font> non trouvé dans le bloc <font color=\"#0000FF\">{[:ITEM:]}</font> du fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['4'] 	= "Chemin du bloc <font color=\"#0000FF\">[:BLOC:]</font> dans le fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['4.1'] 	= "Chemin du bloc <font color=\"#0000FF\">[:BLOC:]</font> dans la fonction Loop du fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['4.2'] 	= "Chemin du bloc <font color=\"#0000FF\">[:BLOC:]</font> est déjà défini comme Loop dans le fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['5'] 	= 'Ne peux définir un bloc vide';
$_err['6'] 	= 'Ne peux définir un item vide';
$_err['7'] 	= 'Ne peux créer le répertoire de cache '.TPLN_CACHE_DIR;
$_err['7.1'] 	= 'Ne peux créer le répertoire de cache [:FILE:]';
$_err['7.2'] 	= 'Ne peux créer le répertoire de templates par défaut '.TPLN_DEFAULT_PATH;
$_err['8'] 	= "Ne peux trouver la fin du bloc  <font color=\"#008000\">[:BLOC:]</font> dans le fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";
$_err['9'] 	= "Template [:FILE:] n'est pas un intégral";
$_err['10'] 	= "Template [:FILE:] n'existe pas";
$_err['11'] 	= "Template [:FILE:] n'est pas défini";
$_err['12'] 	= 'La fonction DefineTemplate() doit avoir un array en paramètre';
$_err['13'] 	= "Votre chemin de bloc doit avoir un bloc parent  <font color=\"#008000\">[:BLOC:]</font> dans le fichier <font color=\"#FF0000\"><b>[:FILE:]</b></font>";

// db error [:MSG:] is replaced
$_err['DB']['0'] = "Problème de connexion à la base ([:MSG:])";
$_err['DB']['0.1'] = 'Pas de connection trouvé';
$_err['DB']['1'] = "Problème de fermeture à la base ([:MSG:])";
$_err['DB']['2'] = "Problème de requête ([:MSG:])";
$_err['DB']['2.1'] = "Index du changement de connection ([:MSG:])";
$_err['DB']['2.11'] = "Index du changement de requête ([:MSG:])";
$_err['DB']['2.2'] = 'Pas de requête trouvée';
$_err['DB']['2.3'] = 'Colonne de résultats non valide';
$_err['DB']['3'] = 'SELECT non trouvé dans votre requête';
$_err['DB']['4'] = 'FROM non trouvé dans votre requête';
$_err['DB']['5'] = 'Showrecords() doit avoir un entier en second paramètre';
$_err['DB']['5.1'] = 'Showrecords() doit avoir un entier supérieur à zéro en second paramètre';



?>