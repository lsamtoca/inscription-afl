<?php

$Regate_htmlDateFormat = 'd/m/Y';

function Regate_selectById($ID_regate) {
    global $pdo_path, $user, $pwd, $pdo_options;

    try {

        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        $sql = 'SELECT * FROM Regate '
                . 'WHERE ID_regate = ?';
        $req = $bd->prepare($sql);
        $req->execute(array($ID_regate));
        if ($req->RowCount() == 0) {
            pageErreur('La régate demandée n\'existe pas.');
            exit;
        }
        // Tout ce qu'on veut savoir sur la regate
        $regate = $req->fetch();

        date_default_timezone_set('Europe/Paris');
        $limite = new DateTime($regate['date_limite_preinscriptions']);
        $limite->setTime(23, 59);
        $regate['limite'] = $limite;
        return $regate;
    } catch (Exception $e) {
        pageServerMisconfiguration('Erreur : ' . $e->getMessage());
        exit;
    }
}

function Regate_estOuverte($regate) {

    if ($regate['date_limite_preinscriptions'] == '')
        return true;
    else {
        date_default_timezone_set('Europe/Paris');
        $now = new DateTime;
//        $limite = new DateTime($regate['date_limite_preinscriptions']);
//        $limite->setTime(23, 59);
    }
    return ($now <= $regate['limite']);
}


function Regate_formatDeadline($regate) {

    global $Regate_htmlDateFormat;
    return $regate['limite']->format($Regate_htmlDateFormat);
}

function Regate_formatDebut($regate) {

    global $Regate_htmlDateFormat;
 
    list($year,$month,$day)=sscanf($regate['date_debut'],"%4d-%2d-%2d");
    $ret=str_replace(array('d','m','Y'),array($day,$month,$year),$Regate_htmlDateFormat);
    
    return $ret;
}

function Regate_formatFin($regate) {

    global $Regate_htmlDateFormat;

    list($year,$month,$day)=sscanf($regate['date_fin'],"%4d-%2d-%2d");
    $ret=str_replace(array('d','m','Y'),array($day,$month,$year),$Regate_htmlDateFormat);

   
    return $ret;
}
