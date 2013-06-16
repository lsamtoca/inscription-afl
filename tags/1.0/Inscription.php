<?php
require_once 'partage.php';
require_once 'mailer.php';

assert('isset($_POST[\'maSoumission\'])');

echo $_POST['Cvoile'];

function compose_mail($ID_inscrit,$titre_regate,$courriel_cv) {

    $url_confirmation=format_confirmation_regate($ID_inscrit);

    $ME = "inscriptions-afl@regateslaser.info";
    $subject="Inscription à la régate, confirmation";

    if(filter_var($courriel_cv, FILTER_VALIDATE_EMAIL)) {
        $sender=$courriel_cv;
        $cc=$courriel_cv;
    }
    else {
        $sender=$ME;
        $cc='';
    }
    $to=$_POST['mail'];

    $message_email_fr="Bonjour ".$_POST['Prenom'].",\n\n"
            ."veuillez confirmer votre inscription à la régate '$titre_regate' en cliquant le lien suivant:\n"
            .$url_confirmation."\n\n"
            ."Bon vent,\n\t l'AFL (pour le club organisateur)";

    $message_email_en="Hello ".$_POST['Prenom'].",\n\n"
            ."please confirm your registration to the race '$titre_regate' by clicking on the following link:\n"
            .$url_confirmation."\n\n"
            ."Bon vent,\n\t the AFL (for the organizing club)";

    if($_POST['lang']=='en')
        $message = $message_email_en;
    else
        $message = $message_email_fr;


    $bcc='';

    return send_mail_text($sender,$to,$subject,$message,$cc,$bcc);
}

try {
    // On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);

    // Ajouter le coureur parmi les inscrits
//	$date=$_POST['anne_naissance']."-".$_POST['mois_naissance']."-".$_POST['jour_naissance'];

    $sql = 'INSERT INTO Inscrit (nom,prenom,naissance,num_lic,isaf_no,num_club,nom_club,
	prefix_voile,num_voile,serie,adherant,sexe,conf,mail,statut,ID_regate,`date preinscription`)
	VALUES(:nom,:prenom,:naissance,:num_lic,:isaf_no,:num_club,:nom_club,
	:prefix_voile,:num_voile,:serie,:adherant,:sexe,:conf,:mail,:statut,:ID_regate,:date_preinscription)';
    $req = $bdd->prepare($sql);
    $req->execute(array(
            'nom' => nom_normaliser($_POST['Nom']),
            'prenom' => nom_normaliser($_POST['Prenom']),
//		'naissance' => $date,
            'naissance' => $_POST['naissance'],
            'num_lic' => strtoupper($_POST['lic']),
            'isaf_no' => strtoupper($_POST['isaf_no']),
            'num_club' => $_POST['num_club'],
            'nom_club' => $_POST['nom_club'],
            'prefix_voile' => $_POST['Cvoile'],
            'num_voile' => $_POST['Nvoile'],
            'serie' => $_POST['serie'],
            'adherant' => $_POST['adherant'],
            'sexe' => $_POST['sexe'],
            'conf' => '0',
            'mail' => $_POST['mail'],
            'statut' => $_POST['statut'],
            'ID_regate' => $_POST['IDR'],
            'date_preinscription' =>  date('Y-m-d G:i:s')
    ));

    // Prepaper la réponse :
    // Nous avons bésoin de connaître l'ID du coureur
    // Et possiblement des données sur la régate :
    // Titre de la regate et courriel_du_cv

    $sql='SELECT ID_inscrit FROM Inscrit '.
            'WHERE nom=:nom AND prenom=:prenom AND ID_regate=:idr '.
            'ORDER BY ID_inscrit DESC';
    $req = $bdd->prepare($sql);
    $req->execute(array(
            'nom' => $_POST['Nom'],
            'prenom' => $_POST['Prenom'],
            'idr' =>$_POST['IDR'],
    ));
    assert('$req->rowCount() > 0');
    $row = $req->fetch();
    $ID_inscrit=$row['ID_inscrit'];

    // QUERY la regate
    $sql='SELECT * FROM Regate WHERE ID_regate=:idr';
    $req = $bdd->prepare($sql);
    $req->execute(array(
            'idr' =>$_POST['IDR'],
    ));
    assert('$req->rowCount() == 1');
    $row = $req->fetch();

    $titre_regate = $row['titre'];
    $courriel_cv = $row['courriel'];

    compose_mail($ID_inscrit,$titre_regate,$courriel_cv);

}
catch(Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>


<?php xhtml_pre("Vous êtes (presque) préinscrit");?>

<?php if($_POST['lang']=='en'): ?>

    <?php echo $_POST['Prenom'].' '.$_POST['Nom']; ?>,
we ask you to confirm your preregistration. 
<br /><br/>
You'll receive an email at your address <br />
<div style="margin-left:20mm;margin-top:5mm">
    <address><?php echo $_POST['mail'] ?></address>
</div><br />
This email contains a link by which you'll be able to confirm your preregistration.

<?php else: ?>
    <?php echo $_POST['Prenom'].' '.$_POST['Nom']; ?>,
nous vous demandons de confirmer votre préinscription. 
<br /><br/>

    <?php if(isset($_POST['no_email'])): ?>

Vous allez recevoir un courriel 
à votre adresse.
Ce courriel contient un lien qui vous permettra de confirmer votre préinscription.

    <?php else: ?>
Vous allez recevoir un courriel 
à l'adresse <br />
<div style="margin-left:20mm;margin-top:5mm">
    <address><?php echo $_POST['mail'] ?></address>
</div><br />
Ce courriel contient un lien qui vous permettra de confirmer votre préinscription.
    <?php endif; ?>
<?php endif; ?>


<p>
    Retour au
    <a href="<?php echo format_url_regate($_POST['IDR']); ?>"> formulaire d'inscription</a>
</p>

<?php xhtml_post(); ?>