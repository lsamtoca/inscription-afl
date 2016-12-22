<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class debugger {
    public function dumpAndExit($object){
        echo '<pre>';
        print_r($object);
        echo '</pre>';
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