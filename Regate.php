<?php
	session_start();
	if(!isset($_SESSION["ID_regate"]))
	{
	   
		header("Location: LoginClub.php");
		//$_SESSION["ID_regate"]=2;
	}

   require "partage.php";
   xhtml_pre1("Gestion de votre régate");
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
  xhtml_pre2("Gestion de votre régate");

// Update the informations on the database of these are set
try{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
		
	$fields=array('titre','description','cv_organisateur','lieu','date_debut','date_fin','date_cloture_preinscriptions','droits');
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

// Get the informations from the database
try{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
	
	$req = $bdd->prepare('SELECT `ID_regate`, `titre`,`description`, `cv_organisateur`,`lieu`,`date_debut`,`date_fin`,`date_cloture_preinscriptions`,`droits` FROM Regate WHERE ID_regate=?');
	$req->execute(array($_SESSION["ID_regate"]));
    $donnees = $req->fetch();
    $req->closeCursor();	
    
    $TITRE_REGATE= $donnees['titre'];
    $DESC_REGATE= $donnees['description'];
    $CV_ORGANISATEUR=$donnees['cv_organisateur'];
    $LIEU=$donnees['lieu'];
    $DATE_DEBUT_REGATE=$donnees['date_debut'];
    $DATE_FIN_REGATE=$donnees['date_fin'];
    $DATE_CLOTURE_PREINSCRIPTIONS=$donnees['date_cloture_preinscriptions'];
    $DROITS=$donnees['droits'];
    
    $URL=format_url_regate($_SESSION["ID_regate"]);
    $URLPRE=format_url_preinscrits($_SESSION["ID_regate"]);
}
catch(Exception $e){
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}


// Cut the pieces of the page to be displayed into functions

function renseignements(){

global $TITRE_REGATE, $DESC_REGATE, $LIEU, $CV_ORGANISATEUR, $DATE_DEBUT_REGATE, $DATE_FIN_REGATE, $DATE_CLOTURE_PREINSCRIPTIONS, $DROITS;

echo "
<div >

<h2>Renseignements sur la régate</h2>

<form id='mainform' action='' method='post'>
<fieldset>

<label>
Titre :</label>
<textarea id='titre' name='titre' cols='50' rows='1'>$TITRE_REGATE</textarea>
<span id=\'mainform_titre_errorloc\' class=\'error_strings\'></span>
<br />
<label>
Description :
</label>
<textarea id='description' name='description' cols='50' rows='10'>$DESC_REGATE</textarea>

<hr />

<label>
Lieu  :
</label>
<textarea id='lieu' name='lieu' cols='50' rows='1'>$LIEU</textarea>
<span id=\'mainform_lieu_errorloc\' class=\'error_strings\'></span>

<br />

<label>
Club organisateur :
</label>
<textarea id='cv_organisateur' name='cv_organisateur' cols='50' rows='1'>$CV_ORGANISATEUR</textarea>

<hr />
";

echo "<label>
Date debut :
</label>
<script>DateInput('date_debut', true, 'YYYY-MM-DD','$DATE_DEBUT_REGATE')</script>
<span id='mainform_date_debut_errorloc' class='error_strings'></span>
<label>
Date fin :
</label>
<script>DateInput('date_fin', true, 'YYYY-MM-DD','$DATE_FIN_REGATE')</script>
<span id='mainform_date_fin_errorloc' class='error_strings'></span>";


echo "Date de cloture des préinscriptions :
</label>
<script>DateInput('date_cloture_preinscriptions', true, 'YYYY-MM-DD','$DATE_CLOTURE_PREINSCRIPTIONS')</script>
<span id='mainform_date_cloture_preinscriptions_errorloc' class='error_strings'></span>";

/* <label>
 Droits d'inscription :
 </label>
 <input type="number" min="0" max="100" name="droits" value=<?php echo "$DROITS";?> /> &#8364;
*/

echo '
<hr />

<br>

<input type="submit" id="Modifier" value="Modifier" />

</fieldset>
</form>
<script language="JavaScript" type="text/javascript"
    xml:space="preserve">
 var frmvalidator  = new Validator("mainform");
 
 frmvalidator.EnableOnPageErrorDisplay();
 frmvalidator.EnableMsgsTogether();

 frmvalidator.addValidation("titre","required","Champ Titre obligatoire");
 frmvalidator.addValidation("date_debut","required","Champ Date debut obligatoire");
 frmvalidator.addValidation("date_fin","required","Champ Date fin obligatoire");
 frmvalidator.addValidation("lieu","required","Champ Lieu obligatoire");
  
 var year="[1-2][0-9]{3}";
 var mois="0[1-9]|1[0-2]";
 var jour="0[1-9]|[1-2][0-9]|3[0-1]";
 var date= year + "-" + mois + "-" + jour;
 frmvalidator.addValidation("date_debut","regexp=^" + date + "$","Champ Date debut a la forme YYYY-MM-DD"); 
 frmvalidator.addValidation("date_fin","regexp=^" + date + "$","Champ Date fin a la forme YYYY-MM-DD"); 
 frmvalidator.addValidation("date_cloture_preinscriptions","regexp=^" + date + "$","Champ Date cloture a la forme YYYY-MM-DD"); 
</script>

</div>
';

}

function urls(){
global $URL, $URLPRE;

echo "
<div >
<h2>URL d'inscription</h2>
<a href='$URL'>$URL</a>
</div>

<div >
<h2>URL pour consulter la liste des préinscrits (ayant confirmé)</h2>
<a href='$URLPRE'>$URLPRE</a>
</div>";

}

function inscrits(){

  global $bdd;
  
echo '
<!-- Inscrits -->
<div >
<h2>Liste des inscrits</h2>';

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
        if($donnees['statut']=='Licencie'){
        	$statut='Licencié FFV';
        }
        elseif($donnees['statut']=='Etranger'){
               	$statut='Coureur étranger';
        }
        else{
        	$statut='Pas encore licencié';
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

echo '</div>';

}

function exportation(){

echo '
<!--Exportation des données-->
<div>
<h2>Exportation des données</h2>

<ul>
<li>
Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_xls.php">format xls</a>  (pour Excel, OpenOffice).
</li>
<li>
Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_xls.php?confirme=1">format xls</a> (pour Excel, OpenOffice).
</li>
</ul>

<h3>Intégration avec le logiciel FREG</h3>
<ul>
<li>
Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_dbf.php">format dbf</a> (pour FREG, XBase, Excel, OpenOffice,...).
</li>
<li>
Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_dbf.php?confirme=1">format dbf</a> (pour FREG, XBase, Excel, OpenOffice, ...).
</li>
</ul>
Pour importer le fichier ins_dbf.dbf vers votre régate dans FREG, vous devez disposer du module FF_PRE_INS.EXE, à demander par courriel au support de FREG.
</div>
';
}


function menu(){
  echo '
<div id="menu">
  <ul>
  <LI><a href="?item=renseignements">Formulaire renseignements sur la régate</a></LI>
  <LI><A href="?item=urls">Adresses formulaire inscription et liste des préinscripts</A></LI>
  <LI><A href="?item=inscrits">Liste des préinscripts</A></LI>
  <LI><A href="?item=exportation">Exportation des données (et intégration avec FREG)</A></LI>
  <LI><a href="?item=mail">Envoyer un courriel à tous les préinscrits</a></LI>
  <LI>Imprimer des listes pour l\'accueil des participants :
    <ol> 
    <li><a href="?item=emargements&serie=LA4&nocontext&nobackground">Laser 4.7</a></li>
    <li><a href="?item=emargements&serie=LAR&nocontext&nobackground">Laser radial</a></li>
    <li><a href="?item=emargements&serie=LAS&nocontext&nobackground">Laser standard</a></li>
    </ol>
  </LI>
  <!-- <LI><a href="deconnexion.php">Deconnexion</a></LI> -->
  </ul>
</div><!-- menu -->'."\n\n";

}

function deconnexion(){
  echo '<div id="deconnexion">'."\n";
  echo '<p>[<a href="deconnexion.php">Deconnexion</a>]</p>'."\n";
  echo "</div><!-- deconnexion -->\n\n";
}

function back_to_menu(){
  echo '
  <div id="bas">
    <div id="back_to_menu">
      Retour au <a href="?item=menu">menu principal</a>
    </div></div>
    ';
}

function  context(){
  if(!isset($_GET['nocontext']))
  {
      back_to_menu();
      deconnexion();
   }
}

function mails(){
  
  global $bdd;
  

  if(isset($_POST['envoyer_mail'])){
    
    $ME = "inscriptions-afl@regateslaser.info";
	$CC="inscriptions-afl@regateslaser.info";
	if(!$_POST['cc']=="")
	   $CC .=','.$_POST['cc'];
    
    $to=$_POST['to'];
    $subject = my_quoted_printable_encode ($_POST['objet'],58,'subject');
//    $message = $_POST['message'];
  
	 
	$headers  = "From: $ME\r\n" ;
    $headers .= "Reply-To: $ME\r\n";
	$headers .= "CC: $CC\r\n" ;
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    //create a boundary string. It must be unique
    //so we use the MD5 algorithm to generate a random hash
    $random_hash = md5(date('r', time())); 
    $php_mixed="PHP-mixed-$random_hash";
    $php_alt="PHP-alt-$random_hash";
	$headers .= "Content-Type: multipart/mixed; boundary=\"$php_mixed\"" . "\r\n";
	$headers .= "This is a multi-part message in MIME format.\r\n"; 
//	$headers .= 'Content-Type: text/plain; charset="UTF-8"' . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();	
    
    //define the body of the message.
    $message='--'.$php_mixed. "\r\n";
/*    $message.="Content-Type: multipart/alternative; boundary=\"$php_alt\"" . "\r\n";     
    $message.='--'.$php_alt. "\r\n";*/
    $message.='Content-Type: text/plain; charset="UTF-8"'. "\r\n";
    $message.='Content-Transfer-Encoding: 8bit' . "\r\n\r\n";
    $message.= $_POST['message']. "\r\n". "\r\n";
/*    $message.='--'.$php_alt.'--'. "\r\n";*/
    
    
    if($_FILES['attachment']['error'] == 0){
      //echo 'piece jointe trouvée';
      $attachment_tmp_name=$_FILES['attachment']['tmp_name'];
      $attachment_name=$_FILES['attachment']['name'];
      $attachment_type=$_FILES['attachment']['type'];
      
      // The attachment
      //read the attachment file contents into a string,
      //encode it with MIME base64,
      //and split it into smaller chunks
      $attachment = chunk_split(base64_encode(file_get_contents($attachment_tmp_name)));

      $message.='--'.$php_mixed . "\r\n";
      $message.="Content-Type: $attachment_type; name=\"$attachment_name\"" . "\r\n";
      $message.='Content-Transfer-Encoding: base64' . "\r\n";
      $message.='Content-Disposition: attachment' . "\r\n"  . "\r\n";
      $message.=$attachment. "\r\n"  . "\r\n";
    }
   
    // Fin
    $message.='--'.$php_mixed.'--'. "\r\n";

//  echo '<pre>'.$message.'</pre>';
    if(mail($to, $subject, $message, $headers))
     echo "Message envoyé à:\n\t$to";
    
    return;
  }
  
  try {
  
  $req = $bdd->prepare('SELECT mail FROM Inscrit WHERE (ID_regate =?)');
  $req->execute(array($_SESSION["ID_regate"]));  
  $i=0;
  while($mail=$req->fetchColumn(0)) $mails[$i++]=$mail;
  $mails_all=implode(',',$mails);
  
  unset($mails);
  
  $req = $bdd->prepare('SELECT mail FROM Inscrit WHERE (ID_regate =?) and conf=1');
  $req->execute(array($_SESSION["ID_regate"]));  
  $i=0;
  while($mail=$req->fetchColumn(0)) $mails[$i++]=$mail;
  $mails_confirme=implode(',',$mails);
  
  unset($mails);
  
  $req = $bdd->prepare('SELECT mail FROM Inscrit WHERE (ID_regate =?) and conf=0');
  $req->execute(array($_SESSION["ID_regate"]));  
  $i=0;
  while($mail=$req->fetchColumn(0)) $mails[$i++]=$mail;
  $mails_pas_confirme=implode(',',$mails);

  }
  catch(Exception $e)
  {
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
  }

  echo '<div>';
  
/*  echo "<p>Tous les mails : <br />$mails_all</p><br />";
  echo "<p>Les mails de ceux qui ont confirmé : <br />$mails_confirme</p><br />";
  echo "<p>Les mails de ceux qui n'ont pas confirmé : <br />$mails_pas_confirme</p><br />";*/
  
  echo "
  <script type='text/javascript'>
  function set_tous(){
    document.getElementById('to').value=\"$mails_all\";
  }
  function set_confirmes(){
    document.getElementById('to').value=\"$mails_confirme\";
  }
  function set_pas_confirmes(){
    document.getElementById('to').value=\"$mails_pas_confirme\";
  }  
  </script>
  
  <form action='' method='POST' enctype='multipart/form-data'>
  <fieldset>
  Envoyer un courriel à :
  <input type='radio' name='aqui' checked value='tous' onClick='set_tous()'><label>tous les préinscrits</label>
  <input type='radio' name='aqui' value='confirmes' onClick='set_confirmes()'><label>les préinscrits ayant confirmé</label>
  <input type='radio' name='aqui' value='pas_confirmes' onClick='set_pas_confirmes()'><label>ceux qui n'ont pas encore confirmé</label>
  <hr />
  <label>To : </label>
  <br />
  <input type='text' name='to' id='to' style='width:100%;' readonly value='$mails_all' />
  <br />
  <label>CC : </label>
  <br />
  <input type='text' name='cc' style='width:100%;' />
  <br />
  <label>Objet : </label>
  <br />
  <input type='text' name='objet' style='width:100%;' /> 
  <hr />
  <label>Votre message : </label>
  <br />
  <textarea name='message' rows='20' style='width:100%;'></textarea>  
  <hr />
  <!--<input type='hidden' name='MAX_FILE_SIZE' value='12345' />-->
  <label>Fichier à joindre : </label>    <br />
  <input type='file' name='attachment' />
  <hr />
  <input type='submit' name='envoyer_mail' value='Envoyer' />
  </fieldset>
  </form>
  ";
  
  echo '<br />';
  
  echo '</div>';
  
}

function emargements(){

  global $bdd;
  
  echo '
  <!-- Emargements -->
  <div >
  <h2 style="color:black;">Liste pour l\'accueil des pré-inscrits : '. $_GET['serie'] .'</h2>';

  $columns=array(
    'Nom' => 'nom',
    'Prénom' => 'prenom',
    'Confirmé' => 'conf',
    'Numéro de voile' => 'voile',
    'Numéro de voile (corrigé)' => '',
//    'Série' =>'serie',
    'Numéro ISAF' => 'isaf_no',
    'Carte ILCA OK ?' => '',
    'Numéro de licence' => 'num_lic',
    'Licence OK ?' => '',
//    'AFL' => 'adherant',  
    'AFL OK ?' => '',
    'Visa médical OK ?' => '',
    'Déclaration' => '',
//    'Signature' => '',
//    'Autorisation parentale'=> '',
    'No tél à rejoindre en cas de besoin'=> '',
    'Repas (oui/non)'=> '',
  );

  try{
	$req = $bdd->prepare('SELECT *,CONCAT(`prefix_voile`,`num_voile`) as `voile`  FROM Inscrit WHERE (ID_regate =?) AND (serie=?) ORDER BY nom');
	$req->execute(array($_SESSION["ID_regate"],$_GET['serie']));
	
	echo '<table class="mytable">'."\n";
	
	echo '<tr class="mytable">'."\n";
	foreach($columns as $key => $value)
	        echo '<th  class="mytable" scope="col">'.$key.'</th>'."\n";
    echo '</tr>'."\n";
    
    while ($donnees = $req->fetch())
    {
        echo '<tr>'."\n";
	   foreach($columns as $key => $value){
	     echo '<td  class="mytable" scope="col">'."\n";
	     
	     switch($key) {
	       case  'Confirmé':
	         if($donnees[$value]=='1')
	           echo 'Oui';
	         else
	           echo 'Non';
	         break;
	         
	       case  'AFL':
	         if($donnees[$value]=='1')
	           echo 'Oui';
	         else
	           echo 'Non';
	         break;
	       case 'Déclaration':
	       echo '<div style="text-align:justify;font-size:80%;">Je m\'engage à me soumettre aux Règles de Course à la Voile et à toutes autres règles qui régissent cette épreuve.
Il appartient à chaque coureur, sous sa seule responsabilité, de décider s’il doit prendre le départ.</div>';
	       echo 'Signature : <br />';
	       echo str_repeat('&nbsp;',30);
	       break;
	       
	       case 'Signature':
	         echo str_repeat('&nbsp;',30);
	         break;
	       case 'Autorisation parentale':
  	         echo 'Je soussigné, M .... autorise mon enfant ..... à participer
à la régate ..... et dégage la responsabilité des organisateurs quant aux risques inhérents à cette participation.
Signature de l’un des parents (mention nécessaire écrite : Bon pour autorisation parentale).

Fait à       le      ';
              break;
              
	       default:
	         if($value != '') echo $donnees[$value];
	     }
	     echo '</td>'."\n";
	    }
        echo '</tr>'."\n";
    }
    echo '</table>'."\n\n";
   	$req->closeCursor();

  }
  catch(Exception $e)
  {
	 // En cas d'erreur, on affiche un message et on arrête tout
      die('Erreur : '.$e->getMessage());
  }

  echo '</div>';

}


$item='menu';
if(isset($_GET['item']))
  $item=$_GET['item'];

switch($item){
    case 'renseignements':
      renseignements();
      context();
      break;
    case 'urls':
      urls();
      context();
      break;
    case 'inscrits':
      inscrits();
      context();
      break;
    case 'exportation':
      exportation();
      context();
      break;
    case 'mail':
      mails();
      context();
      break;
    case 'emargements':
      emargements();
      context();
      break;
    default:
      menu();
      deconnexion();
      }

xhtml_post();
?>
