<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Confirmation</title>
</head>

<body>
<div ><a><?php
try
{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);

    
    $req = $bdd->prepare('SELECT nom , prenom FROM Inscrit WHERE ID_inscrit = ?');
	$req->execute(array($_GET['ID']));
    $donnees = $req->fetch();
    
    if(isset($donnees['prenom']))
    {
	    echo $donnees['prenom'].' '.$donnees['nom'];
    	$sql = 'DELETE FROM Inscrit WHERE ID_inscrit =?';
   		$req->closeCursor();
		$req = $bdd->prepare($sql);
		$req->execute(array($_GET['ID']));
		echo ' Votre inscription est annulé';

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

?></a></div>

</script>
</body>
</html>
