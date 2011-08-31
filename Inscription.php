<?php

require "partage.php";
  
    function do_mail($to,$message){

	$ME = "lsantoca@cmi.univ-mrs.fr";
	$CC = "lsantoca@cmi.univ-mrs.fr,don.dpk@gmail.com,rochepierre06@hotmail.com,martine.antoine@gmail.com";
	$headers  = "From: $ME\r\n" ;
    $headers .= "Reply-To: $ME\r\n";
	$headers .= "CC: $CC\r\n" ;
    $headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-Type: text/plain; charset="UTF-8"' . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();	
	$subject = "Inscription à la régate, confirmation";
	
	//Comment the following line wh  en not testing
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
	
	$date=$_POST['anne_naissance']."-".$_POST['mois_naissance']."-".$_POST['jour_naissance'];

	$sql = 'INSERT INTO Inscrit (nom, prenom,naissance,num_lic,prefix_voile,num_voile,serie,adherant,sexe,conf,mail,statut,ID_regate)VALUES(:nom,:prenom,:naissance,:num_lic,:prefix_voile,:num_voile,:serie,:adherant,:sexe,:conf,:mail,:statut,:ID_regate)';
	$req = $bdd->prepare($sql);
	$req->execute(array(
		'nom' => $_POST['Nom'],
		'prenom' => $_POST['Prenom'],
		'naissance' => $date,
		'num_lic' => $_POST['lic'],
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

    xhtml_pre("Pre-inscription faite");

    //Nous avons bésoin de connaître l'ID du coureur
    $sql=sprintf("SELECT `ID_inscrit` FROM `Inscrit` WHERE `nom`='%s' and `prenom`='%s' and `ID_regate`='%s' order by `ID_inscrit` DESC",
    $_POST['Nom'],$_POST['Prenom'],$_POST['IDR']);
    echo $sql . "<br>";
    
    $req = $bdd->query($sql);
    
    if ($req->rowCount() < 1) {    // Ce cas ne devrait jaimais arriver 
        printf("Erreur .... j'ai trouvé %d résultats :-(",$req->rowCount());
    }
    else  { // Tout est OK
    $row = $req->fetch();
    
    $url_conformation=format_confirmation_regate($row['ID_inscrit']);
    $message="Bonjour ".$_POST['Prenom'].",\n\n";
    $message.="veuillez confirmer votre inscription à la regate en cliquant le lien suivant:\n";
    $message.=$url_conformation."\n\n";
    $message.="Merci,\n\t l'AFL (ou qui de droit)";
    
    do_mail($_POST['mail'],$message);
    
	echo $confirmation = $_POST['Prenom'].' '.$_POST['Nom']." vous allez recevoir un couriel sur <br />".$_POST['mail']."<br />Ce message contient un lien qui vous permetra de confirmer votre préinscription.<br />Vous avez 30min pour valider votre preinscription.";

   }

    xhtml_post();
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>