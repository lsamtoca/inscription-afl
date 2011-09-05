<?php

  session_start();
  if(!isset($_SESSION["ID_regate"]))
  {
		header("Location: LoginClub.php");
  }

  require "partage.php";
  xhtml_pre("Annullation d'une inscription");
?>


<div >

<?php
try
{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    
    $req = $bdd->prepare('SELECT nom , prenom FROM Inscrit WHERE ID_inscrit = ?');
	$req->execute(array($_GET['ID']));
    $donnees = $req->fetch();
    
    if(isset($donnees['prenom']))
    {

    	$sql = 'DELETE FROM Inscrit WHERE ID_inscrit =?';
   		$req->closeCursor();
		$req = $bdd->prepare($sql);
		$req->execute(array($_GET['ID']));
	    
	    echo $donnees['prenom'].' '.$donnees['nom'];
		echo ' : inscription annulé';

    }
    else
    {
    	echo 'ERREUR';
    }
    

	
	$req->closeCursor();


}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>

</div>

<p></p>
<div>
Retour à la page de <a href="Regate.php">gestion de la régate</a>.
</div>


<?php
xhtml_post();
?>