<?php
// Affichage
require_once "partage.php";
xhtml_pre1('Gestion de votre régate');

global $TITRE_REGATE, $DESC_REGATE,
 $LIEU, $CV_ORGANISATEUR,
 $DATE_DEBUT_REGATE, $DATE_FIN_REGATE, $DATE_LIMITE_PREINSCRIPTIONS,
 $COURRIEL;

// global $DROITS;

global $pdo_path, $user, $pwd, $pdo_options;

global $mails_all, $mails_confirme, $mails_pas_confirme;
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>
<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>

<script src="js/myaccordion.js" type="text/javascript"></script>


<script type="text/javascript">

    $(document).ready( function () {
        

        $("#date_debut" ).datepicker({
            dateFormat : "dd/mm/yy",
            defaultDate : "<?php echo $DATE_DEBUT_REGATE; ?>",
            changeYear : true,
            yearRange : "c-2:c+2"
        });

        $("#date_fin" ).datepicker({
            dateFormat : "dd/mm/yy",
            defaultDate : "<?php echo $DATE_FIN_REGATE; ?>",
            changeYear : true,
            yearRange : "c-2:c+2"
        });

        $("#date_limite_preinscriptions" ).datepicker({
            dateFormat : "dd/mm/yy",
            defaultDate : "<?php echo $DATE_LIMITE_PREINSCRIPTIONS; ?>",
            changeYear : true,
            yearRange : "c-2:c+2"
        });

        
        var req_msg="Champ obligatoire";
        var em_msg="Entrez un adresse courriel valide";
        
       
        $("#form_info_regate").validate({
         
            rules : {
                titre : {
                    required:true
                },
                lieu : {
                    required:true
                },
                courriel: {
                    required:true,
                    email:true
                },
                date_debut:{
                    required:true
                },
                date_fin:{
                    required:true
                },
                date_limite_preinscriptions :{
                    required:true
                }
            },
         
            messages : {
                
                titre : {
                    required:req_msg
                },
                lieu : {
                    required:req_msg
                },
                courriel: {
                    required:req_msg,
                    email:em_msg
                },
                date_debut:{
                    required:req_msg
                },
                date_fin:{
                    required:req_msg
                },
                date_limite_preinscriptions :{
                    required:req_msg
                }
            }
        
        });
       
        myaccordion_set_accordion();
      
         
    });
   
   
</script>

<?php xhtml_pre2('Gestion de votre régate'); ?>


<div id='deconnexion'>[<a href='deconnexion.php'>Déconnexion</a>]</div>


<div id='accordion'>


    <h3>Notice d'utilisation du logiciel</h3>
    <div class="contenu">
        Téléchargez la <a href="docs/Notice_07-03-12.pdf">notice</a> d'utilisation (mise à jour le 7/03/12). 
    </div>

    <h3 id="renseignements">Formulaire renseignements sur la régate</h3>
    <div class="contenu">

        <form id='form_info_regate' action='#renseignements' method='post'>
            <fieldset>

                <label>Titre :</label>
                <textarea id='titre' name='titre' cols='50' rows='1'><?php echo $TITRE_REGATE; ?></textarea>

                <br />
                <label>Description :</label>
                <textarea id='description' name='description' cols='50' rows='10'><?php echo $DESC_REGATE ?></textarea>

                <hr />

                <label>Lieu :</label>
                <textarea id='lieu' name='lieu' cols='50' rows='1'><?php echo $LIEU; ?></textarea>

                <br />

                <label>Club organisateur :</label>
                <textarea id='cv_organisateur' name='cv_organisateur' cols='50' rows='1'><?php echo $CV_ORGANISATEUR; ?></textarea>

                <br />

                <label>Courriel du club :</label>
                <textarea id='courriel' name='courriel' cols='50' rows='1'><?php echo $COURRIEL; ?></textarea>

                <hr />
                <label>Date début :</label>
                <input name="date_debut" type="text" id="date_debut" 
                       value="<?php echo $DATE_DEBUT_REGATE; ?>" class="required"/>

                <label>Date fin :</label>
                <input name="date_fin" type="text" id="date_fin" 
                       value="<?php echo $DATE_FIN_REGATE; ?>" class="required"/>

                <br />
                <label>Date limite pour se pré-inscrire sur le web :</label>
                <input name="date_limite_preinscriptions" type="text" id="date_limite_preinscriptions" 
                       value="<?php echo $DATE_LIMITE_PREINSCRIPTIONS; ?>" class="required"/>

                <!-- A faire ?
                    <label>Droits d'inscription :</label>
                -->

                <hr />

                <br>

                <input type="submit" id="Modifier" value="Modifier" />

            </fieldset>
        </form>
    </div>

    <h3>Adresses formulaire inscription et liste des préinscrits</h3>
    <div class="contenu">


        URL d'inscription : <br />
        <a href='<?php echo $URL . "#formulaires"; ?>'><?php echo $URL . "#formulaires"; ?></a>

        <br />

        URL pour consulter la liste des préinscrits (ayant confirmé) : <br />
        <a href='<?php echo $URLPRE; ?>'><?php echo $URLPRE ?> </a>

    </div>


    <h3>Liste des inscrits</h3>
    <div class="contenu">
        <?php
        try {
            $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
            $req = $bdd->prepare('SELECT * FROM Inscrit WHERE (ID_regate =?)');
            $req->execute(array($_SESSION["ID_regate"]));
        } catch (Exception $e) {
            // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : ' . $e->getMessage());
        }
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

    <h3>Exportation des données et intégration avec FREG</h3>
    <div class="contenu">
        <h3>Exportation des données</h3>

        <ul>
            <li>
                Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_xls.php?confirme=1">format xls</a> (pour Excel, OpenOffice).
            </li>
            <li>
                Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_xls.php">format xls</a>  (pour Excel, OpenOffice).
            </li>
        </ul>

        <h3>Intégration avec le logiciel FREG</h3>

        <h5>Nouvelle méthode d'importation dans FREG (depuis 2014) :</h5>
        <ul>
            <li>
                Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_csv.php?confirme=1">format csv</a> ;
            </li>
            <li>
                Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_csv.php">format csv</a>.
            </li>
        </ul>
        Importez ce fichiers dans FREG via <br/>
        Inscrits->Fiches d'inscription à la régate->31. Solitaires->Importer Format CSV 2014
        
        <br />
        <br />
        On peut lire ces fichiers aussi avec Excel, OpenOffice, ou un editeur de texte.

        <!--
        <h5>Ancienne méthode d'importation :</h5>
        <ul>
            <li>
                Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_dbf.php?confirme=1">format dbf</a> ;
            </li>
            <li>
                Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_dbf.php">format dbf</a>.
            </li>
        </ul>
        Pour importer le fichier ins_dbf.dbf vers votre régate dans FREG, vous devez disposer du module FF_PRE_INS.EXE, à demander par courriel au support de FREG.

        <br/>
        On peut lire ces fichiers aussi avec Excel, OpenOffice, XBase.
        -->

    </div>

    <h3 id="courriel">Envoyer un courriel aux coureurs</h3>
    <div class="contenu">

        <?php
        // Oblige to fill the information about email
        if (!filter_var($_SESSION['courriel'], FILTER_VALIDATE_EMAIL)) :
            ?>

            L'adresse '<?php echo $_SESSION['courriel']; ?>' n'est pas un adresse email valide.<br />
            Veuillez compléter le champ Courriel dans le 
            <a href="?item=renseignements">formulaire renseignements sur la régate</a>.<br />'
            Ensuite, déconnectez vous et reconnectez vous une autre fois.

        <?php else : ?>

            <script type='text/javascript'>
                function set_tous(){
                    document.getElementById('to').value="<?php echo $mails_all; ?>";
                }
                function set_confirmes(){
                    document.getElementById('to').value="<?php echo $mails_confirme; ?>";
                }
                function set_pas_confirmes(){
                    document.getElementById('to').value="<?php echo $mails_pas_confirme; ?>";
                }  
            </script>

            <form action='#courriel' method='POST' enctype='multipart/form-data'>
                <fieldset>
                    Envoyer un courriel à :
                    <input type='radio' name='aqui' checked value='tous' onClick='set_tous()'><label>tous les préinscrits</label>
                    <input type='radio' name='aqui' value='confirmes' onClick='set_confirmes()'><label>les préinscrits ayant confirmé</label>
                    <input type='radio' name='aqui' value='pas_confirmes' onClick='set_pas_confirmes()'><label>ceux qui n'ont pas encore confirmé</label>
                    <hr />
                    <label>To : </label>
                    <br />
                    <input type='text' name='to' id='to' style='width:100%;' readonly value='<?php echo $mails_all; ?>' />
                    <br />
                    <label>CC : </label>
                    <br />
                    <input type='text' name='cc' style='width:100%;' />
                    <br />
                    <label>Objet : </label>
                    <br />
                    <input type='text' name='objet' style='width:100%;' /> 
                    <hr />
                    <label>Votre message : </label>
                    <br />
                    <textarea name='message' rows='20' style='width:100%;'></textarea>  
                    <hr />
                    <!--<input type='hidden' name='MAX_FILE_SIZE' value='12345' />-->
                    <label>Fichier à joindre : </label>    <br />
                    <input type='file' name='attachment' />
                    <hr />
                    <input type='submit' name='envoyer_mail' value='Envoyer' />
                </fieldset>
            </form>

        <?php endif; ?>

    </div>

    <h3>Fiches d'enregistrement des participants</h3>
    <div class="contenu">
        Imprimer les fiches d'enregistrement des participants :
        <ol> 
            <li><a href="accueil_participants?serie=LA4">Laser 4.7</a></li>
            <li><a href="accueil_participants?serie=LAR">Laser radial</a></li>
            <li><a href="accueil_participants?serie=LAS">Laser standard</a></li>
        </ol>
    </div>

</div>


<?php xhtml_post(); ?>