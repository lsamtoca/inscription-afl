<?php

// this exports the following globals
// $development, $testing, $path_to_site_inscription

$development = true;
$testing = true;
$path_to_site_inscription='';
        
function executeSetupDevelOrProduction() {
    
    global $development,$testing,$path_to_site_inscription;
    
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        $www_site = 'localhost';
        $racine = dirname($_SERVER['REQUEST_URI']) . '/';
        $development = true;
    } else {
        $development = false;
        $www_site = $_SERVER['HTTP_HOST'] . '/';
        $racine = basename(dirname(realpath(__FILE__))) . '/';
        // This might break all if this is placed in a subdirectory ?
    }

    if (
            ($racine == 'inscriptions_afl_dev/')
            or ( $_SERVER['HTTP_HOST'] == 'localhost')
    ) {
        $testing = true;
    } else {
        $testing = false;
    }

    $path_to_site_inscription = $www_site . $racine;
}

executeSetupDevelOrProduction();

//echo $path_to_site_inscription;
//exit(0);