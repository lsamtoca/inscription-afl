<?php

require_once './php/Regate.php';
require_once './php/mailer.php';
assertAdmin();

function numberOfSailors($idregate) {
    $sql = 'SELECT COUNT(*) as `num` FROM Inscrit WHERE ID_regate= :IDR';
    $assoc = array('IDR' => $idregate);
    $req = executePreparedQuery($sql, $assoc);
    $req->execute();
    $row = $req->fetch();
    return ($row['num']);
}

function detruireRegate($idregate) {
    $bd = newBD();
    // we delete the inscrits
    $sql1 = 'DELETE FROM Inscrit WHERE ID_regate= :IDR';
    $assoc = array('IDR' => $idregate);
    executePreparedQuery($sql1, $assoc, $bd);
    // we delete the race
    $sql2 = 'DELETE FROM Regate WHERE ID_regate= :IDR';
    executePreparedQuery($sql2, $assoc, $bd);
}

function creerRegate($login, $passe, $courriel, $dateDestruction) {
    // On verifie d'abord s'il existe déjà une rehgate avec meme login
    $sql0 = 'SELECT COUNT(*) as `num` FROM `Regate` '
            . 'WHERE `org_login`=:org_login';
    $assoc0 = array('org_login' => $login);
    $req = executePreparedQuery($sql0, $assoc0);
    $ret = ($req->fetch());
    if ($ret['num'] != '0') {
        $message = "Une regate avec le meme identifiant '$login' "
                . "existe déjà dans la base de données.\n"
                . "Veuillez choisir un autre identifiant";
        pageAnswer($message);
        exit(0);
    }


    // 
//			$date=$_POST['anne_destru']."-".$_POST['mois_destru']."-".$_POST['jour_destru'];
    $date = dateReformatJqueryToMysql($dateDestruction);
    $today = date('Y-m-d');
    $sql = 'INSERT INTO Regate (org_login, coded_org_passe,courriel,'
            . 'destruction,ID_administrateur,'
            . 'date_debut, date_fin, date_limite_preinscriptions) ' .
            'VALUES(:org_login, :coded_org_passe, :courriel, ' .
            ':destruction, :ID_administrateur,'
            . ':date_debut, :date_fin, :date_limite_preinscriptions)';
    $assoc = array(
        'org_login' => $login,
        'coded_org_passe' => md5($passe),
        'courriel' => $courriel,
        'destruction' => $date,
        'ID_administrateur' => $_SESSION['ID_administrateur'],
        'date_debut' => $today,
        'date_fin' => $today,
        'date_limite_preinscriptions' => $today,
    );
    executePreparedQuery($sql, $assoc);
}

// Destruction de regate
if (isset($_POST['IDR'])) {
    $idregate = filter_input(INPUT_POST, 'IDR', FILTER_VALIDATE_INT);
    $regate = Regate_selectById($idregate);

    // Ce test a déjà lieu dans JavaScript
    // Donc on a un doublon, et on ne peux pas en fait detruire 
    // une regate si elle n'est pas destructible
    if (Regate_estDestructible($regate) || numberOfSailors($idregate) == 0
    ) {
        detruireRegate($idregate);
    } else {
        $message = "Pour l'instant, il n'est pas possible detruire cette regate, "
                . "car \n  (1) ou bien la date de destruction de cette regate n'est pas passée,"
                . "\n (2) ou bien la regate a déjà un inscrit";
        pageErreur($message);
        exit(0);
    }
}

function envoyerMail($destinataire, $login, $mdp) {

    $sql = 'SELECT * FROM `Administrateur` WHERE `ID_administrateur`=:ID';
    $assoc = array('ID' => $_SESSION['ID_administrateur']);
    $req = executePreparedQuery($sql, $assoc);
    $admin = $req->fetch();

    $adminNom = $admin['Nom'];
    $adminEmail = $admin['courriel'];

    $cc = $sender = $adminEmail;
    $to = $destinataire;
    $subject = 'Création nouvelle régate sur '
            . 'le site des pré-inscription de l\'AFL';

    $message = "Bonjour,\n"
            . "M(me) $adminNom vient de créer une régate pour votre utilisation"
            . " sur le site d'inscriptions de l'AFL.\n"
            . "Vous pouvez gérer cette régate en vous identifiant à l'adresse \n"
            . format_url_login_club()
            . "\n"
            . "Vos identiants sont :\n\tLogin : '$login'\n\tMot de passe : '$mdp'\n"
            . "\n\n"
            . "Cordialement,\n\t l'AFL";

    return send_mail_text($sender, $to, $subject, $message, $cc);
}

// Création nouvelle régate
if (isset($_POST['org_login'])) {
    $login = filter_input(INPUT_POST, 'org_login');
    $passe = filter_input(INPUT_POST, 'org_passe');
    $dateDestruction = filter_input(INPUT_POST, 'date_destru');
    $courriel = filter_input(INPUT_POST, 'org_courriel', FILTER_VALIDATE_EMAIL);
    if ($login != '' &&
            $passe != '' &&
            $courriel != '' &&
            $dateDestruction != '' &&
            $courriel != FALSE
    ) {
        creerRegate($login, $passe, $courriel, $dateDestruction);
        envoyerMail($courriel, $login, $passe);
    } else {
        $message = "Un des champs ést vide ou le courriel donné n'est pas valide";
        pageErreur($message);
        exit(0);
    }
}

// Preparation des données pour l'affichage 
// Liste de toutes les regates
$sql = 'SELECT * FROM Regate ORDER BY `ID_regate` DESC';
$assoc = array();
$req = executePreparedQuery($sql, $assoc);

// Get informations about dbf/COUREUR.DBF
$fileName = 'dbf/COUREUR.DBF';
if (file_exists($fileName)) {
    $cdbf_lastupdate = date("d/m/Y à H:i:s", filemtime($fileName));
} else {
    $cdbf_lastupdate = '**Ce fichier n\'existe encore pas**';
}
?>