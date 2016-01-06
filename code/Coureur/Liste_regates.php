<?php

function do_liste($req, $titre) {
//echo "<h2>$titre</h2>\n";


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

$bdd = newBd();

$condition = '`date_limite_preinscriptions` >= DATE(NOW())';
if (!$development and !$testing)
    $condition .= ' AND `istest`=0';

$sql = "SELECT `ID_regate`,`titre`,`lieu`, 
	`date_debut` as `date`,
	 DATE_FORMAT(`date_debut`, '%d-%m-%Y') as `date_debut`,
	 DATE_FORMAT(`date_fin`, '%d-%m-%Y') as `date_fin` FROM `Regate` 
	 WHERE $condition order by `date`";

$assoc = array();
$regates_avenir = executePreparedQuery($sql, $assoc, $bdd);


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

$regates_passees = executePreparedQuery($sql, $assoc, $bdd);
$title = "Régates ouvertes à l'inscription";
xhtml_pre1($title);
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="js/myaccordion.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function () {
        myaccordion_set_accordion();
        $('#accordion').accordion({
            active: 0
        });
    });

</script>


<?php
xhtml_pre2($title);
doMenu();

?>

<div id="accordion">

    <h3>Régates à venir</h3>
    <div class="contenu">
        <?php
        do_liste($regates_avenir, 'Régates à venir');
        ?>
    </div>

    <h3>Régates passées ou closes à la pré-inscription</h3>
    <div class="contenu">

        <?php
        do_liste($regates_passees, 'Régates passées ou closes à la pré-inscription');
        ?>

    </div>
</div>
<?php
xhtml_post();
