<?php

// this exports the following globals
// $development, $testing, $path_to_site_inscription

global $development, $testing, $path_to_site_inscription, $racine, $unix_base;

$development = true;
$testing = true;
$path_to_site_inscription = '';
$racine = '';
$unix_base = '';

function computeUnixBase() {
    // preg_match('/^.*www\//', __FILE__, $matches);
    // return $matches[0];
    return dirname(dirname(__FILE__)) . '/';
    exit(0);
}


if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $www_site = 'localhost/~lsantoca/';
    $racine = basename(dirname(dirname(realpath(__FILE__)))) . '/';
    //       $racine = dirname($_SERVER['REQUEST_URI']) . '/';
    $development = true;
} else {
    $development = false;
    $www_site = $_SERVER['HTTP_HOST'] . '/';
    $racine = basename(dirname(dirname(realpath(__FILE__)))) . '/';
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

$unix_base = computeUnixBase();
$path_to_site_inscription = $www_site . $racine;
