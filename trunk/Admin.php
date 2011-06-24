<?php
	session_start();
	if(!isset($_SESSION["ID_administrateur"]))
	{
		header("Location: LoginAdmin.php");
	}
	require "partage.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Gestion des Club</title>

</head>
<body>
<div >
       <h1>Gestion des Club</h1>
       <p><a href="deconnexionAdmin.php">Deconnexion</a></p>
       		<form action="" method="post">
			<fieldset>
				<legend>Nouvel Evenement</legend>
				<label for="org_login">Login organisateur :</label>
				<input name="org_login" type="text" id="org_login"/>
				<br />
				<label for="org_passe">Mot de passe organisateur :</label>
				<input name="org_passe" type="text" id="org_passe"/>
				<br />
				<label for="jour_destru">Date de destruction:</label>
				<input name="jour_destru" type="text" value="JJ" size="2" maxlength="2" />
				<input name="mois_destru" type="text" value="MM" size="2" maxlength="2" />
				<input name="anne_destru" type="text" value="AAAA" size="4" maxlength="4" />
				<br />
				<input type="submit" name="submit" id="submit" value="Valider"/>
			</fieldset>
		</form>


</div>
<?php
	if(isset($_POST['IDR']))
	{
		try{
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
				$sql = 'DELETE FROM Regate WHERE ID_regate= :IDR';
				$req = $bdd->prepare($sql);
				$req->execute(array(
				'IDR' => $_POST['IDR']));
			}
			catch(Exception $e){
				// En cas d'erreur, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
				}
	}

	if(isset($_POST['org_login']) && $_POST['org_login']!='' && $_POST['org_passe']!='' && $_POST['jour_destru']!='JJ' && $_POST['mois_destru']!='MM' && $_POST['anne_destru']!='AAAA')
	{
		try{
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
			$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
			$date=$_POST['anne_destru']."-".$_POST['mois_destru']."-".$_POST['jour_destru'];
			$sql = 'INSERT INTO Regate (org_login, org_passe,destruction,ID_administrateur) VALUES(:org_login,:org_passe,:destruction,:ID_administrateur)';
			$req = $bdd->prepare($sql);
			$req->execute(array(
			'org_login' => $_POST['org_login'],
			'org_passe' => $_POST['org_passe'],
			'destruction' => $date,
			'ID_administrateur' => $_SESSION["ID_administrateur"]));
		}
		catch(Exception $e){
			// En cas d'erreur, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
			}
		}
	try{
		// On se connecte à MySQL
    	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
		$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
		$req= $bdd->query('SELECT * FROM Regate');
		echo '<table>';
	    echo
        	'<tr><th scope="col">'.'Login organisateur'.
        	'</th><th scope="col">'.'Mot de passe organisateur'.
        	'</th><th scope="col">'.'Date destruction';
    	while ($donnees = $req->fetch()){
	        echo
        	'<tr><th scope="col">'.$donnees['org_login'] .
        	'</th><th scope="col">'.$donnees['org_passe'].
        	'</th><th scope="col">'.$donnees['destruction'].
        	'</th><th scope="col"><form action="" method="post"><input type="submit" name="submit" id="submit" value="X"/><input name="IDR" type="hidden" id="IDR" value='.$donnees['ID_regate'].'/></form></tr>';
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
</body>
</html>