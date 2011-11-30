<?php
	session_start();
	require "partage.php";
	
	if(isset($_SESSION["ID_administrateur"]))
	{
		header("Location: Admin.php");
	}
	if(isset($_POST["submit"]))
	{
		/* Un des champs est manquant */
		if(!isset($_POST["login"]) || !isset($_POST["pass"]))
			$error_login="<span style='color:red'><br /><br />Un ou plusieurs champs sont manquants.</span>";

		else if($_POST["pass"]=="" || $_POST["login"]=="")
			$error_login="<span style='color:red'><br /><br />Un ou plusieurs champs sont manquants.</span>";

		else
		{
			try
			{

				$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
				//
				$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
				$login=$bdd->quote(htmlentities($_POST["login"]));
				$pass=$bdd->quote(htmlentities($_POST["pass"]));
				
				$requete=$bdd->query("select ID_administrateur from Administrateur where admin_login=$login and admin_passe=$pass ;");
				$nbligne=$requete->rowCount();
				if($nbligne==1)
				{
					$reponse=$requete->fetch(PDO::FETCH_ASSOC);
					$_SESSION["ID_administrateur"]=$reponse["ID_administrateur"];
					$requete->closeCursor();
					$bdd=null;
					header("Location: Admin.php");
				}
				else
				{
					$error_login="<span style='color:red'><br /><br />Pseudo ou mot de passe incorrect.</span>";
				}
				$requete->closeCursor();
			}
			catch(Exception $e)
			{
				die('Erreur : '.$e->getMessage());
			}
		}
	}

xhtml_pre('Login Administrateur');
?>

<form action="" method="post">
	<fieldset>
	<legend>Gérer Les Club</legend>
	<label for="login">Login :</label>
	<input name="login" type="text" id="login"/>
    <br />
	<label for="pass">Mot de passe :</label>
	<input name="pass" type="password" id="pass"/>

    <input type="submit" name="submit" id="submit" value="Valider"/>
</fieldset>

</form>

<?php
xhtml_post();
?>