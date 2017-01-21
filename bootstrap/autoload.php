<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function autoload($className) {
    $fileName = "$className.php";
    $base=__DIR__.'/..';
    $paths = array('./',"$base/classes/", "$base/externals/PHPExcel/");
    foreach ($paths as $path) {
        if (file_exists("$path/$fileName")) {
            include "$path/$fileName";
            break;
        }
    }
}

spl_autoload_register('autoload');

