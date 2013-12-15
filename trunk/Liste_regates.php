<?php

require "partage.php";

function do_liste($req, $titre) {
    echo "<h2>$titre</h2>\n";


    echo '<ul>';
    if ($req->rowCount() == 0) {
        echo '<li>Aucune</li>';
    } else {

        while ($row = $req->fetch()) {

            echo "<li>";
            if ($row['date_debut'] != "00-00-0000" and $row['date_fin'] != "00-00-0000")
                printf("Du %s au %s : ", $row['date_debut'], $row['date_fin']);
            printf("<a href=\"%s\">%s</a>", format_url_regate($row['ID_regate']), $row['titre']);

            if ($row['lieu'] != "")
                printf(" à %s", $row['lieu']);
            echo ". ";
            printf("<a href=\"%s\">Liste des préinscrits</a>", format_url_preinscrits($row['ID_regate']));
            echo ".</li>\n";
        }
    }
    echo '</ul>';
}

try {
//    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);

    $condition = '`date_limite_preinscriptions` >= DATE(NOW())';
    if (!$development and !$testing)
        $condition .= ' AND `istest`=0';

    $sql = "SELECT `ID_regate`,`titre`,`lieu`, 
	`date_debut` as `date`,
	 DATE_FORMAT(`date_debut`, '%d-%m-%Y') as `date_debut`,
	 DATE_FORMAT(`date_fin`, '%d-%m-%Y') as `date_fin` FROM `Regate` 
	 WHERE $condition order by `date`";
    $regs_avenir = $bdd->query($sql);

    $condition = '`date_limite_preinscriptions` < DATE(NOW()) AND '
            . '`date_fin` > DATE(NOW() - INTERVAL 1 YEAR)';
    if (!$development and !$testing)
        $condition .= ' AND `istest`=0';

    $sql = 'SELECT `ID_regate`,`titre`,`lieu`,
     `date_debut` as `date`,
	 DATE_FORMAT(`date_debut`, \'%d-%m-%Y\') as `date_debut`,
	 DATE_FORMAT(`date_fin`, \'%d-%m-%Y\') as `date_fin` FROM `Regate`'
            . " WHERE $condition "
            . ' ORDER by `date`  DESC';

    $regs_passees = $bdd->query($sql);
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}
?>  



<?php

xhtml_pre("Régates ouvertes à l'inscription");

do_liste($regs_avenir, 'Régates à venir');
do_liste($regs_passees, 'Régates passées ou closes à la pré-inscription');

xhtml_post();
?>