<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PPrinter
 *
 * @author lsantoca
 */
class PPrinter {

    public $noTabs = 0; // We need this to be public

    public function __construct($tabs = 0) {
        $this->noTabs = $tabs;
    }

    public function echoWithTabs($msg) {
        $tabs = str_repeat("\t", $this->noTabs);
        echo "$tabs$msg\n";
    }

    public function echoOpen($str) {
        $this->echoWithTabs($str);
        $this->noTabs+=1;
    }

    public function echoClose($str) {
        $this->noTabs-=1;
        $this->echoWithTabs($str);
    }

    public function jsEchoArray($name, $array) {
        $maxIndex = count($array) - 1;
        $sep = ',';
        $this->echoOpen("$name:{");
        $index = 0;
        foreach ($array as $key => $value) {
            if ($index == $maxIndex) {
                $sep = '';
            }
            if (!is_array($value)) {
                $this->echoWithTabs("$key:$value$sep");
            } else {
                $this->jsEchoArray($key, $value);
                $this->echoWithTabs($sep);
            }
            $index++;
        }
        $this->echoClose("}");
    }

}
