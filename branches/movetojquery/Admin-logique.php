<?php

require_once 'php/Regate.php';

// Destruction de regate
if (isset($_POST['IDR'])) {

//    $regate = Regate_selectById($_POST['IDR']);
//    if (!Regate_estDestructible($regate)) {
//        $message = "Pour l'instant, il n'est pas possible detruire cette regate, "
//                . "car la date de destruction de cette regate n'est pas passée";
//        pageErreur($message);
//        exit ;
//    }

    try {
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);

        // We delete a race only if there are no preregistered sailoirs
        $sql = 'SELECT COUNT(*) as `num` FROM Inscrit WHERE ID_regate= :IDR';
        $req = $bd->prepare($sql);
        $req->execute(array('IDR' => $_POST['IDR']));
        $row = $req->fetch();
        if ($row['num'] == 0) {
            $sql = 'DELETE FROM Regate WHERE ID_regate= :IDR';
            $req = $bd->prepare($sql);
            $req->execute(array('IDR' => $_POST['IDR']));
        } else {
            $message = "Pour l'instant, ce n'est pas possible detruire cette regate, "
                    . " car la regate compte plus qu'un inscrit";
            pageErreur($message);
            exit;
        }

        // We do not want to delete records of a race ....
        // ... instead -- maybe -- move to another table !!!
        // First, delete all coureurs whose ID is $_POST['IDR']
// 			$sql = 'DELETE FROM Inscrit WHERE ID_regate= :IDR';
// 			$req = $bd->prepare($sql);
// 			$req->execute(array('IDR' => $_POST['IDR']));
        // Second, delete the regata
        /* 			$sql = 'DELETE FROM Regate WHERE ID_regate= :IDR';
          $req = $bd->prepare($sql);
          $req->execute(array('IDR' => $_POST['IDR'])); */
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}

// Création nouvelle régate
if (isset($_POST['org_login']) && $_POST['org_login'] != ''
        && $_POST['org_passe'] != ''
        && $_POST['org_courriel'] != ''
        && $_POST['date_destru'] != ''
) {
    try {
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
//			$date=$_POST['anne_destru']."-".$_POST['mois_destru']."-".$_POST['jour_destru'];
        $date = dateReformatJqueryToMysql($_POST['date_destru']);
        $today = date('Y-m-d');
        $sql = 'INSERT INTO Regate (org_login, org_passe,courriel,destruction,ID_administrateur,date_debut,date_fin,date_limite_preinscriptions) VALUES(:org_login,:org_passe,:courriel,:destruction,:ID_administrateur,
			:date_debut,:date_fin,:date_limite_preinscriptions)';
        $req = $bd->prepare($sql);
        $req->execute(array(
            'org_login' => $_POST['org_login'],
            'org_passe' => $_POST['org_passe'],
            'courriel' => $_POST['org_courriel'],
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

// Preparation des données pour l'affichage
try {
// On se connecte à MySQL
//    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    $req = $bd->query('SELECT * FROM Regate ORDER BY `ID_regate` DESC');
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}
?>