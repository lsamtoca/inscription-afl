?<php
	
session_start();

if(!isset($_SESSION['ID_administrateur'])){
	header('Location: LoginAdmin.php');
	}

require 'partage.php';
	
    
// Destruction de regate
if(isset($_POST['IDR'])){
		try{
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
			
			// We delete a race only if there are no preregistered sailoirs 
			$sql = 'SELECT COUNT(*) as `num` FROM Inscrit WHERE ID_regate= :IDR';
 			$req = $bdd->prepare($sql);
 			$req->execute(array('IDR' => $_POST['IDR']));
 			$row=$req->fetch();
 	        if($row['num'] == 0){
			   $sql = 'DELETE FROM Regate WHERE ID_regate= :IDR';
			   $req = $bdd->prepare($sql);
			   $req->execute(array('IDR' => $_POST['IDR']));
 	        }
 	        
			// We do not want to delete records of a race .... 
			// ... instead move to another table !!!
			// First, delete all coureurs whose ID is $_POST['IDR']
// 			$sql = 'DELETE FROM Inscrit WHERE ID_regate= :IDR';
// 			$req = $bdd->prepare($sql);
// 			$req->execute(array('IDR' => $_POST['IDR']));
			// Second, delete the regata 
/*			$sql = 'DELETE FROM Regate WHERE ID_regate= :IDR';
			$req = $bdd->prepare($sql);
			$req->execute(array('IDR' => $_POST['IDR']));*/
			}
        catch(Exception $e){
				// En cas d'erreur, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
		}
	}

// Création nouvelle régate
if(isset($_POST['org_login']) && $_POST['org_login']!=''
	 && $_POST['org_passe']!='' 
	 && $_POST['org_courriel'] !=''
	 //&& $_POST['jour_destru']!='JJ' && $_POST['mois_destru']!='MM' && $_POST['anne_destru']!='AAAA'
	 && $_POST['date_destru'] !=''
	)
{
		try{
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
//			$date=$_POST['anne_destru']."-".$_POST['mois_destru']."-".$_POST['jour_destru'];
			$date=$_POST['date_destru'];
			$today=date('Y-m-d');
			$sql = 'INSERT INTO Regate (org_login, org_passe,courriel,destruction,ID_administrateur,date_debut,date_fin,date_limite_preinscriptions) VALUES(:org_login,:org_passe,:courriel,:destruction,:ID_administrateur,
			:date_debut,:date_fin,:date_limite_preinscriptions)';
			$req = $bdd->prepare($sql);
			$req->execute(array(
			'org_login' => $_POST['org_login'],
			'org_passe' => $_POST['org_passe'],
			'courriel' => $_POST['org_courriel'],
			'destruction' => $date,
			'ID_administrateur' => $_SESSION["ID_administrateur"],
			'date_debut' => $today,
			'date_fin' => $today,
			'date_limite_preinscriptions' => $today,
			));
		}
		catch(Exception $e){
			// En cas d'erreur, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
			}
		}

// Preparation des données pour l'affichage
try{
// On se connecte à MySQL
    	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
		$req= $bdd->query('SELECT * FROM Regate');
}
catch(Exception $e){
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>
<?php // Affichage
xhtml_pre1('Gestion des Événements (et des Clubs)'); ?>

<script type="text/javascript" src="classes/calendarDateInput.js">
/***********************************************
* Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/
</script>
<?php xhtml_pre2('Gestion des Événements (et des Clubs)'); ?>

<div id='deconnexion'>[<a href='deconnexion.php'>Deconnexion</a>]</div>

<div id='nouvel_evenement'>
 <!--      <h1>Gestion des Événements (et des Clubs)</h1>-->
 
<form action='' method='post'>
			
<fieldset><legend>Nouvel Événement</legend>
				
<table>
<tr>
  <td>
  <label for='org_login'>Login organisateur :</label>
  </td>
  <td>
  <input name='org_login' type='text' id='org_login' tabindex="2"/>
  </td>
</tr>

<tr>
  <td>
  <label for='org_passe'>Mot de passe organisateur :</label>
  </td>
  <td><input name='org_passe' type='text' id='org_passe' tabindex="2"/></td>
</tr>

<tr>
  <td>				
  <label for='org_courriel'>Courriel organisateur :</label>
  </td>
  <td>
  <input name='org_courriel' type='text' id='org_courriel' tabindex="2"/>
  </td>
</tr>

<tr>
  <td>
  <label for='date_destru'>Date de destruction :</label>
  </td>
  <td>
  <?php $in_one_year=date("Y-m-d",31536000 + time()); ?>
  <script type="text/javascript">
    DateInput('date_destru', true,'YYYY-MM-DD','<?php echo $in_one_year ?>')
  </script> 
				
<!--				<input name='jour_destru' type='text' value='JJ' size='2' maxlength='2' />
				<input name='mois_destru' type='text' value='MM' size='2' maxlength='2' />
				<input name='anne_destru' type='text' value='AAAA' size='4' maxlength='4' />-->
  </td>
 </tr>
 
<tr>
<td></td>
<td>
<input type='submit' name='submit' id='submit' value='Valider'/>
</td>
</tr>
</table>

</fieldset>
</form>
</div>

<div>
<h2>Liste des évenements ouverts</h2>
<table>
<tr>
  <th scope="col">Login organisateur</th>
  <th scope="col">Mot de passe organisateur</th>
  <th scope="col">Courriel organisateur</th>
  <th scope="col">Date destruction</th>
</tr>
<?php while ($donnees = $req->fetch()): ?>
<tr>
  <td scope="col"><?php echo $donnees['org_login'] ?></td>
  <td scope="col"><?php echo $donnees['org_passe'] ?></td>
  <td scope="col"><?php echo $donnees['courriel'] ?></td>
  <td scope="col"><?php echo $donnees['destruction'] ?></td>
  <td scope="col">
    <form action="" method="post">
     <input type="submit" name="submit" value="X"/>
     <input name="IDR" type="hidden" value='<?php echo $donnees['ID_regate'] ?>' />
    </form>
  </td>
</tr>
<?php endwhile; ?>
</tr>
</table>

</div>

<?php 
$req->closeCursor();
xhtml_post(); 
?>