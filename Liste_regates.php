<?php
  require "partage.php";
  html_pre("Les régates ouvertes à l'inscription");
  
  try
  {
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
	
	
	$sql = 'SELECT * FROM `Regate` WHERE 1';
	$req = $bdd->query($sql);
	echo '<ul>';
	while($row=$req->fetch()){
	 printf("<li><a href=\"%s\">Régate %d</a> </li>",format_url_regate($row['ID_regate']),$row['ID_regate']);
	}
    echo '</ul>';


}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

  
  
  
  
  
  html_post();
?>