<?php

require_once './php/Regate.php';

/*
function executeSqlWithArray($sql,$array){
    global $pdo_path, $user, $pwd, $pdo_options;
    try{
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        $req = $bd->prepare($sql);
        $req->execute($array);
        return $req;
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
*/

function numberOfSailors($idregate) {
    global $pdo_path, $user, $pwd, $pdo_options;

    try {
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        $sql = 'SELECT COUNT(*) as `num` FROM Inscrit WHERE ID_regate= :IDR';
        $req = $bd->prepare($sql);
        $req->execute(array('IDR' => $idregate));
        $row = $req->fetch();
        return ($row['num']);
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}

function detruireRegate($idregate) {
    global $pdo_path, $user, $pwd, $pdo_options;

    try {
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);

        //we delete all the inscrits for that races
        $sql = 'DELETE FROM Inscrit WHERE ID_regate= :IDR';
        $req = $bd->prepare($sql);
        $req->execute(array('IDR' => $idregate));
        // we delete the race
        $sql = 'DELETE FROM Regate WHERE ID_regate= :IDR';
        $req = $bd->prepare($sql);
        $req->execute(array('IDR' => $idregate));
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}

function creerRegate($login, $passe, $courriel, $dateDestruction) {
    global $pdo_path, $user, $pwd, $pdo_options;

    try {
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
//			$date=$_POST['anne_destru']."-".$_POST['mois_destru']."-".$_POST['jour_destru'];
        $date = dateReformatJqueryToMysql($dateDestruction);
        $today = date('Y-m-d');
        $sql = 'INSERT INTO Regate (org_login, org_passe,courriel,destruction,ID_administrateur,date_debut,date_fin,date_limite_preinscriptions) VALUES(:org_login,:org_passe,:courriel,:destruction,:ID_administrateur,
			:date_debut,:date_fin,:date_limite_preinscriptions)';
        $req = $bd->prepare($sql);
        $req->execute(array(
            'org_login' => $login,
            'org_passe' => $passe,
            'courriel' => $courriel,
            'destruction' => $date,
            'ID_administrateur' => $_SESSION["ID_administrateur"],
            'date_debut' => $today,
            'date_fin' => $today,
            'date_limite_preinscriptions' => $today,
        ));
    } catch (Exception $e) {
// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}

// Destruction de regate
if (isset($_POST['IDR'])) {

    $idregate = filter_input(INPUT_POST,'IDR',FILTER_VALIDATE_INT);
    $regate = Regate_selectById($idregate);

    // Ce test a déjà lieu dans JavaScript
    // Donc on a un doublon, et on ne peux pas en fait detruire 
    // une regate si elle n'est pas destructible
    if (Regate_estDestructible($regate) || numberOfSailors($idregate) == 0
    ) {
        detruireRegate($idregate);
    } else {
        $message = "Pour l'instant, il n'est pas possible detruire cette regate, "
                . "car  ou (1) bien la date de destruction de cette regate n'est pas passée."
                . "ou (2) bien la regate a déjà un inscrit";
        pageErreur($message);
        exit;
    }
}

// Création nouvelle régate
if (isset($_POST['org_login'])) {
    $login = filter_input(INPUT_POST, 'org_login');
    $passe = filter_input(INPUT_POST, 'org_passe');
    $dateDestruction = filter_input(INPUT_POST, 'date_destru');
    $courriel = filter_input(INPUT_POST, 'org_courriel',FILTER_VALIDATE_EMAIL);
    if ($login != '' &&
            $passe != '' &&
            $courriel != '' &&
            $dateDestruction != '' &&
            $courriel != FALSE
            ) {
        creerRegate($login, $passe, $courriel, $dateDestruction);
    }   else
    {
        $message = "Un des champs était vide ou le courriel n'était pas valide";
        pageErreur($message);
        exit;
    }
}

// Preparation des données pour l'affichage 
// Liste de toutes les regates
try {
    $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    $req = $bd->query('SELECT * FROM Regate ORDER BY `ID_regate` DESC');
} catch (Exception $e) {
// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}

// Get informations about dbf/COUREUR.DBF
$fileName = 'dbf/COUREUR.DBF';
if (file_exists($fileName)) {
    $cdbf_lastupdate = date("d/m/Y à H:i:s", filemtime($fileName));
} else {
    $cdbf_lastupdate = '**Ce fichier n\'existe encore pas**';
}
?>