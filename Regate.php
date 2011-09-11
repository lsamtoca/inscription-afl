<?php
	session_start();
	if(!isset($_SESSION["ID_regate"]))
	{
		header("Location: LoginClub.php");
	}

   require "partage.php";
   xhtml_pre("Géstion de votre régate");
?>

<script type="text/javascript" src="classes/calendarDateInput.js">

/***********************************************
* Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/

</script>

<div >
       <p><a href="deconnexion.php">Deconnexion</a></p>
</div>

<?php

try{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
		
	$fields=array('titre','description','cv_organisateur','lieu','date_debut','date_fin','droits');
	foreach($fields as $field)
	{
	   if(isset($_POST[$field]))
       {
//	       echo "N. $field : " . $_POST[$field];
	    
	       $sql = "UPDATE Regate SET `$field`=? WHERE ID_regate =?";
		   $req = $bdd->prepare($sql);
    	   $req->execute(array($_POST[$field],$_SESSION["ID_regate"]));
    	   $req->closeCursor();
       }
	}
	
}
catch(Exception $e){
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}


try{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
	
	$req = $bdd->prepare('SELECT `ID_regate`, `titre`,`description`, `cv_organisateur`,`lieu`,`date_debut`,`date_fin`,`droits` FROM Regate WHERE ID_regate=?');
	$req->execute(array($_SESSION["ID_regate"]));
    $donnees = $req->fetch();
    $req->closeCursor();	
    
    $TITRE_REGATE= $donnees['titre'];
    $DESC_REGATE= $donnees['description'];
    $CV_ORGANISATEUR=$donnees['cv_organisateur'];
    $LIEU=$donnees['lieu'];
    $DATE_DEBUT_REGATE=$donnees['date_debut'];
    $DATE_FIN_REGATE=$donnees['date_fin'];
    $DROITS=$donnees['droits'];
    
    $URL=format_url_regate($_SESSION["ID_regate"]);
}
catch(Exception $e){
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>

<div >

<h2>Renseignements sur la régate</h2>

<form action="" method="post">
<fieldset>
<label>
Titre :</label>
<textarea id="titre" name="titre" cols="50" rows="1"><?php echo $TITRE_REGATE; ?></textarea>
<br />
<label>
Description :
<textarea id="description" name="description" cols="50" rows="10"><?php echo $DESC_REGATE; ?></textarea>
</label>

<hr />

<label>
Lieu  :
</label>
<textarea id="lieu" name="lieu" cols="50" rows="2"><?php echo $LIEU; ?></textarea>

<label>
Cercle organisateur :
</label>
<textarea id="cv_organisateur" name="cv_organisateur" cols="50" rows="1"><?php echo $CV_ORGANISATEUR; ?></textarea>

<hr />

<label>
Date debut :
</label>

<!--<textarea id="date_debut" name="date_debut" cols="50" rows="1"><?php echo $DATE_DEBUT_REGATE; ?></textarea>-->
<!--<input type="date" name="date_debut" value=<?php echo "$DATE_DEBUT_REGATE";?> />-->


<?php 
$date='2011-09-10';
if(isset($_POST['date_debut'])) 
  $date=$_POST['date_debut'];
echo "<script>DateInput('date_debut', true, 'YYYY-MM-DD','$date')</script>";
?>

    
<label>
Date fin :
</label>
<!--<textarea id="date_fin" name="date_fin" cols="50" rows="1"><?php echo $DATE_FIN_REGATE; ?></textarea>-->
<!--<input type="date" name="date_fin" value=<?php echo "$DATE_FIN_REGATE";?> />-->
<?php 
$date='2011-09-10';
if(isset($_POST['date_fin'])) 
  $date=$_POST['date_fin'];
echo "<script>DateInput('date_debut', true, 'YYYY-MM-DD','$date')</script>";
?>


<hr />

<label>
Droits d'inscription :
</label>
<input type="number" min="0" max="100" name="droits" value=<?php echo "$DROITS";?> /> &#8364;


<br>

<input type="submit" id="Modifier" value="Modifier" />

</fieldset>
</form>


</div>


<div >
<h2>URL d'inscription</h2>
<a href="<?php echo $URL; ?>"><?php echo $URL; ?></a>
</div>

<!-- Inscrits -->
<div >
<h2>Liste des inscrits</h2>
<?php
try
{
	$req = $bdd->prepare('SELECT * FROM Inscrit WHERE (ID_regate =?)');
	$req->execute(array($_SESSION["ID_regate"]));
	echo '<table class="mytable">';
	        echo
        	'<tr class="mytable"><th scope="col">'.'Prenom'.
        	'</th><th  class="mytable" scope="col">'.'Nom'.
        	'</th><th  class="mytable" scope="col">'.'Sexe'.
        	'</th><th  class="mytable" scope="col">'.'Numéros de voile'.
        	'</th><th  class="mytable" scope="col">'.'Série'.
        	'</th><th  class="mytable" scope="col">'.'Licence'.
        	'</th><th  class="mytable" scope="col">'.'Numéros de licence'.
        	'</th><th  class="mytable" scope="col">'.'Adherant AFL'.
        	'</th><th  class="mytable" scope="col">'.'Confirmation'.
        	'</th><th  class="mytable" scope="col">'.'Courriel'.'</tr>';
    while ($donnees = $req->fetch())
    {
        if($donnees['sexe']=='M'){
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
        if($donnees['serie']=='LAS'){
        	$serie='Laser Standard';
        }
        elseif($donnees['serie']=='LAR'){
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
        	'<td class="mytable" scope="col">'.$donnees['prenom'] .'</td>'.
        	'<td class="mytable" scope="col">'.$donnees['nom'].'</td>'.
        	'<td class="mytable" scope="col">'.$sexe.'</td>'.
        	'<td class="mytable" scope="col">'.$donnees['prefix_voile'].' '.$donnees['num_voile'].'</td>'.
        	'<td class="mytable" scope="col">'.$serie.'</td>'.
        	'<td class="mytable" scope="col">'.$statut.'</td>'.
        	'<td class="mytable" scope="col">'.$donnees['num_lic'].'</td>'.
        	'<td class="mytable" scope="col">'.$adherant.'</td>'.
        	'<td class="mytable" scope="col">'.$conf.'</td>'.
        	'<td class="mytable" scope="col">'.$donnees['mail'].'</td>'.
        	'<td class="mytable" scope="col">'.'<a href="Confirmation.php?ID='.$donnees['ID_inscrit'].'">Confirmer</a>'.'</td>'.
        	'<td class="mytable" scope="col">'.'<a href="Annulation.php?ID='.$donnees['ID_inscrit'].'">Annuler</a>'.'</td>'.
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
</div>

<!--Exportation des données-->
<div>
<h2>Exportation des données</h2>

<ul>
<li>
Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_xls.php">format xls</a>  (pour Excel).
</li>
<li>
Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_xls.php?confirme=1">format xls</a> (pour Excel).
</li>
</ul>

<h3>Intégration avec le logiciel FREG</h3>
<ul>
<li>
Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_dbf.php">format dbf</a> (pour FREG, XBase, Excel,...).
</li>
<li>
Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_dbf.php?confirme=1">format dbf</a> (pour FREG, XBase, Excel,...).
</li>
</ul>
Pour importer le fichier ins_dbf.dbf vers votre régate dans FREG, vous devez disposer du module FF_PRE_INS.EXE, à demander par courriel au support de FREG.
</div>

<?php
xhtml_post();
?>