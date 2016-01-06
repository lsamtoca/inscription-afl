<?php

// On cherche l'inscrit
$sql = 'SELECT nom, prenom FROM Inscrit WHERE ID_inscrit = ?';
$assoc = array($_GET['ID']);
$req = executePreparedQuery($sql, $assoc);

$donnees = $req->fetch();
$req->closeCursor();

if (!isset($donnees['prenom'])) {
    $message = "Nous n'avons pas trouvé votre pré-inscription dans la base de données";
    pageErreur($message);
    exit(0);
}

$sql1 = 'UPDATE Inscrit SET `conf`=?, `date confirmation`=? WHERE ID_inscrit =?';
$assoc1 = array(1, date('Y-m-d G:i:s'), $_GET['ID']);
$req = executePreparedQuery($sql1, $assoc1);

$nom = $donnees['nom'];
$prenom = $donnees['prenom'];
$messagep = "Bonjour $prenom $nom\nVotre inscription est maintenant confirmée.";
pageAnswer($messagep, NULL,'Confirmation');
