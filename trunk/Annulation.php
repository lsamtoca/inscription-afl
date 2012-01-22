<?php

  session_start();
  if(!isset($_SESSION["ID_regate"]))
  {
		header("Location: LoginClub.php");
  }
  require "partage.php";

  try
  {
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    
    $req = $bdd->prepare('SELECT nom, prenom, ID_regate FROM Inscrit WHERE ID_inscrit = ?');
	$req->execute(array($_GET['ID']));
    $donnees = $req->fetch();
    
    if(
      isset($donnees['prenom']) // si nous avons trouvé le coureur
      and
      $donnees['ID_regate'] == $_SESSION['ID_regate']   // nous en avons les droits
    ) 
    {

    	$sql = 'DELETE FROM Inscrit WHERE ID_inscrit =?';
   		$req->closeCursor();
		$req = $bdd->prepare($sql);
		$req->execute(array($_GET['ID']));
    }
    else
    {
        xhtml_pre("Annullation d'une inscription");
    	echo '<div>ERREUR : coureur pas trouvé dans cette régate</div>';
    
        echo '
        <p></p> 
        <div>
        Retour à la page de <a href="Regate.php">gestion de la régate</a>.
        </div>';
        xhtml_post();
    }
    

	
	$req->closeCursor();


}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

header("Location: Regate.php?item=inscrits");


?>