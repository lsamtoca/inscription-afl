<?php

require_once 'php/hash.php';
require_once 'php/Inscrit.php';
require_once 'php/Regate.php';
require_once 'php/mailer.php';

// On vient par la seulement si on a complete le formulaire !!!
assert('isset($_POST[\'maSoumission\'])');
if (!isset($_POST['maSoumission'])) {
    pageErreur('Hacker !!!');
}
// Determiner le mode de fonctionnement du script :
// - insert ou confirm
global $modeInsert, $modeConfirm;
$modeInsert = true;
$modeConfirm = false;
if ($_POST['conf'] != '0') {
    $modeInsert = false;
    $modeConfirm = true;
    $ID_inscrit = $_POST['ID_inscrit'];
}


/* Des fonctions ... */


/* Fonctions pour former les queries */

function protect($protect, $string) {
    return $protect . $string . $protect;
}

// $fields is a string separated by commas 
function constructInsertQuery($fields) {

    $fields = explode(',', $fields);
    $protect_column = function ($value) {
                return "`$value`";
            };
    $fields_protected = array_map($protect_column, $fields);
    $columns = implode(',', $fields_protected);

    $make_var = function ($value) {
                $value = str_replace(' ', '_', $value);
                return ":$value";
            };

    $fields_content = array_map($make_var, $fields);
    $values = implode(',', $fields_content);
    $query = "INSERT INTO Inscrit ($columns) VALUES ($values)";
    return $query;
}

// $fields is a string separated by commas 
function constructUpdateQuery($fields) {

    $fields = explode(',', $fields);

    function make_var($value) {
        $value = str_replace(' ', '_', $value);
        return ":$value";
    }

    ;

    $callback = function ($value) {
                $content = make_var($value);
                return "`$value`=$content";
            };

    $fields = array_map($callback, $fields);
    $pairs = implode(',', $fields);
    $query = "UPDATE Inscrit SET $pairs WHERE ID_inscrit=:ID_inscrit";
    return $query;
}

// Add to the bd the informations got from the form
// These have been validated in the client
// pdo is going to clean them up
// TODO : add validations from the server side
// as one could send post data directly, 
// not using the javascript from the form
function do_update() {
    // Mettre à jour les infos du coureur

    global $pdo_path, $user, $pwd, $pdo_options;
    global $regate;
    global $modeConfirm, $ID_inscrit;    
    assert($modeConfirm);

    try {
        // On se connecte à MySQL
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);

        // We first check if the race is still open
        if (!Regate_estOuverte($regate)) {
            $message = 'Cette régate n\'est plus ouverte'
                    . 'aux inscriptions;'
                    . 'vous ne pouvez plus modifier votre inscription.';
            pageErreur($message);
            exit();
        }

        // We should also update 'date_confirmaton' if 'conf=0'
        // TODO : We need to update 'date_derniere_confirmatiom' ou last_update
        //  

        $fields = "nom,prenom,naissance,num_lic,isaf_no,num_club,nom_club,"
                . "prefix_voile,num_voile,serie,adherant,sexe,"
                . "conf,mail,statut,ID_regate"
        //.,date preinscription"
        ;
        $assoc = array(
            ':nom' => nom_normaliser($_POST['Nom']),
            ':prenom' => nom_normaliser($_POST['Prenom']),
            ':naissance' => dateReformatJqueryToMysql($_POST['naissance']),
            ':num_lic' => strtoupper($_POST['lic']),
            ':isaf_no' => strtoupper($_POST['isaf_no']),
            ':num_club' => $_POST['num_club'],
            ':nom_club' => $_POST['nom_club'],
            ':prefix_voile' => $_POST['Cvoile'],
            ':num_voile' => $_POST['Nvoile'],
            ':serie' => $_POST['serie'],
            ':adherant' => $_POST['adherant'],
            ':sexe' => $_POST['sexe'],
            ':conf' => '1',
            ':mail' => $_POST['mail'],
            ':statut' => $_POST['statut'],
            ':ID_regate' => $_POST['IDR'],
            ':ID_inscrit' => $_POST['ID_inscrit'],
        );

        $inscrit = Inscrit_selectById($ID_inscrit, $bd);
        if ($inscrit['conf'] == 0) {
            date_default_timezone_set('Europe/Paris');
            $assoc[':date_confirmation'] = date('Y-m-d G:i:s');
            $fields.=',date confirmation';
        }
        $sql = constructUpdateQuery($fields);

        $req = $bd->prepare($sql);
        $req->execute($assoc);

        // The following not really needed
        // $bd = null;
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}

function do_insert($hash) {

    global $pdo_path, $user, $pwd, $pdo_options;
    global $modeInsert;
    assert($modeInsert);

    try {
        // On se connecte à MySQL
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);

        // Ajouter le coureur parmi les inscrits
        $fields =
                "nom,prenom,naissance,num_lic,isaf_no,num_club,nom_club,"
                . "prefix_voile,num_voile,serie,adherant,sexe,"
                . "conf,mail,statut,ID_regate,date preinscription,hash"
        ;
        $sql = constructInsertQuery($fields);

        date_default_timezone_set('Europe/Paris');
        $assoc = array(
            ':nom' => nom_normaliser($_POST['Nom']),
            ':prenom' => nom_normaliser($_POST['Prenom']),
            ':naissance' => dateReformatJqueryToMysql($_POST['naissance']),
            ':num_lic' => strtoupper($_POST['lic']),
            ':isaf_no' => strtoupper($_POST['isaf_no']),
            ':num_club' => $_POST['num_club'],
            ':nom_club' => $_POST['nom_club'],
            ':prefix_voile' => $_POST['Cvoile'],
            ':num_voile' => $_POST['Nvoile'],
            ':serie' => $_POST['serie'],
            ':adherant' => $_POST['adherant'],
            ':sexe' => $_POST['sexe'],
            ':conf' => '0',
            ':mail' => $_POST['mail'],
            ':statut' => $_POST['statut'],
            ':ID_regate' => $_POST['IDR'],
            ':date_preinscription' => date('Y-m-d G:i:s'),
            ':hash' => $hash
        );


        $req = $bd->prepare($sql);
        $req->execute($assoc);
        // $req->debugDumpParams();
        // exit();

        $inscrit = Inscrit_selectByHashAndOthers(
                $hash, $_POST['Nom'], $_POST['Prenom'], $_POST['IDR'], $bd);

        // The following is not really needed
        // $bd = null;
        return $inscrit['ID_inscrit'];
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}

// Compose the mail...
function compose_mail($ID_inscrit, $hash, $ID_regate, $titre_regate, $courriel_cv) {

    global $message_email_fr, $message_email_fr;
    global $development;
    global $hashGetString;

    // Format the body of answer
    $url_confirmation = format_url_regate($ID_regate);

    $hashString = encodeHashId($hash, $ID_inscrit);
    $url_confirmation .="&$hashGetString=$hashString";

    if ($development) {
        $message = "Cliques ici : <a href=\"$url_confirmation\">$url_confirmation</a>";
        pageErreur($message);
        exit();
    }

    $message_email_fr = "Bonjour " . $_POST['Prenom'] . ",\n\n"
            . "veuillez confirmer votre inscription à la régate '$titre_regate' en cliquant le lien suivant:\n"
            . $url_confirmation . "\n\n"
            . "Vous pouvez modifier les données concernant votre inscription "
            . "à l'aide du lien ci-dessus, jusqu'à la date limite des inscriptions.\n\n"
            . "Si vous souhaitez annuler votre inscription, "
            . "veuillez contacter le club organisateur en repondant à ce courriel.\n\n"
            . "Bon vent,\n\t l'AFL (pour le club organisateur)";

    $message_email_en = "Hello " . $_POST['Prenom'] . ",\n\n"
            . "please confirm your registration to the race '$titre_regate' by clicking on the following link:\n"
            . $url_confirmation . "\n\n"
            . "You'll be able to modify your registration "
            . "by using the link above, until the deadline for registrations.\n\n"
            . "If you wish to cancel your registrion, "
            . "please contact the organizing club by replying to this email.\n\n"
            . "Bon vent,\n\t the AFL (for the organizing club)";

    // Format fields of answer
    $ME = "inscriptions-afl@regateslaser.info";
    $subject = "Inscription à la régate, confirmation";
    if (filter_var($courriel_cv, FILTER_VALIDATE_EMAIL)) {
        $sender = $courriel_cv;
        $cc = $courriel_cv;
    } else {
        $sender = $ME;
        $cc = '';
    }
    $to = $_POST['mail'];
    $bcc = '';


    if ($_POST['lang'] == 'en') {
        $message = $message_email_en;
    } else {
        $message = $message_email_fr;
    }

    if ($development) {
        $sender = $to = 'luigi.santocanale@lif.univ-mrs.fr';
        $cc = $sender;
    }

    return send_mail_text($sender, $to, $subject, $message, $cc, $bcc);
}

function do_mail($ID_inscrit, $hash) {

    global $modeInsert;
    assert($modeInsert);

    global $pdo_path, $user, $pwd, $pdo_options;

    // Nous devons prepaper la réponse.
    // Nous connaissons :
    // $_POST['Nom'],$_POST['Prenom'],$_POST['IDR'];
    // Nous avons bésoin de connaître l'ID du coureur
    // $ID_inscrit,
    // Et le données sur la régate :
    // $titre_regate,$courriel_cv
}

$ID_regate=$_POST['IDR'];
$regate = Regate_selectById($ID_regate);
$URLPRE = format_url_preinscrits($ID_regate);

if ($modeInsert) {
    // Si c'est la premiere fois qu'on met à jour
    $hash = generateHash();
    $ID_inscrit = do_insert($hash);

    $ID_regate = $regate['ID_regate'];
    $titre_regate = $regate['titre'];
    $courriel_cv = $regate['courriel'];

    compose_mail($ID_inscrit, $hash, $ID_regate, $titre_regate, $courriel_cv);
}

if ($modeConfirm) {
    // Si c'est la premiere fois qu'on met à jour
    do_update();
    $inscrit=Inscrit_selectById($ID_inscrit);
    
    //  header("Location:" . format_confirmation_regate($_POST['ID_inscrit']));
}
?>