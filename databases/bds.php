<?php

$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $localhostDbFile = __DIR__ . '/localhostDb.php';
    if (file_exists($localhostDbFile)) {
        require $localhostDbFile;
    } else {
        require $localhostDbFile."_default";
    }
} else {
    require $config['bdFile'];
}

$pdo_path = "mysql:host=$host;dbname=$db";
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
            $bd = newBd();
        }

        if ($debug) {
            echo $sql;
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
