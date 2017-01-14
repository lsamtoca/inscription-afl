<?php

if (!isset($_SESSION)) {
    session_start();
}
require_once('php/mailer.php');
include_once('code/About/About-forms.php');

// Gerer la form WebRegatta
if ($formWebRegatta->isCompleted()) {
    if (!$formWebRegatta->isValidCaptcha()) {
        $message = "Le code de vérification est incorrecte";
        pageErreur($message);
        exit(0);
    }

    $formWebRegatta->fromPost();
    $formArray = $formWebRegatta->toArray();
    session_destroy();
//    $deb = new Debugger();
//    $deb->dumpAndExit($formArray);

    $sender = $formArray['sender'];
    $to = $config['developerEmail'];
    $subject = $formArray['subject'];
    $message = $formArray['message'];
    if (send_mail_text($sender, $to, $subject, $message)) {
        pageAnswer("Message envoyé au développeur.");
        exit(0);
    } else {
        pageErreur("Problème avec l'envoi du courriel");
    }

    exit(0);
}

if ($formOuvrirUneRegate->isCompleted()) {
    if (!$formOuvrirUneRegate->isValidCaptcha()) {
        $message = "Le code de vérification est incorrecte";
        pageErreur($message);
        exit(0);
    }

    $formOuvrirUneRegate->fromPost();
    $formArray = $formOuvrirUneRegate->toArray();
    session_destroy();

    $sender = $formArray['sender'];
    // Here we need to add all admins to field to
    $sql = 'SELECT courriel FROM Administrateur WHERE 1';
    $req = executePreparedQuery($sql, array());
    $adminsCourriels = implode(',', $req->fetchall(PDO::FETCH_COLUMN));
    $to = "$adminsCourriels";
    $subject = $formArray['subject'];
    $clubMessage = "Message envoyé par le club $formArray[club] ($sender) :";
    $message = "$clubMessage\n\n$formArray[message]";
    if (send_mail_text($sender, $to, $subject, $message)) {
        pageAnswer("Votre message a été envoyé aux administrateurs du site.");
        exit(0);
    } else {
        pageErreur("Problème avec l'envoi du courriel");
    }
}
