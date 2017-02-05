<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AnswerToForm {
    protected $form;
    
    public function __construct() {
        $this->form = new Form('form');
    }
    
    public function execute (){
        if(!$this->isActive()){
            return ;
        }
    }
    
    public function isActive(){
        return $this->form->isCompleted();
    }

    public function html($noTabs=0){
        $this->form->displayValidation($noTabs);
        $this->form->displayUl($noTabs);
    }
}