<?php

require "partage.php";
  
    function do_mail($to,$message){

//	$ME = "lsantoca@cmi.univ-mrs.fr";
	$ME = "inscriptions-afl@regateslaser.info";
	$CC="inscriptions-afl@regateslaser.info";
	//.",don.dpk@gmail.com,rochepierre06@hotmail.com,martine.antoine@gmail.com,lsantoca@cmi.univ-mrs.fr";
	$headers  = "From: $ME\r\n" ;
    $headers .= "Reply-To: $ME\r\n";
	$headers .= "CC: $CC\r\n" ;
    $headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-Type: text/plain; charset="UTF-8"' . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();	
	$subject = my_quoted_printable_encode ("Inscription à la régate, confirmation",'58','subject');
	
	//Comment the following line when not testing
	//$to=$ME;
	

/*    $headers = 'From: benchmark4fvca <henry@cmi.univ-mrs.fr>' . "\r\n" .
            'Reply-To: benchmark4fvca <henry@cmi.univ-mrs.fr>' . "\r\n";
    $to = $submitter['mail'];
    $headers .= 'Cc: ' . $cc . "\r\n";
    $headers .= 'Bcc: benchmark4fvca <henry@cmi.univ-mrs.fr>' . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    //$headers .= 'Reply-To: benchmark4fvca <toto@example.com>' . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();
// envoi*/
    
    return (mail($to, $subject, $message, $headers) == FALSE);

  }

try
{
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
	
//	$date=$_POST['anne_naissance']."-".$_POST['mois_naissance']."-".$_POST['jour_naissance'];

	$sql = 'INSERT INTO Inscrit (nom, prenom,naissance,num_lic,isaf_no,num_club,nom_club,
	prefix_voile,num_voile,serie,adherant,sexe,conf,mail,statut,ID_regate)
	VALUES(:nom,:prenom,:naissance,:num_lic,:isaf_no,:num_club,:nom_club,
	:prefix_voile,:num_voile,:serie,:adherant,:sexe,:conf,:mail,:statut,:ID_regate)';
	$req = $bdd->prepare($sql);
	$req->execute(array(
		'nom' => $_POST['Nom'],
		'prenom' => $_POST['Prenom'],
//		'naissance' => $date,
        'naissance' => $_POST['naissance'],
		'num_lic' => $_POST['lic'],
		'isaf_no' => $_POST['isaf_no'],
		'num_club' => $_POST['num_club'],
		'nom_club' => $_POST['nom_club'],
		'prefix_voile' => $_POST['Cvoile'],
		'num_voile' => $_POST['Nvoile'],
		'serie' => $_POST['serie'],
		'adherant' => $_POST['adherant'],
		'sexe' => $_POST['sexe'],
		'conf' => "0",
		'mail' => $_POST['mail'],
		'statut' => $_POST['statut'],
		'ID_regate' => $_POST['IDR']
	));

    xhtml_pre("Vous êtes (presque) préinscrit");

    //Nous avons bésoin de connaître l'ID du coureur
    $sql=sprintf("SELECT `ID_inscrit` FROM `Inscrit` WHERE `nom`='%s' and `prenom`='%s' and `ID_regate`='%s' order by `ID_inscrit` DESC",
    $_POST['Nom'],$_POST['Prenom'],$_POST['IDR']);
    //echo $sql . "<br>";
    
    $req = $bdd->query($sql);
    
    if ($req->rowCount() < 1) {    // Ce cas ne devrait jaimais arriver 
        printf("Erreur .... j'ai trouvé %d résultats :-(",$req->rowCount());
    }
    else  { // Tout est OK
    $row = $req->fetch();
    
    $url_confirmation=format_confirmation_regate($row['ID_inscrit']);
    
    $message_email_fr="Bonjour ".$_POST['Prenom'].",\n\n"
      ."veuillez confirmer votre inscription à la regate en cliquant le lien suivant:\n"
      .$url_confirmation."\n\n"
      ."Bon vent,\n\t l'AFL";
    
    $message_email_en="Hello ".$_POST['Prenom'].",\n\n"
      ."please confirm your registration to the race by clicking on the following link:\n"
      .$url_confirmation."\n\n"
      ."Bon vent,\n\t the AFL";

     if($_POST['lang']=='en') 
        do_mail($_POST['mail'],$message_email_en);
     else
        do_mail($_POST['mail'],$message_email_fr);
    
    $message_html_fr= $_POST['Prenom'].' '.$_POST['Nom']
      .', nous vous demandons de confirmer votre préinscription. <br /><br/>'
      .'Vous allez recevoir un courriel à l\'adresse <br />'
      .'<div style="margin-left:20mm;margin-top:5mm"><address>'.$_POST['mail'].'</address></div><br />'
      .'Ce courriel contient un lien qui vous permettra de confirmer votre préinscription.';
	// Decommenter la ligne suivante  au cas où cela soit implementé
	//echo "<br />Vous avez 30min pour valider votre preinscription.";
    $message_html_en= $_POST['Prenom'].' '.$_POST['Nom']
      .', we ask you to confirm your preregistration. <br /><br/>'
      ."You'll receive an email at your address <br />"
      .'<div style="margin-left:20mm;margin-top:5mm"><address>'.$_POST['mail'].'</address></div><br />'
      .'This email contains a link by which you\'ll be able to confirm your pregistration.';

    
    if($_POST['lang']=='en') 
      echo $message_html_en;
     else
      echo $message_html_fr;

    echo '<p>';
    echo "Retour au <a href=\"";
    echo format_url_regate($_POST['IDR']);
    echo "\">formulaire d'inscription</a>.";
    echo '</p>';
    
   }

    xhtml_post();
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>