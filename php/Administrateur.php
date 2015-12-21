<?php

function Administrateur_selectById($ID_administrateur, $bd = null) {

    $sql = 'SELECT * FROM Administrateur '
            . 'WHERE ID_administrateur = ?';
    $assoc = array($ID_administrateur);
    $req = executePreparedQuery($sql, $assoc, $bd);

    if ($req->RowCount() == 0) {
        pageErreur('L\'administrateur demandé n\'existe pas.');
        exit;
    }
    // Tout ce qu'on veut savoir sur l'admin
    $administrateur = $req->fetch();
    return $administrateur;
}

function Administrateur_setField($ID_administrateur,$field,$value) {
    
    $sql = "UPDATE `Administrateur` SET $field=:value "
        . "WHERE `ID_Administrateur`=:ID";
    $assoc=array('value' => $value,'ID' => $ID_administrateur);
    executePreparedQuery($sql, $assoc);
}