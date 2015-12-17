<?php

// NORMALISATION DES CHAINES
function nom_normaliser($nom) {
    $noms = explode(' ', $nom);

    $i = 0;
    foreach ($noms as $n) {
        $ns = explode('-', $n);
        $j = 0;
        foreach ($ns as $m)
            $ns[$j++] = ucwords(strtolower($m));
        $noms[$i++] = implode('-', $ns);
    }
    return implode(' ', $noms);
}

// THE FOLLOWING CLEAN FOR WHAT ?
function clean_post_var($var) {
    if (get_magic_quotes_gpc())
        return stripslashes($var);
    else
        return $var;
}
