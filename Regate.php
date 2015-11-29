<?php

require_once('partage.php');

session_start();

// Load the module Login
$login=new Login;
if (!$login->clubCorrectlyLogged()) {
    header("Location: LoginClub.php");
}

require_once('php/mailer.php');

include 'Regate-logique.php';
include 'Regate-html.php';

