<?php

require "partage.php";

try {
    // On se connecte à la BD
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);

    // On cherche l'inscrit
    $req = $bdd->prepare('SELECT nom, prenom FROM Inscrit WHERE ID_inscrit = ?');
    $req->execute(array($_GET['ID']));
    $donnees = $req->fetch();
    $req->closeCursor();

    isset($donnees['prenom']) or
            die('ERREUR : nous n\'avons pas trouvé votre pré-inscription dans la base de données :-(');

    $sql = 'UPDATE Inscrit SET `conf`=?, `date confirmation`=? WHERE ID_inscrit =?';
    $req = $bdd->prepare($sql);
    $req->execute(array(1,date('Y-m-d G:i:s'),$_GET['ID']));
    $req->closeCursor();
} catch(Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

?>

<?php xhtml_pre('Confirmation'); ?>

<div>

    Bonjour <?php echo $donnees['prenom'].' '.$donnees['nom'] ?>,<br />
    votre inscription est maintenant confirmée !!!

</div>

<?php xhtml_post(); ?>