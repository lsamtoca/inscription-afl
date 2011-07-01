<?php
	session_start();
	if(!isset($_SESSION["ID_regate"]))
	{
		header("Location: LoginClub.php");
	}

   require "partage.php";
   xhtml_pre("Géstion de la régate");
?>

<div >
       <h1>Gestion de la régate</h1>
       <p><a href="deconnexion.php">Deconnexion</a></p>

</div>
<?php

try
{


	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
	
	$req = $bdd->prepare('SELECT ID_regate, description FROM Regate WHERE ID_regate=?');
	$req->execute(array($_SESSION["ID_regate"]));
    $donnees = $req->fetch();
    
    $DESC_REGATE= $donnees['description'];
    $URL=format_url_regate($_SESSION["ID_regate"]);
    $req->closeCursor();
	
	if(isset($_POST['description']))
    {
	    $DESC_REGATE=$_POST['description'];
	    echo 'Nouvelle description  :';
	    echo $_POST['description'];
	    $sql = 'UPDATE Regate SET description=? WHERE ID_regate =?';
		$req = $bdd->prepare($sql);

    	$req->execute(array($_POST['description'],$_SESSION["ID_regate"]));

		$req->closeCursor();


    }

}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>
<div >
<h2>URL d'inscription</h2>
<a href="<?php echo $URL; ?>"><?php echo $URL; ?></a>
</div>
<div >
<h2>Description de la régate</h2>
<form action="" method="post">
<textarea id="description" name="description" cols="50" rows="10"><?php echo $DESC_REGATE; ?></textarea>

<!--Why do we need the following lines ?-->
<!--<input type="hidden" name="login" id="login" value=<?php echo '"'.$_POST['login'].'"' ?> >
<input type="hidden" name="pass" id="pass" value=<?php echo '"'.$_POST['pass'].'"' ?> >-->
<input type="submit" id="Modifier" value="Modifier" />
    </form>

</div>
<div >
<h2>Liste des inscrits</h2>
<?php
try
{
	$req = $bdd->prepare('SELECT * FROM Inscrit WHERE (ID_regate =?)');
	$req->execute(array($_SESSION["ID_regate"]));
	echo '<table>';
	        echo
        	'<tr><th scope="col">'.'Prenom'.
        	'</th><th scope="col">'.'Nom'.
        	'</th><th scope="col">'.'Sexe'.
        	'</th><th scope="col">'.'Numéros de voile'.
        	'</th><th scope="col">'.'Série'.
        	'</th><th scope="col">'.'Licence'.
        	'</th><th scope="col">'.'Numéros de licence'.
        	'</th><th scope="col">'.'Adherant AFL'.
        	'</th><th scope="col">'.'Confirmation'.
        	'</th><th scope="col">'.'Couriel'.'</tr>';
    while ($donnees = $req->fetch())
    {
        if($donnees['sexe']==1){
        	$sexe='Homme';
        }
        else{
        	$sexe='Femme';
        }
        if($donnees['adherant']==1){
        	$adherant='oui';
        }
        else{
        	$adherant='non';
        }
        if($donnees['conf']==1){
        	$conf='oui';
        }
        else{
        	$conf='non';
        }
        if($donnees['serie']==1){
        	$serie='Laser Standard';
        }
        elseif($donnees['serie']==2){
        	$serie='Laser Radial';
        }
        else{
        	$serie='Laser 4.7';
        }
        if($donnees['statut']==1){
        	$statut='Licencié FFV';
        }
        elseif($donnees['statut']==2){
        	$statut='Pas encore licencié';
        }
        else{
        	$statut='Coureur etrangé';
        }
        echo
        	'<tr>'.
        	'<td scope="col">'.$donnees['prenom'] .'</td>'.
        	'<td scope="col">'.$donnees['nom'].'</td>'.
        	'<td scope="col">'.$sexe.'</td>'.
        	'<td scope="col">'.$donnees['prefix_voile'].' '.$donnees['num_voile'].'</td>'.
        	'<td scope="col">'.$serie.'</td>'.
        	'<td scope="col">'.$statut.'</td>'.
        	'<td scope="col">'.$donnees['num_lic'].'</td>'.
        	'<td scope="col">'.$adherant.'</td>'.
        	'<td scope="col">'.$conf.'</td>'.
        	'<td scope="col">'.$donnees['mail'].'</td>'.
        	'<td scope="col">'.'<a href="Confirmation.php?ID='.$donnees['ID_inscrit'].'">Confirmer</a>'.'</td>'.
        	'<td scope="col">'.'<a href="Annulation.php?ID='.$donnees['ID_inscrit'].'">Annuler</a>'.'</td>'.
        	'</tr>';
    }
    echo '</table>';
   	$req->closeCursor();

}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>

<br>
Télécharger la <a href="Liste_inscrits_xls.php">liste des inscrits au format xls</a>.
</div>

<?php
xhtml_post();
?>