<?php

function Inscrit_selectById($ID_inscrit, $bd = null) {
    global $pdo_path, $user, $pwd, $pdo_options;

    $closeConnection = false;

    try {
        if ($bd == null) {
            $closeConnection = true;
            $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        }

        $sql = 'SELECT * FROM Inscrit '
                . 'WHERE ID_inscrit = ?';
        $req = $bd->prepare($sql);
        $req->execute(array($ID_inscrit));
        if ($req->RowCount() == 0) {
            pageErreur('Le coureur demandé n\'existe pas.');
            exit;
        }
        // Tout ce qu'on veut savoir sur la regate
        $inscrit = $req->fetch();

        // Close the connection
        if ($closeConnection)
            $bd = null;

        return $inscrit;
    } catch (Exception $e) {
        pageServerMisconfiguration('Erreur : ' . $e->getMessage());
        exit;
    }
}

function Inscrit_selectByIdAndHash($ID_inscrit, $hash, $bd = null) {
    global $pdo_path, $user, $pwd, $pdo_options;

    $closeConnection = false;

    try {
        if ($bd == null) {
            $closeConnection = true;
            $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        }

        $sql = 'SELECT * FROM Inscrit '
                . 'WHERE ID_inscrit=:ID_inscrit AND hash=:hash';
        $req = $bd->prepare($sql);
        $assoc = array(
            ':ID_inscrit' => $ID_inscrit,
            ':hash' => $hash
        );

        $req->execute($assoc);
        if ($req->RowCount() == 0) {
            pageErreur('Le coureur demandé n\'existe pas.');
            exit;
        }
        // Tout ce qu'on veut savoir sur la regate
        $inscrit = $req->fetch();

        // Close the connection
        if ($closeConnection)
            $bd = null;

        return $inscrit;
    } catch (Exception $e) {
        pageServerMisconfiguration('Erreur : ' . $e->getMessage());
        exit;
    }
}

function Inscrit_selectByHashAndOthers($hash, $nom, $prenom, $ID_regate, $bd = null) {
    global $pdo_path, $user, $pwd, $pdo_options;

    $closeConnection = false;

    try {
        if ($bd == null) {
            $closeConnection = true;
            $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        }


        $sql = 'SELECT ID_inscrit FROM Inscrit ' .
                'WHERE hash=:hash AND nom=:nom AND prenom=:prenom AND ID_regate=:idr ' .
                'ORDER BY ID_inscrit DESC';
        $req = $bd->prepare($sql);
        $assoc = array(
            ':hash' => $hash,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':idr' => $ID_regate
        );
        $req->execute($assoc);
        if ($req->RowCount() == 0) {
            pageErreur('Le coureur demandé n\'existe pas.');
            exit;
        }
        // Tout ce qu'on veut savoir sur la regate
        $inscrit = $req->fetch();

        // Close the connection
        if ($closeConnection)
            $bd = null;

        return $inscrit;
    } catch (Exception $e) {
        pageServerMisconfiguration('Erreur : ' . $e->getMessage());
        exit;
    }
}

