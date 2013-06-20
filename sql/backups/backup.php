<?php

error_reporting(E_ALL); // Activer le rapport d'erreurs PHP

$db_charset = "utf8"; /* mettre utf8 ou latin1 */

$db_server = $_POST['serveur']; // Nom du serveur MySQL.  ex. mysql5-26.perso
$db_name = $_POST['bd']; // Nom de la base de données.  ex. mabase
$db_username = $_POST['login']; // Nom de la base de données.  ex. mabase
$db_password = $_POST['password']; // Mot d epasse de la base de données.

$cmd_mysql = "mysqldump";

//   $date = date('_d-m-y_H\hi');
// Store the files with english convention, 
// so that they appear sorted in listings
$date = date('_Y-m-d_H\hi');
$archive_GZIP = $db_name . $date . '.gz';

echo " Sauvegarde de la base <font color=red><b>$db_name</b></font> par <b>mysqldump</b> dans le fichier <b>$archive_GZIP</b> <br> \n";
$commande = $cmd_mysql . " --host=$db_server --user=$db_username --password=$db_password -C -Q -e --default-character-set=$db_charset --single-transaction  $db_name | gzip -c > $archive_GZIP ";
$CR_exec = system($commande);
?> 