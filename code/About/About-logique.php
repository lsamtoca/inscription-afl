<?php

if (!isset($_SESSION)) {
    session_start();
}
require_once('php/mailer.php');

$form = new Form('form_installerWebRegatta', [
    'submitValue' => 'Envoyer',
    'captcha' => true,
    'label' => 'Demandez au développeur d\'installer ce logiciel sur votre site web.'
        ]);

$subject = new Input('text', 'subject', [
    'label' => 'Objet',
    'value' => 'Installer WebRegatta',
    'style' => 'width:50%',
    'disabled' => true,
    'validations' => [
        ['type' => 'required', 'message' => 'Ce champ est obligatoire']
    ]
        ]);

$expediteur = new Input(
        'text', 'sender', [
    'label' => 'Votre courriel',
    'style' => 'width:50%',
    'validations' => [
        ['type' => 'required', 'message' => 'Ce champ est obligatoire'],
        ['type' => 'email', 'message' => 'Ceci n\'est pas un adresse email valide']
    ]
        ]
);
$message = new Input(
        'textArea', 'message', ['label' => 'Votre message', 'rows' => 10, 'cols' => '120']);

$form->inputs = [$expediteur, $subject, $message];


if ($form->isCompleted()) {
    if (!$form->isValidCaptcha()) {
        $message = "Le captcha est incorrecte";
        pageErreur($message);
        exit(0);
    }

    $form->fromPost();
    $formArray = $form->toArray();
    session_destroy();
//    $deb = new Debugger();
//    $deb->dumpAndExit($formArray);
    
    $sender = $formArray['sender'];
    $to = $config['developerEmail'];
    $subject = $formArray['subject'];
    $message = $formArray['message'];
    if (send_mail_text($sender, $to, $subject, $message)){
        pageAnswer("Message envoyé");
        exit(0);
    }else
    {
        pageErreur("Problème avec l'envoi du courriel");
    }

    exit(0);
} 

