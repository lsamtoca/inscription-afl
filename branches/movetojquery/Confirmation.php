<?php
require "partage.php";

try {
    // On se connecte à la BD
    $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);

    // On cherche l'inscrit
    $req = $bd->prepare('SELECT nom,prenom,ID_regate FROM Inscrit WHERE ID_inscrit = ?');
    $req->execute(array($_GET['ID']));
    $row = $req->fetch();
    $req->closeCursor();

    isset($row['prenom']) or
            die('ERREUR : nous n\'avons pas trouvé votre pré-inscription dans la base de données :-(');

    $sql = 'UPDATE Inscrit SET `conf`=?, `date confirmation`=? WHERE ID_inscrit =?';
    $req = $bd->prepare($sql);
    $req->execute(array(1, date('Y-m-d G:i:s'), $_GET['ID']));
    $req->closeCursor();
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}

$URLPRE = format_url_preinscrits($row['ID_regate']);
?>

<?php xhtml_pre('Confirmation'); ?>

<div>
    <p>
        Bonjour <?php echo $row['prenom'] . ' ' . $row['nom'] ?>,<br />
        votre inscription est maintenant confirmée !!!
    </p>

    <p>
        Vous pouvez vérifier votre inscription sur la
        <a href="<?php echo $URLPRE; ?>">
            <span id='liste_preinscrits'>liste des pré-inscrits</span>
        </a> à cette régate.
    </p>

</div>

<?php xhtml_post(); ?>