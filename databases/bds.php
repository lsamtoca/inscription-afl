<?php

$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $host = 'localhost';
    $user = 'inscriptions-afl';
    $pwd = 'inscriptions-afl';
    $db = 'inscriptions-afl';
    // That is for my Mac !!!
    //      $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_WARNING;
} else {
    // Le fichier suivant, à placer dans un endroit protegé, definit les variables
    // $host, $user, $pwd, $db
    // pout l'acces à la bd

    preg_match('/^.*www\//', __FILE__, $matches);
    $unix_base = $matches[0];
    require $unix_base . 'basedesnoms/.AFLdb.php';
}

$pdo_path = "mysql:host=$host;dbname=$db";

// Cette fonction est déjà definie dans
// basedesnoms/.AFLdb.php
// Il faudra eventuellement la deplacer

if (!function_exists('connect')) {

    function connect() {
        global $host;
        global $pwd;
        global $user;
        global $db;

        $con = mysql_connect("$host", "$user", "$pwd")
                or
                die("Erreur connexion : " . mysql_error());

        mysql_select_db($db, $con);

        return $con;
    }

}

function newBd() {
    global $pdo_path, $user, $pwd, $pdo_options;

    try {
        return new PDO($pdo_path, $user, $pwd, $pdo_options);
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}

function executePreparedQuery($sql, $assoc, $bd = NULL, $debug = FALSE) {
    global $pdo_path, $user, $pwd, $pdo_options;

    try {
        if ($bd == NULL) {
            $bd = newbd();
        }

        $req = $bd->prepare($sql);
        if ($debug) {
            $req->debugDumpParams();
            exit(0); // We exit normally
        }
        $req->execute($assoc);
        // The following is not really needed
        // $bd = null;
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }

    return $req;
}
