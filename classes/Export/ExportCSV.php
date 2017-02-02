<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportCSV
 *
 * @author lsantoca
 */
class ExportCsv extends Export {

    public $csvSep = ';';
    public $csvNewLine = "\r\n";

    function __contruct() {
        $this->contentType='text/csv';
    }

    protected function addField($str) {
        $this->content .= $str . $this->csvSep;
    }

    protected function newLine() {
        $this->content.=$this->csvNewLine;
    }

    protected function arrayToLine($array) {
        foreach ($array as $str) {
            $this->addField($str);
        }
    }

    protected function arrayToLineLn($array) {
        $this->arrayToLine($array);
        $this->newLine();
    }

}
