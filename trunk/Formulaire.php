<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Préinscription</title>
</head>

<body>
<div ><a><?php
try
{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);

    
    $req = $bdd->prepare('SELECT description FROM Regate WHERE ID_regate = ?');
	$req->execute(array($_GET['ID']));
    $donnees = $req->fetch();
    echo $donnees['description'];


}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?></a></div>


<form action="Inscription.php" method="post">
	<fieldset>
	<legend>Preinscription</legend>
	<input name="IDR" type="hidden" id="IDR" value=<?php echo '"'.$_GET['ID'].'"' ;?>/>

	<label for="Nom">Nom :</label>
	<input name="Nom" type="text" id="nom"/>
	
	<label for="Prenom">Prenom :</label>
	<input name="Prenom" type="text" id="prenom"/>
    <br />
    <label for="jour_naissance">Date de naissance :</label>
    <input name="jour_naissance" type="text" value="JJ" size="2" maxlength="2" />
    <input name="mois_naissance" type="text" value="MM" size="2" maxlength="2" />
    <input name="anne_naissance" type="text" value="AAAA" size="4" maxlength="4" />
    <br />
    <label for="mail">Mail :</label>
    <input name="mail" type="text" value="@" id="mail"/>
    <br />
    <input type="radio" name="sexe" id="radio4" value="1"/>
	<label for="radio4">Homme</label>

	<input type="radio" name="sexe" id="radio5" value="0"/>
	<label for="radio5">Femme</label>
    <hr />
    <input type="radio" name="statut" id="radio1" value="1"/>
	<label for="radio1">Licencié FFV</label>

	<input type="radio" name="statut" id="radio2" value="2"/>
	<label for="radio2">Pas encore licencié</label>

	<input type="radio" name="statut" id="radio3" value="3"/>
	<label for="radio3">Coureur etrangé</label>
    <br /> 
	<label for="AFL">Adhérant à l'AFL :</label>
   	<input type="radio" name="adherant" id="radio9" value="1"/>
	<label for="radio9">Oui</label>

	<input type="radio" name="adherant" id="radio10" value="0"/>
	<label for="radio10">Non</label>
    <hr /> 
    <input type="radio" name="serie" id="radio6" value="1"/>
	<label for="radio6">Laser Standard</label>

	<input type="radio" name="serie" id="radio7" value="2"/>
	<label for="radio7">Laser Radial</label>

	<input type="radio" name="serie" id="radio8" value="3"/>
	<label for="radio8">Laser 4.7</label>
    <br />
	<label id="l_Cvoile" for="Cvoile"></label>
	<input name="Cvoile" type="hidden" id="Cvoile" size="4" maxlenght="4"/>
   	<input name="Nvoile" type="hidden" id="Nvoile" size="8" maxlenght="8"/>
    <br />
    <label id="l_lic" for="lic"></label>
    <input name="lic" id="lic" type="hidden" size="8" maxlenght="8"/>
    <hr />
    <input type="hidden" name="maSoumission" id="soumission" value="Valider"/>
</fieldset>

</form>
<script type="text/javascript">
  var cas_FFV = document.getElementById('radio1');
  var cas_nonlic = document.getElementById('radio2');
  var cas_etr = document.getElementById('radio3');
  var valider = document.getElementById('soumission');

  cas_FFV.onclick = function()
  {
	  document.getElementById('lic').type='text';
	  document.getElementById('l_lic').innerHTML = '<label id="l_lic" for="lic">Vous devez présenter votre licence FFV visée par un médecin sportif ou présenter un cértificat médical de moins de trois mois<br />Numeros Licence :</label>';
	  document.getElementById('l_Cvoile').innerHTML = '<label id="l_Cvoile" for="Cvoile">Numéros de voile :</label>';
	  document.getElementById('Cvoile').type='text';
	  document.getElementById('Nvoile').type='text';
  	  document.getElementById('Cvoile').value='FRA';
   	  document.getElementById('soumission').type='submit';

  }
  cas_nonlic.onclick = function()
  {
  	  document.getElementById('l_lic').innerHTML = '<p>Vous devez vous licencier auprès d\'un club FFV de votre choix.<br />Cette licence doit être visée par un médecin sportif. Vous devez être affilié a L\'Association France Laser ».<br />Vous pouvez régulariser cette affiliation soit a l\'inscription , soit auprès de votre délégué laser local http://www.francelaser.org/ (divers -> liste des délégués), ou directement a l\'AFL</p>';
  	  document.getElementById('lic').type='hidden';
	  document.getElementById('l_Cvoile').innerHTML = '<label id="l_Cvoile" for="Cvoile">Numéros de voile :</label>';
	  document.getElementById('Cvoile').type='text';
	  document.getElementById('Nvoile').type='text';
   	  document.getElementById('soumission').type='submit';
  }
  cas_etr.onclick = function()
  {
  	  document.getElementById('l_lic').innerHTML = '<p>Message : « Vous devez présenter a l\'inscription au club :<br/> Un certificat médical de moins de trois mois.<br/> Un document attestant que vous avez une assurance responsabilité civile d\'un montant d\'au moins 1,5MEuros.<br/> Une carte ILCA.</p>';
  	  document.getElementById('lic').type='hidden';
	  document.getElementById('l_Cvoile').innerHTML = '<label id="l_Cvoile" for="Cvoile">Numéros de voile :</label>';
	  document.getElementById('Cvoile').type='text';
	  document.getElementById('Nvoile').type='text';
   	  document.getElementById('Cvoile').value='';
   	  document.getElementById('soumission').type='submit';  } 
</script>
</body>
</html>
