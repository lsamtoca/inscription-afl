<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function creerRegate($login, $passe, $courriel, $dateDestruction) {
    
    // On verifie d'abord s'il existe déjà un utilisateur avec le meme login   
    $user=  User_selectByLogin($login);
    if ($user != NULL) {
        $message = "Un utilisateur avec le même identifiant '$login' "
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

    $message = "Bonjour,\n\n"
            . "M(me) $adminNom vient de créer une régate pour vous "
            . "sur le site des inscriptions aux régates de l'AFL.\n"
            . "Vous pouvez gérer cette régate en vous identifiant à l'adresse \n"
            . format_url_login()
            . "\n"
            . "Vos identiants sont :\n\tLogin : '$login'\n\tMot de passe : '$mdp'\n"
            . "\n\n"
            . "Cordialement,\n\t l'AFL";

    return send_mail_text($sender, $to, $subject, $message, $cc);
}

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
