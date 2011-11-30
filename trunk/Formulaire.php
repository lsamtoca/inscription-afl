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

<script type="text/javascript">
 //<![CDATA[

var strings_fr = [];
var strings_en = [];
var labels = [];
var langue = 'fr';

function double_label(label,french,english,at_begin){
  strings_fr[label]=french;
  strings_en[label]=english;
  
  var found=false;
  for(l in labels){
    if(l==label) { found=true; break;}
    }
  if(!found)
    labels.push(label);
    
  if(at_begin) {
    if(langue=='fr')
      document.getElementById(label).innerHTML=strings_fr[label];
    else  
      document.getElementById(label).innerHTML=strings_en[label];
      }
}

function get_string(label){
  if(langue=='fr')
    return strings_fr[label];
  else
    return strings_en[label];
}

function set_anglais(){  
   langue='en';
   document.getElementById('input_lang').value='en';
   add_validations_mainform();
   
   for (i in labels) {
    if(! document.getElementById(labels[i]).innerHTML=='')
      document.getElementById(labels[i]).innerHTML=strings_en[labels[i]];
    }
}

function set_francais(){
    langue='fr';
    document.getElementById('input_lang').value='fr';
       
    add_validations_mainform();

   for (i in labels) {
    if(! document.getElementById(labels[i]).innerHTML=='')
      document.getElementById(labels[i]).innerHTML=strings_fr[labels[i]];
    }    
}

function switch_language(){
    if(langue=='fr')
      set_anglais();
    else  
      set_francais();
    
}


//]]>
</script>

<?php
  xhtml_pre2("Préinscription");
?>



<div id='infos_regate'>
<?php

// Ce code permet de reperer et afficher les infos sur la regate
try
{
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    
    $sql = 'SELECT `ID_regate`,`titre`,`lieu`,`description`,
	 DATE_FORMAT(`date_debut`, \'%d-%m-%Y\') as `date_debut`,
	 DATE_FORMAT(`date_fin`, \'%d-%m-%Y\') as `date_fin`,
	 `date_cloture_preinscriptions`
	 FROM `Regate` 
	 WHERE ID_regate = ?';
    $req = $bdd->prepare($sql);
	$req->execute(array($_GET['regate']));
    $row = $req->fetch();
        
    
    
    echo '<p>';
    // Affichage dates
    if($row['date_debut'] != "00-00-0000" and $row['date_fin'] != "00-00-0000")
	   printf("Du %s au %s : ",$row['date_debut'],$row['date_fin']);
	
	// Affichage titre et lieu
	printf("<b>%s</b>",$row['titre']); 
	if($row['lieu'] != "")
	   printf(" à %s",$row['lieu']);
	echo '.';
	echo '</p>'."\n";
    
    // Affichage description
    echo '<p>';
    echo $row['description'];
    echo '</p>'."\n";
  
    // Lien sur la liste des préinscrits
    $URLPRE=format_url_preinscrits($_GET['regate']);
    echo '<p>';
    echo "<a href='$URLPRE'><span id='liste_preinscrits'></span></a>" ."\n";
    echo '<script type="text/javascript">'."\n";
    echo 'double_label("liste_preinscrits","Liste des préinscrits.","Preregistered sailoirs.",true)'."\n";
    echo '</script>'."\n";
    echo '</p>'."\n";
    

    if($row['date_cloture_preinscriptions'] != ''){
          
      $now = new DateTime;
      $cloture = new DateTime($row['date_cloture_preinscriptions']);
      
      echo '<p>';
      echo '<span id="deadline"></span>'."\n";
      echo '<script type="text/javascript">';
      echo 'double_label("deadline","Date de cloture des préinscriptions : le ","Deadline for preregistration: ",true)'."\n";
      echo '</script>'."\n";
      echo $cloture->format( 'd-m-Y' );
      echo '</p>'."\n";
 
      if($now > $cloture){
            echo '<p>';
            echo 'La date limite pour se préinscrire à cette régate, le '. $cloture->format( 'd-m-Y' ) . ' est passée. <br /> ';
            echo 'Il n\'est plus possible se préinscrire à cette régate :-(';
            echo '</p>';
            xhtml_post();
            die('');
       }
    }
    
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}
?>

</div>


<div id='choix_langue'>
[<a onclick='switch_language()'><span id='choisir_langue'></span></a>]
</div>

<div id='formulaire'>

<form id="mainform" action="Inscription.php" method="post" onsubmit="return validateForm();">
	<fieldset>
	<legend><span id='legend'></span></legend>
	
    <input name="lang" type="hidden" id="input_lang" value="fr" />
	<input name="IDR" type="hidden" id="IDR" value=<?php echo '"'.$_GET['regate'].'"' ;?>/>

<!-- Donnés personnels : nom prenom, date naissance, sexe -->
	<label for="Nom"><span id='l_Nom'></span>:</label>
	<input name="Nom" type="text" id="Nom"/>
	
	<label for="Prenom"><span id='l_Prenom'></span>:</label>
	<input name="Prenom" type="text" id="Prenom"/>
	    <span id='mainform_Nom_errorloc' class='error_strings'></span>
	    <span id='mainform_Prenom_errorloc' class='error_strings'></span>
    <br />
        
    
    <table><tr>
      <td>    <label><span id='l_naissance'></span>:</label></td>
      <td>
      <!--    <input name="naissance" type="text" id="naissance" value="YYYY-MM-DD" size="10" maxlength="10" /> -->
    <script type="text/javascript">DateInput('naissance', true, 'YYYY-MM-DD','1994-01-01')</script> 
    </td>
    <td>    <div id='mainform_naissance_errorloc' class='error_strings'></div></td>
    </tr></table>
  
    <!--<br />-->
    
    
    <input type="radio" name="sexe" id="radio4" value="M"/>
	<label for="radio4"><span id='l_homme'></span></label>
	<input type="radio" name="sexe" id="radio5" value="F"/>
	<label for="radio5"><span id='l_femme'></span></label>
    <span id='mainform_sexe_errorloc' class='error_strings'></span>

    <hr />

<!-- Contact -->
    <label for="mail"><span id='l_mail'></span> :</label>
    <input name="mail" type="text" value="@" id="mail"/>
    <span id='mainform_mail_errorloc' class='error_strings'></span>
    
	
    <hr />
    
<!-- Club -->
    <label for="nom_club"><span id='l_nom_club'></span>:</label>
    <input name="nom_club" type="text" id="nom_club"/>
    <label for="num_club"><span id='l_num_club'></span>:</label>
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

	<label for="Cvoile"><span id='l_Nvoile'></span>:</label>
	<input name="Cvoile" type="text" id="Cvoile" size="3" maxlength="3" value="FRA"/>
   	<input name="Nvoile" type="text" id="Nvoile" size="6" maxlength="6"/>
        <span id='mainform_Cvoile_errorloc' class='error_strings'></span>    
        <span id='mainform_Nvoile_errorloc' class='error_strings'></span>
    <hr />
    
<!-- Statut : Licence et AFL -->
    <input type="radio" name="statut" id="radio1" value="Licencie"/>
	<label for="radio1"><span id='l_ffv'></span></label>

	<input type="radio" name="statut" id="radio3" value="Etranger"/>
	<label for="radio3"><span id='l_etranger'></span></label>
	    
	<input type="radio" name="statut" id="radio2" value="Autre" />
	<label for="radio2"><span id='l_autre'></span></label>

	<span id='mainform_statut_errorloc' class='error_strings'></span>
    <br /> 
	
	<label ><span id='l_afl'></span>:</label>
   	<input type="radio" name="adherant" id="radio9" value="1"/>
	<label for="radio9"><span id='l_oui'></span></label>

	<input type="radio" name="adherant" id="radio10" value="0" />
	<label for="radio10"><span id='l_non'></span></label>
    <span id='mainform_adherant_errorloc' class='error_strings'></span>
    <br />
    
    
    <label for="lic"><span id='l_lic'></span></label>
    <input name="lic" id="lic" type="hidden" size="8" maxlength="8" value=""/>
       
    <label for="isaf_no"><span id="l_isaf_no"></span></label>
    <input name="isaf_no" id="isaf_no" type="hidden" size="7" value=""/>
    
    <span id='mainform_lic_errorloc' class='error_strings'></span>
    <span id='mainform_isaf_no_errorloc' class='error_strings'></span>
    

    <br />
    
    <div id="message"></div>
    
    <hr /> 

<!--  <div id='mainform_errorloc' class='error_strings'></div>-->

    <input type="submit" name="maSoumission" id="soumission" value="Valider"/>
</fieldset>

</form></div>

<script  type="text/javascript" xml:space="preserve">
//<![CDATA[

// Un peu de chaines de caracteres

double_label('choisir_langue','this form in English','ce formulaire en Français',true);

double_label('legend','Préinscription ','Preregistration',true);
double_label('l_Nom','Nom ','Family name',true);
double_label('l_Prenom','Prénom ','First name',true);
double_label('l_naissance','Date de naissance ','Date of birth',true);
double_label('l_homme','Homme ','Male',true);
double_label('l_femme','Femme ','Female',true);
double_label('l_mail','Courriel ','Email',true);
double_label('l_nom_club','Club ','Club',true);
double_label('l_num_club','no ','number',true);
double_label('l_Nvoile','Numéro de voile ','Sail number',true);
double_label('l_ffv','Licencié FFV ','Licenced FFV',true);
double_label('l_etranger','Coureur étranger ','Foreign sailor',true);
double_label('l_autre','Pas encore licencié ','Not licenced yet',true);
double_label('l_afl','Adhérant AFL ','AFL member',true);
double_label('l_oui','Oui','Yes',true);
double_label('l_non','Non','No',true);
double_label('l_lic','Numéro licence :','Licence number:',false);
double_label('l_isaf_no','Numéro ISAF :','ISAF number:',false);
//double_label('maSoumission','Valider','Submit',true);


var message_ffv_fr = "<p>Lors de l'inscription au club "+
        "vous devez présenter votre licence FFV visée par un médecin sportif ou présenter un cértificat médical de moins de trois mois.</p>";

var message_ffv_en = "<p>When completing your registration at the club "+
        "you need to present your FFV licence undersigned by a doctor, or a medical certificate not older than 3 months.</p>";

var message_non_lic_fr ="<p>Vous devez :<ol>" +
   	    "<LI>vous licencier auprès d'un club FFV de votre choix. Cette licence doit être visée par un médecin sportif.</LI>" +
   	    "<li>être affilié à l'Association France Laser. Vous pouvez régulariser cette affiliation soit à l'inscription, soit auprès de votre délégué laser local" +
   	    "http://www.francelaser.org/ (divers -> liste des délégués), ou directement a l'AFL.</li>" +
   	    "</ol></p>";
   	    
var message_non_lic_en ="<p>You must:<ol>" +
   	    "<LI>obtain a FFV licence at a FFV club of your choice. This licence need to be undersigned by a doctor.</LI>" +
   	    "<li>become member of the Association France Laser. You can pay your membership either at registration, or to your local laser delegate" +
   	    "http://www.francelaser.org/ (divers -> liste des délégués), or directly to the AFL.</li>" +
   	    "</ol></p>";
   	    
var message_etr_fr ="<p>Vous devez présenter lors de l'inscription au club :" +
        "<ol><LI>un certificat médical de moins de trois mois,</LI>" +
        "<li>un document attestant que vous avez une assurance responsabilité civile d'un montant d'au moins 1,5MEuros,</li>" +
        "<li>une carte ILCA.</li></ol></p>";
   	    
var message_etr_en ="<p>When registering at the Club you'll need to present :" +
        "<ol><LI>a medical certificate not older than three months,</LI>" +
        "<li>a document giving evidence that you have a third-party insurance to the amount of at least 1,5MEuros,</li>" +
        "<li>an ILCA card.</li></ol></p>";
 


function my_validation_required(champ,validator){ 

   double_label('mainform_'+champ+'_errorloc',
    'Le champ '+ strings_fr['l_'+champ]+ ' est obligatoire',
    'Field '+ strings_en['l_'+champ]+ ' is required',
    false);
    
//   alert(get_string('mainform_'+champ+'_errorloc'));
   validator.addValidation(champ,'required',get_string('mainform_'+champ+'_errorloc'));
}

function my_validation_required_condition(champ,validator,condition,expl_fr,expl_en){ 

   double_label('mainform_'+champ+'_errorloc',
    expl_fr + ' : le champ '+ strings_fr['l_'+champ]+ ' est obligatoire',
    expl_en + ': field '+ strings_en['l_'+champ]+ ' is required',
    false);
    
//   alert(get_string('mainform_'+champ+'_errorloc'));
   validator.addValidation(champ,'required',get_string('mainform_'+champ+'_errorloc'),condition);
}

function my_validation_email(champ,validator){ 
    double_label('mainform_'+champ+'_errorloc',
    'Le champ '+ strings_fr['l_'+champ]+ ' n\'est pas une adresse email valide',
    'Field '+ strings_en['l_'+champ]+ ' is not a valid email address',
    false);
   
   validator.addValidation(champ,'email',get_string('mainform_'+champ+'_errorloc'));
}

function my_validation_radio(champ,validator,french,english){
    double_label('mainform_'+champ+'_errorloc',french,english,false);  
    validator.addValidation(champ,"selone_radio",get_string('mainform_'+champ+'_errorloc')); 
}

function my_validation_regexp(champ,validator,regexp,french,english){
    double_label('mainform_'+champ+'_errorloc',
    'Le champ '+ strings_fr['l_'+champ]+ ' n\'est pas de la forme ' + french,
    'Field '+ strings_en['l_'+champ]+ ' is not of the form ' + english,
    false);
  
    validator.addValidation(champ,'regexp=^'+regexp+'$',get_string('mainform_'+champ+'_errorloc'));
}

 
 
function add_validations_mainform(){

var frmvalidator  = new Validator("mainform");
// Choix de l'affichage des messages d'erreur
frmvalidator.EnableOnPageErrorDisplay();
 // frmvalidator.EnableOnPageErrorDisplaySingleBox();
//frmvalidator.EnableMsgsTogether();
 
 frmvalidator.clearAllValidations();
 frmvalidator.formobj.old_onsubmit = null;
   
  my_validation_required('Nom',frmvalidator);
  my_validation_required('Prenom',frmvalidator);
  my_validation_required('naissance',frmvalidator);
    
  var year="[1-2][0-9]{3}";
  var mois="0[1-9]|1[0-2]";
  var jour="0[1-9]|[1-2][0-9]|3[0-1]";
  var date= year + "-" + mois + "-" + jour;
  my_validation_regexp('naissance',frmvalidator,date,'AAAA-MM-JJ','YYYY-MM-DD'); 
  
  my_validation_radio('sexe',frmvalidator,'Etes vous homme ou femme ?','Are Male or Female ?');
  
  my_validation_required('mail',frmvalidator);
  my_validation_email('mail',frmvalidator); 

  my_validation_required_condition('num_club',frmvalidator,
    "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Licencie')",
    'Vous êtes licencié FFV',
    'You have an FFV licence');

/*  frmvalidator.addValidation("num_club","req","Vous êtes licencié FFV : le numéro du Club obligatoire",
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'1')");*/
 
  my_validation_regexp('num_club',frmvalidator,'[0-9]{5}','NNNNN (5 chiffres)','NNNNN (5 digits)');

  my_validation_radio('serie',frmvalidator,'Choisssez : Standard, Radial, ou 4.7','Choose : Standard, Radial, or 4.7');
  
  my_validation_required('Nvoile',frmvalidator);
  my_validation_regexp('Nvoile',frmvalidator,'[0-9]{1,6}','NNNNNN (au plus 6 chiffres)','NNNNNN (at most 6 digits)');
  my_validation_regexp('Cvoile',frmvalidator,'[A-Z]{3,3}','LLL (3 lettres)','LLL (3 letters');

  my_validation_radio('statut',frmvalidator,'Licencié FFV ?','Do you have an FFV licence?');
  my_validation_radio('adherant',frmvalidator,'Adhérant AFL ?','Are you member of the AFL?');
  
  my_validation_required_condition('lic',frmvalidator,
    "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Licencie')",
    'Vous êtes licencié FFV',
    'You have an FFV licence');
  
/* frmvalidator.addValidation("lic","req","Vous êtes licencié FFV : le numéro de licence obligatoire",
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Licencie')");*/
//  frmvalidator.addValidation("lic","regexp=^[0-9]{7,7}[A-Z]$","Numéro de licence incorrecte (7 chiffres et 1 lettre)");  
    my_validation_regexp('lic',frmvalidator,'[0-9]{7,7}[A-Z]',
      'NNNNNNNL (7 chiffres et 1 lettre)',
      'NNNNNNNL (7 digits and 1 letter)');
    
    
//  frmvalidator.addValidation("isaf_no","req","Vous êtes coureur étranger : le numéro ISAF est obligatoire",
//         "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Etranger')");
 
  my_validation_required_condition('isaf_no',frmvalidator,
    "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Etranger')",
    "Vous êtes coureur étranger",
    'You are an international sailor');
  
 my_validation_regexp('isaf_no',frmvalidator,'[A-Z]{5}[0-9]+',
  'LLLLLN... (5 lettres et au moins 1 chiffre)',
  'LLLLLN... (5 letters and at least 1 digit)');
  
//  frmvalidator.addValidation("isaf_no","regexp=^[A-Z]{5}[0-9]+$","Numéro ISAF incorrecte (5 lettres et au moins une chiffre)");  
}

set_francais();

    
 
// For IE explorer ... sic :-( 
function changeInputType(oldObject, oType) {
  var newObject = document.createElement('input');
  newObject.type = oType;
  if(oldObject.size) newObject.size = oldObject.size;
  if(oldObject.value) newObject.value = oldObject.value;
  if(oldObject.name) newObject.name = oldObject.name;
  if(oldObject.id) newObject.id = oldObject.id;
  if(oldObject.className) newObject.className = oldObject.className;
// Something like the line below ????
//  if(oldObject.add_validation) newObject.add_validation = oldObject.add_validation;
  oldObject.parentNode.replaceChild(newObject,oldObject);
  return newObject;
}

function display_html(id){
  	   document.getElementById(id).innerHTML = get_string(id); 
}

function display_isaf_no(){
  changeInputType(document.getElementById('isaf_no'),'text');
  display_html('l_isaf_no');  
}

function display_lic_no(){
	  //document.getElementById('lic').type='text';
	  changeInputType(document.getElementById('lic'),'text'); // IE sic :-(
	  display_html('l_lic');
}

function hide_isaf_no(){
	  //document.getElementById('isaf_no').type='hidden';
	  changeInputType(document.getElementById('isaf_no'),'hidden');
	  document.getElementById('l_isaf_no').innerHTML ='';
  	  document.getElementById('mainform_isaf_no_errorloc').innerHTML = '';
}


function hide_lic_no(){
  	  //document.getElementById('lic').type='hidden';
   	  changeInputType(document.getElementById('lic'),'hidden');
  	  document.getElementById('l_lic').innerHTML = '';
  	  document.getElementById('mainform_lic_errorloc').innerHTML = '';  
}

  var cas_FFV = document.getElementById('radio1');
  var cas_nonlic = document.getElementById('radio2');
  var cas_etr = document.getElementById('radio3');
  
  //var valider = document.getElementById('soumission');

  cas_FFV.onclick = function()
  {
 	  display_lic_no();
	  display_isaf_no();
	  document.getElementById('mainform_isaf_no_errorloc').innerHTML = '';
	    	  
	  double_label('message',message_ffv_fr,message_ffv_en,true);
	  add_validations_mainform();
  }
  
  cas_etr.onclick = function()
  {  	  
	  hide_lic_no();
	  display_isaf_no();
  	  
	  double_label('message',message_etr_fr,message_etr_en,true);
	  add_validations_mainform();
   } 
  
  cas_nonlic.onclick = function()
  { 	  
 	  hide_lic_no();
 	  hide_isaf_no();  	  
  	  double_label('message',message_non_lic_fr,message_non_lic_en,true);
  	  add_validations_mainform();
  }
   	 
   	 
//]]>   	  
</script>



<?php
xhtml_post();
?>