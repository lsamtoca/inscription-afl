<?php
  require "partage.php";
  xhtml_pre("Préinscription");
  
?>



<script type="text/javascript" src="classes/calendarDateInput.js">

/***********************************************
* Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/

</script>

<script src="classes/javascript_form/gen_validatorv4.js" type="text/javascript"></script>

<div >
<?php
try
{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    
    //$req = $bdd->prepare('SELECT * FROM Regate WHERE ID_regate = ?');
    $sql = 'SELECT `ID_regate`,`titre`,`lieu`,`description`,
	 DATE_FORMAT(`date_debut`, \'%d-%m-%Y\') as `date_debut`,
	 DATE_FORMAT(`date_fin`, \'%d-%m-%Y\') as `date_fin` FROM `Regate` 
	 WHERE ID_regate = ?';
    $req = $bdd->prepare($sql);
	$req->execute(array($_GET['ID']));
	
    $row = $req->fetch();
    
    if($row['date_debut'] != "00-00-0000" and $row['date_fin'] != "00-00-0000")
	   printf("Du %s au %s : ",$row['date_debut'],$row['date_fin']);
	printf("<b>%s</b>",$row['titre']); 
	if($row['lieu'] != "")
	   printf(" à %s",$row['lieu']);
    echo ".<br />\n";
    
    echo $row['description'] . ".<br /><p></p><p></p>\n";


}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>

</div>

<div>
<form id="mainform" action="Inscription.php" method="post">
	<fieldset>
	<legend>Préinscription</legend>
	<input name="IDR" type="hidden" id="IDR" value=<?php echo '"'.$_GET['ID'].'"' ;?>/>

<!-- Donnés personnels : nom prenom, date naissance, sexe -->
	<label for="Nom">Nom :</label>
	<input name="Nom" type="text" id="nom"/>
	
	<label for="Prenom">Prénom :</label>
	<input name="Prenom" type="text" id="prenom"/>
    <br />
    
  <!--  <label for="jour_naissance">Date de naissance :</label>
    <input name="jour_naissance" type="text" value="JJ" size="2" maxlength="2" />
    <input name="mois_naissance" type="text" value="MM" size="2" maxlength="2" />
    <input name="anne_naissance" type="text" value="AAAA" size="4" maxlength="4" />-->
    
    
    <div class="tableplain">
    <label for="naissance">Date de naissance :</label>
    <script>DateInput('naissance', true, 'YYYY-MM-DD','2000-01-01')</script>
    </div>
    <br />
    
    
    <input type="radio" name="sexe" id="radio4" value="1"/>
	<label for="radio4">Homme</label>
	<input type="radio" name="sexe" id="radio5" value="0"/>
	<label for="radio5">Femme</label>

    <hr />

<!-- Contact -->
    <label for="mail">Courriel :</label>
    <input name="mail" type="text" value="@" id="mail"/>
    
	
    <hr />
    
<!-- Club -->
    <label for="club">Club (nom):</label>
    <input name="nom_club" type="text" id="nom_club"/>
    <label for="club">(no) : </label>
    <input name="num_club" type="number"  id="num_club"/>
       
     <hr />
<!-- Licence et AFL -->
    <input type="radio" name="statut" id="radio1" value="1"/>
	<label for="radio1">Licencié FFV</label>

	<input type="radio" name="statut" id="radio2" value="2" />
	<label for="radio2">Pas encore licencié</label>

	<input type="radio" name="statut" id="radio3" value="3"/>
	<label for="radio3">Coureur étranger</label>
    <br /> 
	
	<label for="AFL">Adhérant à l'AFL :</label>
   	<input type="radio" name="adherant" id="radio9" value="1"/>
	<label for="radio9">Oui</label>

	<input type="radio" name="adherant" id="radio10" value="0" />
	<label for="radio10">Non</label>
    <hr /> 
    
<!-- Serie -->
    <input type="radio" name="serie" id="radio6" value="1"/>
	<label for="radio6">Laser Standard</label>
	<input type="radio" name="serie" id="radio7" value="2"/>
	<label for="radio7">Laser Radial</label>
	<input type="radio" name="serie" id="radio8" value="3" />
	<label for="radio8">Laser 4.7</label>
    <br />

	<label id="l_Cvoile" for="Cvoile"></label>
	<input name="Cvoile" type="hidden" id="Cvoile" size="3" maxlenght="3"/>
   	<input name="Nvoile" type="hidden" id="Nvoile" size="6" maxlenght="6"/>
    <br />
    <label id="l_lic" for="lic"></label>
    <input name="lic" id="lic" type="hidden" size="8" maxlenght="8"/>
    <hr />
    <input type="hidden" name="maSoumission" id="soumission" value="Valider"/>
</fieldset>

</form>
<script type="text/javascript">
 var frmvalidator  = new Validator("mainform");
 

 frmvalidator.addValidation("Nom","required","Champ Nom obligatoire");
 frmvalidator.addValidation("Prenom","required","Champ Prénom obligatoire");
 frmvalidator.addValidation("mail","required","Champ Courriel obligatoire"); 
 frmvalidator.addValidation("Nvoile","required","Champ Numéro de Voile obligatoire"); 
 frmvalidator.addValidation("naisance","required","Champ Date de naissance est obligatoire"); 
     
 frmvalidator.addValidation("serie","selone_radio","Choisssez : Standard, Radial, ou 4.7");
 frmvalidator.addValidation("sexe","selone_radio","Etes vous homme ou femme ?");
 frmvalidator.addValidation("statut","selone_radio","Licencié FFV ?");
 frmvalidator.addValidation("adherant","selone_radio","Adhérant AFL ?");


 frmvalidator.addValidation("mail","email","Le champ Courriel n'est pas un adresse email"); 
 frmvalidator.addValidation("Nvoile","regexp=^[0-9]{1,6}$","Numéro de voile incorrecte"); 
 frmvalidator.addValidation("Cvoile","regexp=^[A-Z]{3,3}$","Code pays sur la voile incorrecte");
 frmvalidator.addValidation("lic","regexp=^[0-9]{7,7}[A-Z]$","Numéro de licence incorrecte");  
</script>
 

</div>

<script type="text/javascript">
  var cas_FFV = document.getElementById('radio1');
  var cas_nonlic = document.getElementById('radio2');
  var cas_etr = document.getElementById('radio3');
  var valider = document.getElementById('soumission');

  cas_FFV.onclick = function()
  {
	  document.getElementById('lic').type='text';
	  document.getElementById('l_lic').innerHTML = '<label id="l_lic" for="lic">Vous devez présenter votre licence FFV visée par un médecin sportif ou présenter un cértificat médical de moins de trois mois<br />Licence numéro :</label>';
	  document.getElementById('l_Cvoile').innerHTML = '<label id="l_Cvoile" for="Cvoile">Numéro de voile :</label>';
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
  	  document.getElementById('l_lic').innerHTML = '<p>Vous devez présenter a l\'inscription au club :<br/> Un certificat médical de moins de trois mois.<br/> Un document attestant que vous avez une assurance responsabilité civile d\'un montant d\'au moins 1,5MEuros.<br/> Une carte ILCA.</p>';
  	  document.getElementById('lic').type='hidden';
	  document.getElementById('l_Cvoile').innerHTML = '<label id="l_Cvoile" for="Cvoile">Numéros de voile :</label>';
	  document.getElementById('Cvoile').type='text';
	  document.getElementById('Nvoile').type='text';
   	  document.getElementById('Cvoile').value='';
   	  document.getElementById('soumission').type='submit';  } 
</script>


<?php
xhtml_post();
?>