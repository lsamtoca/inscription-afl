<?php

if($_SERVER['HTTP_HOST'] == 'localhost') {
    $host='localhost';
    $user='inscriptions_afl';
    $pwd='inscriptions_afl';
    $db='inscriptions_afl';
}
else {
    // Le fichier suivant, à placer dans un endroit protegé, definit les variables
    // $host, $user, $pwd, $db
    // pout l'acces à la bd
    $unix_base='/homez.462/xnrgates/www/';
    require $unix_base.'basedesnoms/.AFLdb.php';
}

$pdo_path="mysql:host=$host;dbname=$db";

// Cette fonction est déjà definie dans
// basedesnoms/.AFLdb.php
// Il faudra eventuellement la deplacer

if (!function_exists('connect')) {
    function connect() {
        global $host;
        global $pwd;
        global $user;
        global $db;

        $con = mysql_connect("$host","$user","$pwd")
                or
                die("Erreur connexion : ". mysql_error());

        mysql_select_db($db, $con);

        return  $con;
    }
}

?>
