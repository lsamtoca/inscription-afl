<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Advertise {

    private $form;

    public function __construct() {


        $this->form = new Form('form_installerWebRegatta', [
            'submitValue' => 'Envoyer',
            'captcha' => true,
            'label' => 'Demandez au développeur d\'installer ce logiciel sur votre site web.'
        ]);

        $this->form->subject = new Input('text', 'subject', [
            'label' => 'Objet',
            'value' => 'Installer WebRegatta',
            'style' => 'width:50%',
            'disabled' => true,
            'validations' => [
                ['type' => 'required', 'message' => 'Ce champ est obligatoire']
            ]
        ]);

        $this->form->expediteur = new Input(
                'text', 'sender', [
            'label' => 'Votre courriel',
            'style' => 'width:50%',
            'validations' => [
                ['type' => 'required', 'message' => 'Ce champ est obligatoire'],
                ['type' => 'email', 'message' => 'Ceci n\'est pas un adresse email valide']
            ]
                ]
        );
        $this->form->message = new Input(
                'textArea', 'message', [
            'label' => 'Votre message', 'rows' => 10, 'cols' => '120',
            'validations' => [
                ['type' => 'required', 'message' => "Vous devez envoyer un quelque message !!!"]
            ]
        ]);
        $this->form->linkInputs();
    }

    public function execute() {
        if (!$this->form->isValidCaptcha()) {
            $message = "Le code de vérification est incorrecte";
            pageErreur($message);
            exit(0);
        }

        $this->form->fromPost();
        $formArray = $this->form->toArray();
        session_destroy();

        $mail = new MyMailer();
        $mail->setFrom($formArray['sender']);
        $mail->addAddressTo($config['developerEmail']);
        $mail->subject = $formArray['subject'];
        $mail->message = $formArray['message'];
        if ($mail->send()) {
            pageAnswer("Message envoyé au développeur.");
            exit(0);
        } else {
            pageErreur("Problème avec l'envoi du courriel");
        }

        exit(0);
    }

    public function isAcitve() {
        return $this->form->isCompleted();
    }

    public function html($noTabs) {
        $this->form->displayValidation($noTabs);
        $this->form->display($noTabs);
    }

}
