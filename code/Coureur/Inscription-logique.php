<?php

// Difficile de comprendre qqchose de ce code ...
// Reorganisation ???

require_once 'php/hash.php';
require_once 'php/Inscrit.php';
require_once 'php/Regate.php';
require_once 'php/mailer.php';



/* Des fonctions ... */

function validatePOST() {
    // Here this function shall be used to clean up the post !!!

    foreach ($_POST as $key => $value) {
        $post[$key] = $value;
        //filter_var($value, FILTER_VALIDATE_STRING);
        // There is no filter such as FILTER_VALIDATE_STRING
        //if ($post[$key] == NULL) {
        //    $message = "'" . $post[$key] . "' is not a valid string";
        //    pageErreur($message);
        //}
    }

    if (isset($post['mail']) && filter_var($post['mail'], FILTER_VALIDATE_EMAIL) == NULL) {
        $message = "'" . $post[$key] . "' is not a valid email";
        pageErreur($messageInvalidEmail);
        exit(0);
    }

    return $post;
}

function dumpArray($array) {
    foreach ($array as $key => $value) {
        echo "$key => " . $array[$key] . "</br>";
    }
}

/* Fonctions pour former les queries */

function protect($protect, $string) {
    return $protect . $string . $protect;
}

function protect_column($value) {
    return "`$value`";
}

function construct_var($value) {
    $value = str_replace(' ', '_', $value);
    return ":$value";
}

// $fields is a string separated by commas 
function constructInsertQuery($fields) {

    $fields = explode(',', $fields);
    $fields_protected = array_map('protect_column', $fields);
    $columns = implode(',', $fields_protected);


    $fields_content = array_map('construct_var', $fields);
    $values = implode(',', $fields_content);
    $query = "INSERT INTO Inscrit ($columns) VALUES ($values)";
    return $query;
}

function callback($value) {
    $content = construct_var($value);
    return "`$value`=$content";
}

// $fields is a string separated by commas 
function constructUpdateQuery($fields0) {

    $fields1 = explode(',', $fields0);
    $fields2 = array_map('callback', $fields1);
    $pairs = implode(',', $fields2);
    $query = "UPDATE Inscrit SET $pairs WHERE ID_inscrit=:ID_inscrit";
    return $query;
}

function do_update() {
    // Mettre à jour les infos du coureur
    global $post;
    global $regate;
    global $modeConfirm, $ID_inscrit;
    assert($modeConfirm);


    // We first check if the race is still open
    global $messageErrPreregClosed;
    if (!Regate_estOuverte($regate)) {
        pageErreur($messageErrPreregClosed);
        exit(0);
    }

    // We should also update 'date_confirmaton' if 'conf=0'
    // TODO : We need to update 'date_derniere_confirmatiom' ou last_update
    //  
    $fields = "nom,prenom,naissance,num_lic,isaf_no,num_club,nom_club,"
            . "prefix_voile,num_voile,serie,adherant,sexe,"
            . "conf,mail,statut,ID_regate,taille_polo"
    //.,date preinscription"
    ;
    $assoc = array(
        ':nom' => nom_normaliser($post['Nom']),
        ':prenom' => nom_normaliser($post['Prenom']),
        ':naissance' => dateReformatJqueryToMysql($post['naissance']),
        ':num_lic' => strtoupper($post['lic']),
        ':isaf_no' => strtoupper($post['isaf_no']),
        ':num_club' => $post['num_club'],
        ':nom_club' => $post['nom_club'],
        ':prefix_voile' => $post['Cvoile'],
        ':num_voile' => $post['Nvoile'],
        ':serie' => $post['serie'],
        ':adherant' => $post['adherant'],
        ':sexe' => $post['sexe'],
        ':conf' => '1',
        ':mail' => $post['mail'],
        ':statut' => $post['statut'],
        ':taille_polo' => $post['taillepolo'],
        ':ID_regate' => $post['IDR'],
        ':ID_inscrit' => $post['ID_inscrit'],
    );

    $inscrit = Inscrit_selectById($ID_inscrit);
    if ($inscrit['conf'] == 0) {
        date_default_timezone_set('Europe/Paris');
        $assoc[':date_confirmation'] = date('Y-m-d G:i:s');
        $fields.=',date confirmation';
    }

    $sql = constructUpdateQuery($fields);
    executePreparedQuery($sql, $assoc);
}

// Here we need to enforce that you cannot
// subscribe to a race twice
// How to do this :
// we see wehter there is already somebody on the same race
// Is no, OK we proceed
// If yes, we need to enforce that an email is sent to the usbscriber
// But to what price ? We do not want emails to be stolen !!!
// So, if somebody subscribes a second time, but with a different email,
// then raise an error then ask the to contact directly the club

function verify_insert() {
    // If there is already an entry with the same Licence number
    // then raise sopm sort of error
    global $modeInsert;
    assert($modeInsert);
    global $post;

    // For some reason the below do not work.
    //    $ID_regate = strtoupper(filter_input(INPUT_POST, 'IDR'));
    //    $isaf_no = strtoupper(filter_input(INPUT_POST, 'isaf_no', FILTER_SANITIZE_STRING));
    //    $num_lic = strtoupper(filter_input(INPUT_POST, 'lic', FILTER_SANITIZE_STRING));
    $ID_regate = strtoupper($post['IDR']);
    $isaf_no = strtoupper($post['isaf_no']);
    $num_lic = strtoupper($post['lic']);

    // with much probability will return a line
    // what if there is no numero ISAF
    // Or if one is empty
    $sql = "SELECT nom,prenom,mail,ID_inscrit FROM Inscrit WHERE "
            . "ID_regate= :IDregate AND "
            . "(isaf_no <> '' AND (isaf_no=:isafno) OR "
            . "(num_lic <> '' AND num_lic=:numlic));";
    $assoc = array(
        ':IDregate' => $ID_regate,
        ':isafno' => $isaf_no,
        ':numlic' => $num_lic
    );
    $req = executePreparedQuery($sql, $assoc);

    $nbligne = $req->rowCount();
    if ($nbligne >= 1) {
        $row = $req->fetch();
        $nom = $row['nom'];
        $prenom = $row['prenom'];
        $courriel = $row['mail'];
        $id_inscrit = $row['ID_inscrit'];

        global $messageErrAlreadyThere;
        if (strtoupper($courriel) != strtoupper($post['mail'])) {
            pageErreur($messageErrAlreadyThere);
            exit(1);
        }
        return $id_inscrit;
    }
    return -1;
}

function do_insert($hash) {

    global $modeInsert;
    assert($modeInsert);
    global $post;

    // Prepare the sql query
    $fields = "nom,prenom,naissance,num_lic,isaf_no,num_club,nom_club,"
            . "prefix_voile,num_voile,serie,adherant,sexe,"
            . "conf,mail,statut,ID_regate,date preinscription,hash"
    ;
    $sql = constructInsertQuery($fields);

    // Prepare the values
    date_default_timezone_set('Europe/Paris');
    $assoc = array(
        ':nom' => nom_normaliser($post['Nom']),
        ':prenom' => nom_normaliser($post['Prenom']),
        ':naissance' => dateReformatJqueryToMysql($post['naissance']),
        ':num_lic' => strtoupper($post['lic']),
        ':isaf_no' => strtoupper($post['isaf_no']),
        ':num_club' => $post['num_club'],
        ':nom_club' => $post['nom_club'],
        ':prefix_voile' => $post['Cvoile'],
        ':num_voile' => $post['Nvoile'],
        ':serie' => $post['serie'],
        ':adherant' => $post['adherant'],
        ':sexe' => $post['sexe'],
        ':conf' => '0',
        ':mail' => $post['mail'],
        ':statut' => $post['statut'],
        ':ID_regate' => $post['IDR'],
        ':date_preinscription' => date('Y-m-d G:i:s'),
        ':hash' => $hash
    );
    executePreparedQuery($sql, $assoc);

    $inscrit = Inscrit_selectByHashAndOthers(
            $hash, $post['Nom'], $post['Prenom'], $post['IDR']);
    return $inscrit['ID_inscrit'];
}

// Compose the mail...
function compose_mail($ID_inscrit, $ID_regate, $titre_regate, $courriel_cv) {
    global $hashGetString;
    global $regate;
    
    $inscrit = Inscrit_selectById($ID_inscrit);
    $hash = $inscrit['hash'];
    
    //$nom = $inscrit['nom'];
    $prenom = $inscrit['prenom'];
    $courriel_inscrit = $inscrit['mail'];

    // Format the body of answer
    $url_confirmation = format_url_regate($ID_regate);
    $hashString = encodeHashId($hash, $ID_inscrit);
    $url_confirmation .="&$hashGetString=$hashString" . "#formulaires";

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
    $to = $courriel_inscrit;
    $bcc = '';

    $url_paiement = $regate['paiement_en_ligne'];
    $url_aut_parentale=  format_url_aut_parentale();
    $est_mineur = est_mineur($inscrit, $regate);
    $message = // This in Inscription.php
            message_email($prenom, $titre_regate, 
                    $url_confirmation, $url_paiement, $url_aut_parentale,
             $est_mineur);

    return send_mail_text($sender, $to, $subject, $message, $cc, $bcc);
}

// Here it starts the main code
global $post;
$post = validatePOST();

// On vient par la seulement si on a complete le formulaire !!!
if (!isset($post['maSoumission'])) {
    pageErreur('Hacker !!!');
    exit(1);
}
assert('isset($post[\'maSoumission\'])');

include "Inscription-strings.php";


// Determiner le mode de fonctionnement du script :
// - insert ou confirm
global $modeInsert, $modeConfirm;
$modeInsert = true;
$modeConfirm = false;
if ($post['conf'] != '0') {
    $modeInsert = false;
    $modeConfirm = true;
    $ID_inscrit = $post['ID_inscrit'];
}


$ID_regate = $post['IDR'];
//echo $ID_regate;
//exit(0);
$regate = Regate_selectById($ID_regate);
$URLPRE = format_url_preinscrits($ID_regate);
if ($modeInsert) {
    // Si c'est la premiere fois qu'on met à jour
    if (($ID_inscrit = verify_insert()) == -1) {
        $hash = generateHash();
        $ID_inscrit = do_insert($hash);
    }

    $ID_regate = $regate['ID_regate'];
    $titre_regate = $regate['titre'];
    $courriel_cv = $regate['courriel'];

    compose_mail($ID_inscrit, $ID_regate, $titre_regate, $courriel_cv);
}

if ($modeConfirm) {
// Si c'est la premiere fois qu'on met à jour
    do_update();    
}

$inscrit = Inscrit_selectById($ID_inscrit);