<?php

// Read the bootstrap
require_once 'bootstrap.php';

// ROUTER
// This define the translation table 
// $paths,
// which is an assocation list with key the 
// requested path, and value the real path, with the necessary kind of authorisation access
require_once 'routes.php';

if (!isset($_GET['path'])) {
    $requestedPath = 'index.php';
} else {
    $requestedPath = $_GET['path'];
}

$defaultPath = 'code/Coureur/Liste_regates.php';
//$defaultPath = $requestedPath;
$defaultAuth = 'AutNone';

$path = $defaultPath;
$aut = $defaultAuth;

if (isset($paths[$requestedPath])) {
    $path = $paths[$requestedPath][0];
    $aut = $paths[$requestedPath][1];
} else {
    if ($requestedPath != '') {
        //       $server = $_SERVER['PHP_SELF'];
        //       $server .= $_SERVER['REQUEST_URI'];
        //$message = '404, pas trouvé ' . $server;
        $message = '404, pas trouvé ' . $server;
        pageErreur($message, 'index');
        exit(0);
    }
}


$Login = new Login;
switch ($aut) {
    case 'AutClub':
        session_start();
        $Login->assertClub();
        break;

    case 'AutAdmin':
        session_start();
        $Login->assertAdmin();
        break;

    case 'AutLogin':
    default:
        session_start();
        break;
}

//echo $path;
//exit(0);

include_once($path);
exit(0);
