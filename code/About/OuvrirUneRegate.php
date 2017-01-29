<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class OuvrirUneRegate {

    private $form;

    public function __construct() {

        $this->form = new Form('form_ouvrirUneRegate', [
            'submitValue' => 'Envoyer',
            'captcha' => true,
            'label' => 'Demandez aux administrateurs d\'ouvrir une régate.'
        ]);

        $this->form->subject = new Input('text', 'subject', [
            'label' => 'Objet',
            'value' => "Demande d'ouverture d'une nouvelle régate",
            'style' => 'width:50%',
            'disabled' => true,
            'validations' => [
                ['type' => 'required', 'message' => 'Ce champ est obligatoire']
            ]
        ]);

        $this->form->club = new Input('text', 'club', [
            'label' => 'Votre club',
            'style' => 'width:50%',
            'validations' => [
                ['type' => 'required', 'message' => 'Ce champ est obligatoire']
            ]
        ]);

        $this->form->sender = new Input(
                'text', 'sender', [
            'label' => 'Le courriel du club',
            'style' => 'width:50%',
            'validations' => [
                ['type' => 'required', 'message' => 'Ce champ est obligatoire'],
                ['type' => 'email', 'message' => 'Ceci n\'est pas un adresse email valide']
            ]
                ]
        );
        $this->form->message = new Input(
                'textArea', 'message', [
            'label' => 'Précisez votre demande', 'rows' => 10, 'cols' => '120',
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
        // Try to recall why we need this
        session_destroy();

        // Here we need to add all admins to field to
        $sql = 'SELECT courriel FROM Administrateur WHERE 1';
        $req = executePreparedQuery($sql, array());
        $adminsCourriels = implode(',', $req->fetchall(PDO::FETCH_COLUMN));

        $mail = new MyMailer();
        $mail->addAddressesTo($adminsCourriels);
        $mail->setFrom($formArray['sender']);
        $mail->subject = $formArray['subject'];
        $clubMessage = "Message envoyé par le club $formArray[club] ($formArray[sender]) :";
        $mail->messageText = "$clubMessage\n\n$formArray[message]";
        if ($mail->send()) {
            pageAnswer("Votre message a été envoyé aux administrateurs du site.");
        } else {
            pageErreur("Problème avec l'envoi du courriel");
        }
        exit(0);
    }

    public function isActive() {
        return $this->form->isCompleted();
    }

    public function html($noTabs) {
        $this->form->displayValidation($noTabs);
        $this->form->display($noTabs);
    }

}
