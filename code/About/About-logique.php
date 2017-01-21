<?php

if (!isset($_SESSION)) {
    session_start();
}

require_once(__DIR__ . '/OuvrirUneRegate.php');
$ouvrirUneRegate = new OuvrirUneRegate();

if ($ouvrirUneRegate->isActive()) {
    $ouvrirUneRegate->execute();
}

if ($config['moduleAdvertise']) {
    require_once(__DIR__ . '/Advertise.php');
    $advertise = new Advertise();

// Gerer la form WebRegatta
    if ($advertise->isAcitve()) {
        $advertise->execute();
    }
}