<?php // Affichage
xhtml_pre1('Gestion des Événements (et des Clubs)'); ?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>

<!--
<script type="text/javascript" src="classes/calendarDateInput.js">
    /***********************************************
     * Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
     * Script featured on and available at http://www.dynamicdrive.com
     * Keep this notice intact for use.
     ***********************************************/
</script>
-->

<?php xhtml_pre2('Gestion des Événements (et des Clubs)'); ?>

<div id='deconnexion'>[<a href='deconnexion.php'>Deconnexion</a>]</div>

<div id='nouvel_evenement'>
    <!--      <h1>Gestion des Événements (et des Clubs)</h1>-->
        
    <form action='' method='post'>
        
        <fieldset>
            
            <legend>Nouvel Événement</legend>
                
            <label for='org_login'>Login organisateur :</label>
            <input name='org_login' type='text' id='org_login' tabindex="2"/>
                
            <br />
                
            <label for='org_passe'>Mot de passe organisateur :</label>
            <input name='org_passe' type='text' id='org_passe' tabindex="2"/>
            <br />
            <label for='org_courriel'>Courriel organisateur :</label>
            <input name='org_courriel' type='text' id='org_courriel' tabindex="2"/>
                
            <br />
                
            <label for='date_destru'>Date de destruction :</label>
            <?php $in_one_year = date("Y-m-d", 31536000 + time()); ?>
            <script type="text/javascript">
                DateInput('date_destru', true,'YYYY-MM-DD','<?php echo $in_one_year ?>')
            </script>
                
            <br />
                
            <input type='submit' name='submit' id='submit' value='Valider'/>
                
            <br />
                
        </fieldset>
    </form>
</div>

<div>
    <h2>Liste des évenements ouverts</h2>
    <table>
        <tr>
            <th scope="col">Login organisateur</th>
            <th scope="col">Mot de passe organisateur</th>
            <th scope="col">Courriel organisateur</th>
            <th scope="col">Date destruction</th>
            <th scope="col">Créateur</th>
        </tr>
        <?php while ($donnees = $req->fetch()): ?>
        <tr>
            <td scope="col"><?php echo $donnees['org_login'] ?></td>
            <td scope="col"><?php echo $donnees['org_passe'] ?></td>
            <td scope="col"><?php echo $donnees['courriel'] ?></td>
            <td scope="col"><?php echo $donnees['destruction'] ?></td>
            <td scope="col"><?php echo "--"; ?></td>
            <td scope="col">
                <form action="" method="post">
                    <input type="submit" name="submit" value="X"/>
                    <input name="IDR" type="hidden" value='<?php echo $donnees['ID_regate'] ?>' />
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

<div id="admin_autres_options">
    <h4>Autres options</h4>
    <ol>
        <li>
            <a href="coureur_dbf_update.php" onclick="alert('Cette operation peut prendre du temps');">Mettre à jour le fichier COUREUR.DBF</a>
        </li>
    </ol>
</div>

    <?php 
$req->closeCursor();
xhtml_post(); 
?>