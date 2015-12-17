<?php

/* NOT USED
function execute_sql($filesql) {
    $con = connect();

    $requetes = "";

    $sql = file($filesql); // on charge le fichier SQL

    foreach ($sql as $l) { // on le lit
        if (substr(trim($l), 0, 2) != "--") { // suppression des commentaires
            $requetes .= $l;
        }
    }

    $reqs = split(";", $requetes); // on sépare les requêtes
    foreach ($reqs as $req) { // et on les éxécute
        if (!mysql_query($req, $con) && trim($req) != "") {
            die("ERROR : " . $req); // stop si erreur
        }


        echo '<quote>';
        echo $req;
        echo '</quote>';
        echo '<br>';
        echo '<br>';
    }

    mysql_close($con);

    echo "Fichier " . $filesql . " executé.";
}

*/