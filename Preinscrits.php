<?php

require_once 'partage.php';
if (!isset($_GET['regate'])) {
    pageErreur('Il faut choisir une regate');
    exit;
}

global $regate;

include 'Preinscrits-logique.php';

xhtml_pre('Liste des préinscrits à la régate');

echo '<h1>'.$regate['titre'].'</h1>'."\n\n";
include 'Preinscrits-html.php';

xhtml_post();

