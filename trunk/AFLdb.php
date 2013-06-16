<?php

$host="nom du serveur";
$user="nom utilisateur db";
$pwd="mot de passe utilisateur";
$db="nom de la BD qu'on souhaite utiliser";
$pdo_path="mysql:host=$host;dbname=$db";

function connect(){
	global $host;
	global $pwd;
	global $user;
	
	$con = mysql_connect("$host","$user","$pwd")
	or 
		die("Erreur connexion : ". mysql_error());
	
	mysql_select_db($db, $con);

	return  $con;
}
?>
