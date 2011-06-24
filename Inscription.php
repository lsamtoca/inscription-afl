<?php

require "partage.php";
  
try
{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
	
	$date=$_POST['anne_naissance']."-".$_POST['mois_naissance']."-".$_POST['jour_naissance'];

	$sql = 'INSERT INTO Inscrit (nom, prenom,naissance,num_lic,prefix_voile,num_voile,serie,adherant,sexe,conf,mail,statut,ID_regate)VALUES(:nom,:prenom,:naissance,:num_lic,:prefix_voile,:num_voile,:serie,:adherant,:sexe,:conf,:mail,:statut,:ID_regate)';
	$req = $bdd->prepare($sql);
	$req->execute(array(
		'nom' => $_POST['Nom'],
		'prenom' => $_POST['Prenom'],
		'naissance' => $date,
		'num_lic' => $_POST['lic'],
		'prefix_voile' => $_POST['Cvoile'],
		'num_voile' => $_POST['Nvoile'],
		'serie' => $_POST['serie'],
		'adherant' => $_POST['adherant'],
		'sexe' => $_POST['sexe'],
		'conf' => "0",
		'mail' => $_POST['mail'],
		'statut' => $_POST['statut'],
		'ID_regate' => $_POST['IDR']
	));

	echo $confirmation = $_POST['Prenom'].' '.$_POST['Nom']." vous allez recevoir un couriel sur <br />".$_POST['mail']."<br />Ce message contient un lien qui vous permetra de confirmer votre préinscription.<br />Vous avez 30min pour valider votre preinscription.";


}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>