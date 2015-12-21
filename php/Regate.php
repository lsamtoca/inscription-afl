<?php

$Regate_htmlDateFormat = 'd/m/Y';

function Regate_selectById($ID_regate,$bd=NULL) {
    
    $sql = 'SELECT * FROM Regate '
            . 'WHERE ID_regate = ?';
    $assoc = array($ID_regate);
    $req = executePreparedQuery($sql,$assoc,$bd);
    if ($req->RowCount() == 0) {
        pageErreur('La régate demandée n\'existe pas.');
        exit(0);
    }
    // Tout ce qu'on veut savoir sur la regate
    $regate = $req->fetch();

    return Regate_setLimite($regate);
}

function Regate_setLimite($regate) {
    date_default_timezone_set('Europe/Paris');
    $limite = new DateTime($regate['date_limite_preinscriptions']);
    $limite->setTime(23, 59);
    $regate['limite'] = $limite;
    return $regate;
}

function Regate_estOuverte($regate) {

    if ($regate['date_limite_preinscriptions'] == '')
        return true;
    else {
        if (!isset($regate['limite']))
            $regate = Regate_setLimite($regate);

        date_default_timezone_set('Europe/Paris');
        $now = new DateTime;
    }
    return ($now <= $regate['limite']);
}

function Regate_estDestructible($regate) {

    date_default_timezone_set('Europe/Paris');
    $now = new DateTime;
    $destruction = new DateTime($regate['destruction']);
//    echo $destruction->format('d/m/Y');
//    echo $now->format('d/m/Y');
//    exit;
    return ($destruction < $now);
}

function Regate_formatDeadline($regate) {

    global $Regate_htmlDateFormat;
    return $regate['limite']->format($Regate_htmlDateFormat);
}

function Regate_formatDebut($regate) {

    global $Regate_htmlDateFormat;

    list($year, $month, $day) = sscanf($regate['date_debut'], "%4d-%2d-%2d");
    $ret = str_replace(array('d', 'm', 'Y'), array($day, $month, $year), $Regate_htmlDateFormat);

    return $ret;
}

function Regate_formatFin($regate) {

    global $Regate_htmlDateFormat;

    list($year, $month, $day) = sscanf($regate['date_fin'], "%4d-%2d-%2d");
    $ret = str_replace(array('d', 'm', 'Y'), array($day, $month, $year), $Regate_htmlDateFormat);

    return $ret;
}

function Regate_setField($ID_regate,$field,$value) {
    
    $sql = "UPDATE `Regate` SET $field=:value "
        . "WHERE `ID_REGATE`=:ID";
    $assoc=array('value' => $value,'ID' => $ID_regate);
    executePreparedQuery($sql, $assoc);
}