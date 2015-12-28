<?php

if (!isset($_GET['path'])) {
    $requestedPath = 'index.php';
} else {
    $requestedPath = $_GET['path'];
}

$coureurFiles = array('Formulaire', 'Liste_regates', 'index');
$clubFiles = array('Regate', 'Annulation',
    'Liste_inscrits_csv',
    'Liste_inscrits_dbf',
    'Liste_inscrits_xls',
    'accueil_participants'
);
$adminFiles = array('Admin');
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
        

$defaultPath = 'code/Coureur/index.php';
//$defaultPath = $requestedPath;
$defaultAuth = 'AutNone';

$path = $defaultPath;
$aut = $defaultAuth;
if (isset($paths[$requestedPath])) {
    $path = $paths[$requestedPath][0];
    $aut = $paths[$requestedPath][1];
}



require_once 'partage.php';

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
        session_start();
        break;
}

include_once($path);
