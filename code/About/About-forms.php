<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Form WebRegatta

$formWebRegatta = new Form('form_installerWebRegatta', [
    'submitValue' => 'Envoyer',
    'captcha' => true,
    'label' => 'Demandez au développeur d\'installer ce logiciel sur votre site web.'
        ]);

$formWebRegatta->subject = new Input('text', 'subject', [
    'label' => 'Objet',
    'value' => 'Installer WebRegatta',
    'style' => 'width:50%',
    'disabled' => true,
    'validations' => [
        ['type' => 'required', 'message' => 'Ce champ est obligatoire']
    ]
        ]);

$formWebRegatta->expediteur = new Input(
        'text', 'sender', [
    'label' => 'Votre courriel',
    'style' => 'width:50%',
    'validations' => [
        ['type' => 'required', 'message' => 'Ce champ est obligatoire'],
        ['type' => 'email', 'message' => 'Ceci n\'est pas un adresse email valide']
    ]
        ]
);
$formWebRegatta->message = new Input(
        'textArea', 'message', [
    'label' => 'Votre message', 'rows' => 10, 'cols' => '120',
    'validations' => [
        ['type' => 'required', 'message' => "Vous devez envoyer un quelque message !!!"]
    ]
        ]);
$formWebRegatta->linkInputs();

