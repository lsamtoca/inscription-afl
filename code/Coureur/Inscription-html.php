<?php

/// We might be inside a function !!!
global $inscrit;

if ($modeInsert) {
    $message = messageAckInsertion($inscrit['prenom']);
    pageAnswer($message, NULL, $dict['titleModeInsert']);
    exit(0);
} else {
    $url_preinscrits=  format_url_preinscrits($post['IDR']);
    $message = messageAckConfirmation($inscrit['prenom'],$url_preinscrits);
    pageAnswer($message, NULL, $dict['titreModeConfirm']);
    exit(0);
}

