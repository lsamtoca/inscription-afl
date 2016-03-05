<?php
//include_once('undergoingWorks.php');
//exit(0);

if (!isset($_GET['path'])) {
    $requestedPath = 'index.php';
} else {
    $requestedPath = $_GET['path'];
}

$coureurFiles = array(
    'Formulaire', 'Liste_regates', //, 
    'Confirmation',
    'Inscription'
        //'index'
);
$clubFiles = array('Regate', 'Annulation',
    'Liste_inscrits_csv',
    'Liste_inscrits_dbf',
    'Liste_inscrits_xls',
    'accueil_participants'
);
$adminFiles = array(
    'Admin',
    'coureur_dbf_update'
    );
$loginFiles = array('Login', 'changePwd', 'deconnexion');

$paths = array();

function setPaths($files, $subdir, $aut) {
    global $paths;
    foreach ($files as $file) {
        $paths[$file] = array("code/$subdir/$file.php", $aut);
        $paths["$file.php"] = array("code/$subdir/$file.php", $aut);
    }
}

setPaths($coureurFiles, 'Coureur', 'AutNone');
setPaths($adminFiles, 'Admin', 'AutAdmin');
setPaths($clubFiles, 'Club', 'AutClub');
setPaths($loginFiles, 'Login', 'AutLogin');
$paths['Logout'] = array("code/Login/deconnexion.php", 'AutLogin');
$paths['about'] = array("code/About/about.php", 'AutNone');

// Aliases
$paths['index'] = $paths['Liste_regates'];
$paths['index.php'] = $paths['Liste_regates'];
$paths['LoginClub.php'] = $paths['Login'];
$paths['LoginAdmin.php'] = $paths['Login'];

$defaultPath = 'code/Coureur/Liste_regates.php';
//$defaultPath = $requestedPath;
$defaultAuth = 'AutNone';

require_once 'bootstrap.php';

//$_SERVER['REQUEST_URI'] = dirname($_SERVER['PHP_SELF']);
        

$path = $defaultPath;
$aut = $defaultAuth;
if (isset($paths[$requestedPath])) {
    $path = $paths[$requestedPath][0];
    $aut = $paths[$requestedPath][1];
} else {
    if ($requestedPath != '') {
 //       $server = $_SERVER['PHP_SELF'];
 //       $server .= $_SERVER['REQUEST_URI'];
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

include_once($path);
