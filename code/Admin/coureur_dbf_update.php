<?php

session_start();
if (!isset($_SESSION['ID_administrateur'])) {
    header('Location: LoginAdmin.php');
}
require_once 'partage.php';


$fileName = 'dbf/COUREUR.DBF';
$systemcall="wget -O $fileName -q ftp://ftp.ffvoile.net/tmpDbf/coureur.dbf";
system($systemcall);


$handle = dbase_open($fileName, 0);

try {

//$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);

// Creation de la table MYSQL
    $columns = dbase_get_header_info($handle);
    $sql = "DROP TABLE IF EXISTS `COUREUR.DBF`";
    $req = $bdd->prepare($sql);
    $req->execute();

    $sql = "CREATE TABLE `COUREUR.DBF` (\n";
    foreach ($columns as $key => $column) {
        $cols_names[$key] = $column['name'];
        $sqls[$key] = $column['name'] . " VARCHAR(" . $column['length'] . ")";
    }
    $sql.=implode(",\n", $sqls);
    $sql.="\n);\n";
    $req = $bdd->prepare($sql);
    $req->execute();

// Copie du fichier dans la table
    $no_records = dbase_numrecords($handle);
    $columns = implode(',', $cols_names);
    $values = ":" . implode(',:', $cols_names);
    $sql = "insert into `COUREUR.DBF` ($columns) VALUES ($values)";
    $req = $bdd->prepare($sql);


    for ($i = 1; $i <= $no_records; $i++) {
        $row = dbase_get_record_with_names($handle, $i);
        unset($row['deleted']);
        $req->execute($row);
    }
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}
?>
<?php xhtml_pre('Mise à jour du fichier COUREUR.DBF'); ?>
<?php

$msg = "Le fichier COUREUR.DBF a été mis à jour";
redirect($msg, 3000, 'Admin.php');
?>
<?php xhtml_post(); ?>