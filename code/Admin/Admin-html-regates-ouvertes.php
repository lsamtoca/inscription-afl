<div class="contenu">
    <p>
    <table class="mytable">
        <tr>
            <th scope="col">Numéro</th>
            <th scope="col">Titre événement</th>
            <!--<th scope="col">Login organisateur</th> -->
             <!-- <th scope="col">Mot de passe organisateur</th>-->
            <th scope="col">Courriel organisateur</th>
            <th scope="col">Date destruction</th>
            <th scope="col">Créateur</th>
            <th scope="col">Destruction</th>
            <th scope="col">Administrer la régate</th>
        </tr>
        <?php
        while ($regate = $req->fetch()):
            $url_preinscriptions = format_url_regate($regate['ID_regate']);
            $url_preinscrits = format_url_preinscrits($regate['ID_regate']);
            $creator = Administrateur_selectByID($regate['ID_administrateur']);
            $createur = $creator['Prenom'] . ' ' . $creator['Nom'];
            $destruction = dateReformatMysqlToJquery($regate['destruction']);
            ?>
            <tr>
                <td scope="col" style="text-align:right">
                    <a href="<?php echo $url_preinscriptions; ?>">
                        <?php echo $regate['ID_regate'] ?></a></td>
                <td scope="col"><?php echo $regate['titre'] ?></td>
                <!--<td scope="col"><?php echo $regate['org_login'] ?></td>-->
                <!--<td scope="col"><?php echo $regate['org_passe'] ?></td>-->
                <td scope="col"><?php echo $regate['courriel'] ?></td>
                <td scope="col"><?php echo $destruction ?></td>
                <td scope="col"><?php echo $createur; ?></td>
                
                <td scope="col">
                    <form action="<?php echo urlSelf(); ?>#regatesouvertes" method="post"  onSubmit="return validate_date_destr('<?php echo $destruction ?>')">
                        <input type="submit" name="detruire" value="X" />
                        <input name="IDR" type="hidden" value='<?php echo $regate['ID_regate'] ?>' />
                    </form>
                </td>
                
                <td scope="col">
                    <form action="" method="post" >
                        <input type="submit" name="loginAsClub" value='Administrer'/>
                        <input name="IDR" type="hidden" value='<?php echo $regate['ID_regate'] ?>' />
                    </form>
                </td>

            </tr>
        <?php endwhile; ?>
    </table>
</p>
</div>
