<?php 

session_start();
if(!isset($_SESSION['ID_administrateur'])) {
    header('Location: LoginAdmin.php');
}

// Destruction de regate
if(isset($_POST['IDR'])) {
    try {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);

        // We delete a race only if there are no preregistered sailoirs
        $sql = 'SELECT COUNT(*) as `num` FROM Inscrit WHERE ID_regate= :IDR';
        $req = $bdd->prepare($sql);
        $req->execute(array('IDR' => $_POST['IDR']));
        $row=$req->fetch();
        if($row['num'] == 0) {
            $sql = 'DELETE FROM Regate WHERE ID_regate= :IDR';
            $req = $bdd->prepare($sql);
            $req->execute(array('IDR' => $_POST['IDR']));
        }

        // We do not want to delete records of a race ....
        // ... instead -- maybe -- move to another table !!!
        // First, delete all coureurs whose ID is $_POST['IDR']
// 			$sql = 'DELETE FROM Inscrit WHERE ID_regate= :IDR';
// 			$req = $bdd->prepare($sql);
// 			$req->execute(array('IDR' => $_POST['IDR']));
        // Second, delete the regata
        /*			$sql = 'DELETE FROM Regate WHERE ID_regate= :IDR';
			$req = $bdd->prepare($sql);
			$req->execute(array('IDR' => $_POST['IDR']));*/
    }
    catch(Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
    }
}

// Création nouvelle régate
if(isset($_POST['org_login']) && $_POST['org_login']!=''
        && $_POST['org_passe']!=''
        && $_POST['org_courriel'] !=''
        //&& $_POST['jour_destru']!='JJ' && $_POST['mois_destru']!='MM' && $_POST['anne_destru']!='AAAA'
        && $_POST['date_destru'] !=''
) {
    try {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
//			$date=$_POST['anne_destru']."-".$_POST['mois_destru']."-".$_POST['jour_destru'];
        $date=$_POST['date_destru'];
        $today=date('Y-m-d');
        $sql = 'INSERT INTO Regate (org_login, org_passe,courriel,destruction,ID_administrateur,date_debut,date_fin,date_limite_preinscriptions) VALUES(:org_login,:org_passe,:courriel,:destruction,:ID_administrateur,
			:date_debut,:date_fin,:date_limite_preinscriptions)';
        $req = $bdd->prepare($sql);
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
    }
    catch(Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
    }
}

// Preparation des données pour l'affichage
try {
// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    $req= $bdd->query('SELECT * FROM Regate');
}
catch(Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>