<?php

function Administrateur_selectById($ID_administrateur, $bd = null) {
    global $pdo_path, $user, $pwd, $pdo_options;

    $closeConnection = false;

    try {
        if ($bd == null) {
            $closeConnection = true;
            $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        }

        $sql = 'SELECT * FROM Administrateur '
                . 'WHERE ID_administrateur = ?';
        $req = $bd->prepare($sql);
        $req->execute(array($ID_administrateur));
        if ($req->RowCount() == 0) {
            pageErreur('L\'administrateur demandé n\'existe pas.');
            exit;
        }
        // Tout ce qu'on veut savoir sur la regate
        $administrateur = $req->fetch();

        // Close the connection
        if ($closeConnection)
            $bd = null;

        return $administrateur;
    } catch (Exception $e) {
        pageServerMisconfiguration('Erreur : ' . $e->getMessage());
        exit;
    }
}
