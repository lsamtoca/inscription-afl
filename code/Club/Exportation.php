<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Exportation
 *
 * @author lsantoca
 */
class Exportation extends AnswerToForm {

    public $form;
    private $htmlFile = 'Exportation_html.php';

    public function __construct() {
        $form = new Form('exportFREG', [
            'submitValue' => 'Télechargez le fichier csv',
            'label' => 'Paramétrisez et télechargez le fichier csv pour importation dans FREG'
        ]);

        $form->inscrits = new Input('radios', 'inscrits', [
            'label' => 'Choix des inscrits :',
            'values' => [
                ['name' => 'confirmes', 'label' => 'ceux qui ont confirmé'],
                ['name' => 'all', 'label' => 'tous']
            ],
            'value' => 'confirmes'
        ]);

        $form->groupeClasse_cat = new Input('radios', 'groupesClasse_cat', [
            'label' => 'Methode de création du champs GROUPE et CLASSE_CAT dans FREG :',
            'values' => [
                ['name' => 'laser',
                    'label' => 'Laser',
                    'infos' => 'A venir'],
                ['name' => 'optimist', 'label' => 'Optimist']
            ],
            'value' => 'laser'
        ]);

        $form->linkInputs();
        $this->form = $form;
    }

    function isActive() {
        return $this->form->isCompleted();
//        return isset($_GET['ExportFREG']);
    }

    function html($noTabs=0) {
        ob_start();
        $this->form->displayUl();
        $formHtml = ob_get_clean();
        include($this->htmlFile);
    }

    function execute() {
        if (!$this->isActive()) {
            return;
        }

        $idRegateField = 'ID_regate';
        if (!isset($_SESSION[$idRegateField])) {
            throw new Exception('Pas de régate choisie');
        }
        $idRegate = $_SESSION[$idRegateField];
        $this->form->fromPost();

        $confirmes = $this->form->inscrits->value;
        $methode = $this->form->groupeClasse_cat->value;
        $csv = new ExportFREG($idRegate, $confirmes, $methode);
        $csv->display();
        exit(0);
    }

}
