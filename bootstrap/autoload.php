<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function autoload($className) {
    $fileName = "$className.php";
    $class_root = __DIR__ . '/..';
    $paths = array('./', "$class_root/classes/", "$class_root/externals/PHPExcel/");

    $found = false;
    foreach ($paths as $path) {
        if (file_exists("$path/$fileName")) {
            require_once "$path/$fileName";
            $found = true;
        } else {
            $directories = scandir($path);
            foreach ($directories as $directory) {
                if (is_dir("$path/$directory") &&
                        file_exists("$path/$directory/$fileName")) {
                    require_once "$path/$directory/$fileName";
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            break;
        }
    }
}

spl_autoload_register('autoload');

