<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Routing
// Determine the script to load

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
$paths['about'] = array("code/About/About.php", 'AutNone');
$paths['captcha'] = array("externals/captcha/captcha_code_file.php", 'AutNone');
//$paths['captcha'] = array("externals/securimage/securimage_show.php", 'AutNone');
//$paths['TestExport'] = array('classes/TestExport.php','AutNone');
$paths['test'] = array("code/Admin/NouvelleRegate.php", 'AutNone');

// Aliases
$paths['index'] = $paths['Liste_regates'];
$paths['index.php'] = $paths['Liste_regates'];
$paths['LoginClub.php'] = $paths['Login'];
$paths['LoginAdmin.php'] = $paths['Login'];






