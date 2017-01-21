<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Logger {

    private $msgAck = '';
    private $msgWarning = '';
    private $msgError = '';
    public $lastError = '';

    private function log($which, $string) {
        $this->$which .= $string;
        if ($which == 'Error') {
            $this->lastError = $string;
        }
    }

    public function logError($string) {
        $this->log('msgError', "Error : $string\n");
    }

    public function logWarning($string) {
        $this->log('msgWarning', "Warning : $string\n");
    }

    public function logAck($string) {
        $this->log('msgOK', "Ack : $string\n");
    }

    public function errors() {
        return $this->msgError;
    }

    public function warnings() {
        return $this->msgWarning;
    }

    public function acks() {
        return $this->msgAck;
    }

    public function report() {
        $errs='';
        if($this->msgError != ''){
            $errs = "Errors:\n$this->msgError\n";
        }
        $warns='';
        if($this->msgWarning != ''){
            $warns = "Warnings:\n$this->msgWarning\n";
        }
        $acks='';
        if($this->msgAck){
            "Acks :\n$this->msgAck\n";
        }
       
        return $errs . $warns . $acks ;
    }

}
