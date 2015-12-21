<div class="contenu">
    <?php
    $sql = 'SELECT * FROM Inscrit WHERE (ID_regate =?)';
    $assoc = array($_SESSION["ID_regate"]);
    $req = executePreparedQuery($sql, $assoc);
    ?>
    <table class="mytable">
        <tr class="mytable">
            <th scope="col">Prénom</th>
            <th  class="mytable" scope="col">Nom</th>
            <th  class="mytable" scope="col">Sexe</th>
            <th  class="mytable" scope="col">Taille polo</th>
            <th  class="mytable" scope="col">Numéros de voile</th>
            <th  class="mytable" scope="col">Série</th>
            <th  class="mytable" scope="col">Licence</th>
            <th  class="mytable" scope="col">Numéros de licence</th>
            <th  class="mytable" scope="col">Adherant AFL</th>
            <th  class="mytable" scope="col">Confirmation</th>
            <th  class="mytable" scope="col">Courriel</tr>

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
            if ($donnees['serie'] == 'LAS') {
                $serie = 'Laser Standard';
            } elseif ($donnees['serie'] == 'LAR') {
                $serie = 'Laser Radial';
            } else {
                $serie = 'Laser 4.7';
            }
            if ($donnees['statut'] == 'Licencie') {
                $statut = 'Licencié FFV';
            } elseif ($donnees['statut'] == 'Etranger') {
                $statut = 'Coureur étranger';
            } else {
                $statut = 'Pas encore licencié';
            }
            ?>

            <tr>
                <td class="mytable" scope="col"><?php echo $donnees['prenom']; ?></td>
                <td class="mytable" scope="col"><?php echo $donnees['nom']; ?></td>
                <td class="mytable" scope="col"><?php echo $sexe; ?></td>
                <td class="mytable" scope="col"><?php echo $donnees['taille_polo']; ?></td>
                <td class="mytable" scope="col"><?php echo $donnees['prefix_voile'] . $donnees['num_voile']; ?> </td>                
                <td class="mytable" scope="col"><?php echo $serie; ?></td>
                <td class="mytable" scope="col"><?php echo $statut; ?></td>
                <td class="mytable" scope="col"><?php echo $donnees['num_lic']; ?></td>
                <td class="mytable" scope="col"><?php echo $adherant; ?></td>
                <td class="mytable" scope="col"><?php echo $conf; ?></td>
                <td class="mytable" scope="col"><?php echo $donnees['mail']; ?></td>
                <td class="mytable" scope="col"><a href="Confirmation.php?ID=<?php echo $donnees['ID_inscrit']; ?>">Confirmer</a></td>
                <td class="mytable" scope="col"><a href="Annulation.php?ID=<?php echo $donnees['ID_inscrit']; ?>">Annuler</a></td>
            </tr>

            <?php
        endwhile;
        $req->closeCursor();
        ?>

    </table>

</div> 
