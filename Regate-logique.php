<?php

global $pdo_path, $user, $pwd, $pdo_options;

function get_mails($which) {

    global $bdd;

    switch ($which) {
        case 'confirme':
            $postfix=' and conf=1';
            break;
        case 'pas_confirme':
            $postfix=' and conf=0';
            break;
        case 'all':
        default:
            $postfix="";
            break;
    }

    $req = $bdd->prepare("SELECT mail FROM Inscrit WHERE (ID_regate =?)$postfix");
    $req->execute(array($_SESSION["ID_regate"]));
    $i = 0;
    $mails[0] = "";
    while ($mail = $req->fetchColumn(0))
        $mails[$i++] = $mail;
    
    return implode(',', $mails);
}

try {
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);

    // Update the informations on the database if these are set
    $fields = array(
        'titre', 'description',
        'cv_organisateur', 'lieu',
        'date_debut', 'date_fin', 'date_limite_preinscriptions',
        'droits', 'courriel');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {

            if (preg_match('/^date_/', $field))
                $_POST[$field] = dateReformatJqueryToMysql($_POST[$field]);

            $sql = "UPDATE Regate SET `$field`=? WHERE ID_regate =?";
            $req = $bdd->prepare($sql);
            $req->execute(array(clean_post_var($_POST[$field]), $_SESSION["ID_regate"]));
            $req->closeCursor();
        }
    }

    // Get the informations from the database
    $req = $bdd->prepare('SELECT `ID_regate`, `titre`,`description`, `cv_organisateur`,`lieu`,`date_debut`,`date_fin`,`date_limite_preinscriptions`,`droits`,`courriel` FROM Regate WHERE ID_regate=?');
    $req->execute(array($_SESSION["ID_regate"]));
    $donnees = $req->fetch();
    $req->closeCursor();

    $TITRE_REGATE = $donnees['titre'];
    $DESC_REGATE = $donnees['description'];
    $CV_ORGANISATEUR = $donnees['cv_organisateur'];
    $COURRIEL = $donnees['courriel'];
    $LIEU = $donnees['lieu'];
    $DATE_DEBUT_REGATE = dateReformatMysqlToJquery($donnees['date_debut']);
    $DATE_FIN_REGATE = dateReformatMysqlToJquery($donnees['date_fin']);
    $DATE_LIMITE_PREINSCRIPTIONS = dateReformatMysqlToJquery($donnees['date_limite_preinscriptions']);
    $DROITS = $donnees['droits'];

    $URL = format_url_regate($_SESSION["ID_regate"]);
    $URLPRE = format_url_preinscrits($_SESSION["ID_regate"]);

    // If we need to send an email
    if (isset($_POST['envoyer_mail'])) {

        // From = replyto = to
        if (isset($_SESSION['courriel']) and $_SESSION['courriel'] != '')
            $sender = $_SESSION['courriel'];
        else
            $sender = "inscriptions-afl@regateslaser.info";
        $to = $sender;

        $subject = clean_post_var($_POST['objet']);
        $message = clean_post_var($_POST['message']);

        $bcc = $_POST['to']; // destinataires en BCC
        $cc = $_POST['cc'];

        if (send_mail_text_attachement($sender, $to, $subject, $message, $cc, $bcc))
            echo "Message envoyé à:\n\t$bcc";

        return;
    }

    // Get mails of participants
    $mails_all = get_mails('all');
    $mails_confirme = get_mails('confirme');
    $mails_pas_confirme = get_mails('pas_confirme');
    
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}
