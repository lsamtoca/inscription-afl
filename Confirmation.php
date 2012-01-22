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
    
    $req = $bdd->prepare('SELECT nom, prenom FROM Inscrit WHERE ID_inscrit = ?');
	$req->execute(array($_GET['ID']));
    $donnees = $req->fetch();
    
    if(isset($donnees['prenom'])) // Hack pour dire qu'on a trouvé
    {
   		$req->closeCursor();

    	$sql = 'UPDATE Inscrit SET `conf`=?, `date confirmation`=? WHERE ID_inscrit =?';
		$req = $bdd->prepare($sql);
		$req->execute(array(1,date('Y-m-d G:i:s'),$_GET['ID']));
		
	    echo "Bonjour ".$donnees['prenom'].' '.$donnees['nom'].",<br />";
		echo 'votre inscription est maintenant confirmée !!!';

    }
    else
    {
    	echo 'ERREUR : nous n\'avons pas trouvé votre pré-inscription dans la base de données :-(' ;
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