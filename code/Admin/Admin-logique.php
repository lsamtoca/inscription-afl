<?php

// Do we need the following ?
// Since it has been replaced by Regate::
require_once './php/Regate.php';
require_once './php/User.php';

require_once __DIR__ . '/CourrielUtilisateurs.php';
require_once __DIR__ . '/NouvelleRegate.php';
require_once __DIR__ . '/FichiersFFV.php';
require_once __DIR__ . '/ListeRegates.php';

$courrielUtilisateurs = new CourrielUtilisateurs();
$nouvelleRegate = new NouvelleRegate();
$fichiersFFV = new FichiersFFV();
$listeRegates = new ListeRegates();

if ($nouvelleRegate->isActive()) {
    $nouvelleRegate->execute();
    exit(0);
}
if ($courrielUtilisateurs->isActive()) {
    $courrielUtilisateurs->execute();
    exit(0);
}
if ($fichiersFFV->isActive()) {
    $fichiersFFV->execute();
    exit(0);
}

if ($listeRegates->isActive()) {
    $listeRegates->execute();
    exit(0);
}


