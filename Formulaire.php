<?php
  require "partage.php";
  xhtml_pre1("Préinscription");
?>

<script type="text/javascript" src="classes/calendarDateInput.js">
/***********************************************
* Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/
</script>

<script src="classes/javascript_form/gen_validatorv4.js" type="text/javascript">
/***********************************************
* http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
***********************************************/
</script>

<?php
  xhtml_pre2("Préinscription");
?>



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
<form id="mainform" action="Inscription.php" method="post" onsubmit="return validateForm();">
	<fieldset>
	<legend>Préinscription</legend>
	<input name="IDR" type="hidden" id="IDR" value=<?php echo '"'.$_GET['ID'].'"' ;?>/>

<!-- Donnés personnels : nom prenom, date naissance, sexe -->
	<label for="Nom">Nom :</label>
	<input name="Nom" type="text" id="nom"/>
	
	<label for="Prenom">Prénom :</label>
	<input name="Prenom" type="text" id="prenom"/>
	    <span id='mainform_Nom_errorloc' ></span>
	    <span id='mainform_Prenom_errorloc' class='error_strings'></span>
    <br />
        
    
    <table><tr>
      <td>    <label for="naissance">Date de naissance :</label></td>
      <td>
      <!--    <input name="naissance" type="text" id="naissance" value="YYYY-MM-DD" size="10" maxlength="10" /> -->
    <script>DateInput('naissance', true, 'YYYY-MM-DD','2000-01-01')</script> 
    </td>
    <td>    <div id='mainform_naissance_errorloc' class='error_strings'></div></td>
    </tr></table>
  
    <!--<br />-->
    
    
    <input type="radio" name="sexe" id="radio4" value="M"/>
	<label for="radio4">Homme</label>
	<input type="radio" name="sexe" id="radio5" value="F"/>
	<label for="radio5">Femme</label>
    <div id='mainform_sexe_errorloc' class='error_strings'></div>

    <hr />

<!-- Contact -->
    <label for="mail">Courriel :</label>
    <input name="mail" type="text" value="@" id="mail"/>
    <span id='mainform_mail_errorloc' class='error_strings'></span>
    
	
    <hr />
    
<!-- Club -->
    <label for="nom_club">Club (nom):</label>
    <input name="nom_club" type="text" id="nom_club"/>
    <label for="num_club">(no) : </label>
    <input name="num_club" type="text"  id="num_club" size="5" maxlength="5"/>
    <span id='mainform_num_club_errorloc' class='error_strings'></span>
       
     <hr />
    
<!-- Serie -->
    <input type="radio" name="serie" id="radio6" value="LAS"/>
	<label for="radio6">Laser Standard</label>
	<input type="radio" name="serie" id="radio7" value="LAR"/>
	<label for="radio7">Laser Radial</label>
	<input type="radio" name="serie" id="radio8" value="LA4" />
	<label for="radio8">Laser 4.7</label>
        <span id='mainform_serie_errorloc' class='error_strings'></span>
    <br />

	<label for="Cvoile">Numéro de voile : </label>
	<input name="Cvoile" type="text" id="Cvoile" size="3" maxlenght="3" value="FRA"/>
   	<input name="Nvoile" type="text" id="Nvoile" size="6" maxlenght="6"/>
        <span id='mainform_Cvoile_errorloc' class='error_strings'></span>    <span id='mainform_Nvoile_errorloc' class='error_strings'></span>
    <hr />
    
<!-- Statut : Licence et AFL -->
    <input type="radio" name="statut" id="radio1" value="1"/>
	<label for="radio1">Licencié FFV</label>

	<input type="radio" name="statut" id="radio2" value="2" />
	<label for="radio2">Pas encore licencié</label>

	<input type="radio" name="statut" id="radio3" value="3"/>
	<label for="radio3">Coureur étranger</label>
	    <span id='mainform_statut_errorloc' class='error_strings'></span>
    <br /> 
	
	<label for="AFL">Adhérant à l'AFL :</label>
   	<input type="radio" name="adherant" id="radio9" value="1"/>
	<label for="radio9">Oui</label>

	<input type="radio" name="adherant" id="radio10" value="0" />
	<label for="radio10">Non</label>
    <span id='mainform_adherant_errorloc' class='error_strings'></span>
    <br>
    
    <label id="l_lic" for="lic"></label>
    <input name="lic" id="lic" type="hidden" size="8" maxlenght="8" value=""/>
    <span id='mainform_lic_errorloc' class='error_strings'></span>
    <hr /> 

<!--  <div id='mainform_errorloc' class='error_strings'></div>-->

    <input type="submit" name="maSoumission" id="soumission" value="Valider"/>
</fieldset>

</form>

<script language="JavaScript" type="text/javascript"
    xml:space="preserve">
 //<![CDATA[
 var frmvalidator  = new Validator("mainform");
  
  frmvalidator.EnableOnPageErrorDisplay();
 // frmvalidator.EnableOnPageErrorDisplaySingleBox();
   frmvalidator.EnableMsgsTogether();

 frmvalidator.addValidation("Nom","required","Champ Nom obligatoire");
 frmvalidator.addValidation("Prenom","required","Champ Prénom obligatoire");

 frmvalidator.addValidation("naissance","required","Champ Date de naissance est obligatoire");
  
 var year="[1-2][0-9]{3}";
 var mois="0[1-9]|1[0-2]";
 var jour="0[1-9]|[1-2][0-9]|3[0-1]";
 var date= year + "-" + mois + "-" + jour;
 frmvalidator.addValidation("naissance","regexp=^" + date + "$","Champ Date a la forme YYYY-MM-DD"); 
  
 frmvalidator.addValidation("sexe","selone_radio","Etes vous homme ou femme ?");
  
 frmvalidator.addValidation("mail","required","Champ Courriel obligatoire"); 
 frmvalidator.addValidation("mail","email","Le champ Courriel n'est pas un adresse email"); 

 frmvalidator.addValidation("num_club","req","Vous êtes licencié FFV : le numéro du Club obilgatoire",
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'1')");
 frmvalidator.addValidation("num_club","regexp=^[0-9]{5}$","Champ Club no incorrecte");

 frmvalidator.addValidation("serie","selone_radio","Choisssez : Standard, Radial, ou 4.7"); 
 frmvalidator.addValidation("Nvoile","required","Champ Numéro de Voile obligatoire"); 
 frmvalidator.addValidation("Nvoile","regexp=^[0-9]{1,6}$","Numéro de voile incorrecte"); 
 frmvalidator.addValidation("Cvoile","regexp=^[A-Z]{3,3}$","Code pays sur la voile incorrecte");

 frmvalidator.addValidation("statut","selone_radio","Licencié FFV ?");
 frmvalidator.addValidation("adherant","selone_radio","Adhérant AFL ?");
 
 frmvalidator.addValidation("lic","req","Vous êtes licencié FFV : le numéro de licence obilgatoire",
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'1')");
 frmvalidator.addValidation("lic","regexp=^[0-9]{7,7}[A-Z]$","Numéro de licence incorrecte");  
    
    //]]>
    
    </script>
</div>

<script type="text/javascript">

  var cas_FFV = document.getElementById('radio1');
  var cas_nonlic = document.getElementById('radio2');
  var cas_etr = document.getElementById('radio3');
  
  //var valider = document.getElementById('soumission');

  cas_FFV.onclick = function()
  {
  
      var message = "Vous devez présenter votre licence FFV visée par un médecin sportif ou présenter un cértificat médical de moins de trois mois.";
	  var html= "<br />" + message + "<br /><label id=\"l_lic\" for=\"lic\">Licence numéro :</label>";
	  
	  document.getElementById('lic').type='text';
	  document.getElementById('l_lic').innerHTML = html;

      frmvalidator.addValidation("lic","required","Numéro de licence obilgatoire");  
  }
  cas_nonlic.onclick = function()
  {
   	  document.getElementById('lic').type='hidden';
  	  document.getElementById('l_lic').innerHTML = '<p>Vous devez :<ol><LI>vous licencier auprès d\'un club FFV de votre choix. Cette licence doit être visée par un médecin sportif.</LI><li>être affilié à l\'Association France Laser. Vous pouvez régulariser cette affiliation soit à l\'inscription, soit auprès de votre délégué laser local http://www.francelaser.org/ (divers -> liste des délégués), ou directement a l\'AFL.</li></ol></p>';

  }
  cas_etr.onclick = function()
  {
  	  document.getElementById('lic').type='hidden';
  	  document.getElementById('l_lic').innerHTML = '<p>Vous devez présenter à l\'inscription au club :<ol><LI>un certificat médical de moins de trois mois,</LI><li>un document attestant que vous avez une assurance responsabilité civile d\'un montant d\'au moins 1,5MEuros,</li><li>une carte ILCA.</li></ol></p>';

   } 
   	  
   	  
</script>



<?php
xhtml_post();
?>