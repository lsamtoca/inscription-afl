<div class="contenu">
    <!--
    <link href="js/tables/Fixed-Header-Table/css/defaultTheme.css" rel="stylesheet" media="screen"/>
    <script src="js/tables/Fixed-Header-Table/jquery.fixedheadertable.js" type="text/javascript"></script>
    <script src="js/jquery.freezeheader.js"></script>
    -->
    <script src="js/tableHeadFixer.js"></script>
    <style>
        #parent {
            height: 500px;
        }

        #fixTable {
            width: 90% !important;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#tableInscrits").tableHeadFixer({"head": true, "left": 2});
//            $("#tableInscrits").freezeHeader({ 'height': '300px' });
//            $('#tableInscrits').fixedHeaderTable(
//                    {autoshow: true, 
//                footer: false, cloneHeadToFoot: false,
//                        fixedColumn: false});
//            $('#tableInscrits').fixedHeaderTable({height: 500});
//            $('#tableInscrits').fixedHeaderTable({themeClass: 'defaultTheme'});
//            $('#tableInscrits').fixedHeaderTable({width:500});
            //$('#tableInscrits').fixedHeaderTable('show');
        });
    </script>
    <?php
    $sql = 'SELECT * FROM Inscrit WHERE (ID_regate =?) ORDER BY `Nom`';
    $assoc = array($_SESSION["ID_regate"]);
    $req = executePreparedQuery($sql, $assoc);
    ?>
    <div id="parent">
        <table id="tableInscrits" class="mytable">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Sexe</th>
                    <th>Taille polo</th>
                    <th>Numéros de voile</th>
                    <th>Série</th>
                    <th>Licence</th>
                    <th>Numéros de licence</th>
                    <th>Adhérent AFL</th>
                    <th>Confirmation</th>
                    <th>Courriel</th>
                    <th>Confirmer</th>
                    <th>Annuler</th>
                </tr>
            </thead>
            <tfoot></tfoot>
            <tbody>
                <?php
                while ($donnees = $req->fetch()) :
                    if ($donnees['sexe'] == 'M') {
                        $sexe = 'Homme';
                    } else {
                        $sexe = 'Femme';
                    }
                    if ($donnees['adherant'] == 1) {
                        $adherant = 'oui';
                    } else {
                        $adherant = 'non';
                    }
                    if ($donnees['conf'] == 1) {
                        $conf = 'oui';
                    } else {
                        $conf = 'non';
                    }
                    $serie = $availableSeries[$donnees['serie']]['nomLong'];
                    //
                    if ($donnees['statut'] == 'Licencie') {
                        $statut = 'Licencié FFV';
                    } elseif ($donnees['statut'] == 'Etranger') {
                        $statut = 'Coureur étranger';
                    } else {
                        $statut = 'Pas encore licencié';
                    }
                    ?>

                    <tr>
                        <td><?php echo $donnees['nom']; ?></td>
                        <td><?php echo $donnees['prenom']; ?></td>
                        <td><?php echo $sexe; ?></td>
                        <td><?php echo $donnees['taille_polo']; ?></td>
                        <td><?php echo $donnees['prefix_voile'] . $donnees['num_voile']; ?> </td>                
                        <td><?php echo $serie; ?></td>
                        <td><?php echo $statut; ?></td>
                        <td><?php echo $donnees['num_lic']; ?></td>
                        <td><?php echo $adherant; ?></td>
                        <td><?php echo $conf; ?></td>
                        <td><?php echo $donnees['mail']; ?></td>
                        <td><a href="Confirmation.php?ID=<?php echo $donnees['ID_inscrit']; ?>">Confirmer</a></td>
                        <td><a href="Annulation.php?ID=<?php echo $donnees['ID_inscrit']; ?>">Annuler</a></td>
                    </tr>

                <?php endwhile; ?>
            </tbody>

        </table>
    </div>
</div> 
