<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Debugger {
    public function dump($object){
        echo '<pre>';
        print_r($object);
        echo '</pre>';
    }
    public function dumpAndExit($object){
        $this->dump($object);
        exit(0);
    }
    
    public function config(){
        global $config;
        $this->dumpAndExit($config);
    }
}

function debug($object){
    $deb=new Debugger();
    $deb->dumpAndExit($object);
}