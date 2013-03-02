<?php
session_start();
if (!isset($_SESSION['ID_administrateur'])) {
    header('Location: LoginAdmin.php');
}

require_once 'partage.php';

include 'Admin-logique.php';
include 'Admin-html.php';


?>
