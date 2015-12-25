<?php

require_once 'php/Inscrit.php';

$ID_inscrit=$_GET['ID'];
$inscrit=Inscrit_selectById($ID_inscrit);
if (
        isset($inscrit['prenom']) // si nous avons trouvé le coureur
        and
        $inscrit['ID_regate'] == $_SESSION['ID_regate']   // nous en avons les droits
) {
    $sql1 = 'DELETE FROM Inscrit WHERE ID_inscrit =?';
    $assoc1 = array($_GET['ID']);
    $req1 = executePreparedQuery($sql1, $assoc1);
} else {
    $message = 'Ce coureur n\'a pas été trouvé dans cette régate';
    pageErreur($message);
    exit(0);
}
$message = 'La pré-inscription du coureur a été annullée';
pageAnswer($message);

//header("Location: Regate.php?item=inscrits");
