<?php
  require "partage.php";
  xhtml_pre("Confirmation");
?>


<div ><a>

<?php

try
{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options)
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    
    $req = $bdd->prepare('SELECT nom , prenom FROM Inscrit WHERE ID_inscrit = ?');
	$req->execute(array($_GET['ID']));
    $donnees = $req->fetch();
    
    if(isset($donnees['prenom']))
    {
	    echo $donnees['prenom'].' '.$donnees['nom'];
    	$sql = 'UPDATE Inscrit SET conf="1" WHERE ID_inscrit =?';
   		$req->closeCursor();
		$req = $bdd->prepare($sql);
		$req->execute(array($_GET['ID']));
		echo ' Votre inscription est confirmée';

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

</a></div>


<?php
xhtml_post();
?>