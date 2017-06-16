<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AnswerToForms {

    protected $forms = array();

    public function __construct($forms=array()) {
        foreach($forms as $formName => $form){
            $this->forms[$formName] = $form;
        }
    }

    public function execute() {
        if (!$this->isActive()) {
            return;
        }
    }

    public function isActive() {
        foreach ($this->forms as $formName => $form) {

            if ($form->isCompleted()) {
                return $formName;
            }
        }
    }

    public function html($noTabs = 0) {
        foreach ($this->forms as $formName => $form) {
            $form->displayValidation($noTabs);
            $form->displayUl($noTabs);
        }
    }

}
