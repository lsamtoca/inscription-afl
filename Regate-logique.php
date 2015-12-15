<?php

require_once 'php/Regate.php';

//global $pdo_path, $user, $pwd, $pdo_options;

function get_mails($which) {

    global $bd;

    switch ($which) {
        case 'confirme':
            $postfix = ' and conf=1';
            break;
        case 'pas_confirme':
            $postfix = ' and conf=0';
            break;
        case 'all':
        default:
            $postfix = "";
            break;
    }

    $sql = "SELECT mail FROM Inscrit WHERE (ID_regate =?)$postfix";
    $assoc = array($_SESSION["ID_regate"]);
    $req = executePreparedQuery($sql, $assoc, $bd);

    $i = 0;
    $mails[0] = "";
    while ($mail = $req->fetchColumn(0)) {
        $mails[$i++] = $mail;
    }

    return implode(',', $mails);
}

function update_field($field, $value) {
    global $bd, $ID_regate;

    $sql = "UPDATE Regate SET `$field`=? WHERE ID_regate =?";
    $cleanedValue = clean_post_var($value);
    $assoc = array($cleanedValue, $ID_regate);
    executePreparedQuery($sql, $assoc, $bd);
}

function get_field_value($field) {
    global $bd, $ID_regate;

    $sql = "SELECT $field FROM Regate WHERE ID_regate =?";
    $assoc = array($ID_regate);
    $req = executePreparedQuery($sql, $assoc, $bd);
    $result = $req->fetch();
    return $result[$field];
}

function validate_post_and_update() {

    // Make a copy of post and work on it
    foreach ($_POST as $key => $value) {
        $post[$key] = $value;
    }

    // Update the informations on the database if these are set
    $fields = array(
        'titre', 'description',
        'cv_organisateur', 'lieu',
        'date_debut', 'date_fin', 'date_limite_preinscriptions',
        'droits', 'courriel', 'paiement_en_ligne', 'informations', 'resultats');

    $messages = '';
    // VALIDATE AND UPDATE $post
    foreach ($fields as $field) {

        if (isset($post[$field])) {

            $postOK = true;
            switch ($field) {

                case 'date_debut':
                case 'date_fin':
                case 'date_limite_preinscriptions':
                    $post[$field] = dateReformatJqueryToMysql($post[$field]);
                    break;

                case 'courriel':
                    $cleaned = filter_var($post[$field], FILTER_VALIDATE_EMAIL);
                    if (!$cleaned) {
                        $postOK = false;
                        $messages.='\'' . $post[$field] . '\' n\'est pas un courriel valide';
                        $messages.="\n";
                        break;
                    }
                    $post[$field] = $cleaned;
                    break;

                case 'resultats':
                case 'paiement_en_ligne':
                    $cleaned = filter_var($post[$field], FILTER_VALIDATE_URL);
                    if (!$cleaned && $post[$field] != '') {
                        $old_value = get_field_value($field);
                        $cleaned_old_value = filter_var($old_value, FILTER_VALIDATE_URL);
                        if (!$cleaned_old_value && $old_value != '') {
                            update_field($field, '');
                        }
                        $postOK = false;
                        $messages.='\'' . $post[$field] . '\' n\'est pas un URL valide';
                        $messages.="\n";
                        break;
                    }
                    $post[$field] = $cleaned;
                    break;

                default:
                    break;
            }

            // Immediately after we have validated it, we do the upate
            if ($postOK) {
                update_field($field, $post[$field]);
            }
        }
    }
    if ($messages != '') {
        pageErreur($messages);
        exit(1);
    }
}

// Get the informations from the database
$ID_regate = $_SESSION["ID_regate"];
$bd = newBd();
validate_post_and_update();

$regate = Regate_selectById($ID_regate, $bd);

$TITRE_REGATE = $regate['titre'];
$DESC_REGATE = $regate['description'];
$CV_ORGANISATEUR = $regate['cv_organisateur'];
$COURRIEL = $regate['courriel'];
$LIEU = $regate['lieu'];
$DATE_DEBUT_REGATE = dateReformatMysqlToJquery($regate['date_debut']);
$DATE_FIN_REGATE = dateReformatMysqlToJquery($regate['date_fin']);
$DATE_LIMITE_PREINSCRIPTIONS = dateReformatMysqlToJquery($regate['date_limite_preinscriptions']);

// Ajouts de 26/11/2015
$DROITS_INSCRIPTION = $regate['droits'];
$paiement_EN_LIGNE = $regate['paiement_en_ligne'];
$INFORMATIONS = $regate['informations'];

$URL = format_url_regate($ID_regate);
$URLPRE = format_url_preinscrits($ID_regate);


// If we are sending an email
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

    if (send_mail_text_attachement($sender, $to, $subject, $message, $cc, $bcc)) {
        pageAnswer("Message envoyé à:\n\t$bcc");
        exit(0);
    }
}
// If we are contacting the helpdesk
if (isset($_POST['helpdesk'])) {

    // From = replyto = to
    $developer = 'luigi.santocanale@lif.univ-mrs.fr';
    // Ici il faudrait ajouter
    // l'admin de la regate, ainsi que Pierre
    $sender = $_SESSION['courriel'];
    $to = $developer;

    $subject = clean_post_var($_POST['objet']);
    $message = clean_post_var($_POST['message']);

    $cc = $bcc = "";

    if (send_mail_text($sender, $to, $subject, $message, $cc, $bcc)) {
        pageAnswer("Message envoyé.\nMerci bien.");
        exit(0);
    }
}

// Get mails of participants
$mails_all = get_mails('all');
$mails_confirme = get_mails('confirme');
$mails_pas_confirme = get_mails('pas_confirme');

