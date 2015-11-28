<?php
require_once 'php/Administrateur.php';
date_default_timezone_set('Europe/Paris');
$in_one_year = date("d/m/Y", 31536000 + time());

// Affichage
xhtml_pre1('Administration des régates (événements et clubs)');
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>
<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>

<script src="js/myaccordion.js" type="text/javascript"></script>


<script type="text/javascript">

    $(document).ready(function () {

        $("#date_destru").datepicker({
            dateFormat: "dd/mm/yy",
            defaultDate: "<?php echo $in_one_year; ?>",
            changeYear: true,
            yearRange: "c-20:c+20"

        });

        var req_msg = "Champ obligatoire";
        var ml_msg = "Au moins 8 caractères";
        var Ml_msg = "Au plus 10 caractères";
        var em_msg = "SVP, un adresse courriel valide";

        $("#formnewrace").validate({
            rules: {
                org_login: {
                    required: true,
                    minlength: 8,
                    maxlength: 10
                },
                org_passe: {
                    required: true,
                    minlength: 8,
                    maxlength: 10
                },
                org_courriel: {
                    required: true,
                    email: true
                },
                date_destru: {
                    required: true
                }
            },
            messages: {
                org_login: {
                    required: req_msg,
                    minlength: ml_msg,
                    maxlength: Ml_msg
                },
                org_passe: {
                    required: req_msg,
                    minlength: ml_msg,
                    maxlength: Ml_msg
                },
                org_courriel: {
                    required: req_msg,
                    email: em_msg
                },
                date_destru: {
                    required: req_msg
                }
            }

        });

        myaccordion_set_accordion();

    });

    function validate_date_destr(date_d) {

        var ar = date_d.split('/');
        var day = ar[0];
        var month = ar[1];
        var year = ar[2];
        var date_destr = new Date();
        date_destr.setFullYear(year, month, day);
        var now = new Date();
        //       console.log(now.toString());
        //       console.log(date_destr.toString());

        if (now < date_destr) {
            return confirm("La date de destruction de cette regate, le "
                    + date_d
                    + ", n'est pas passée. Etes vous surs ?");
        }
        return true;

    }

</script>

<?php xhtml_pre2('Administration des régates (événements et clubs)'); ?>

<div id='deconnexion'>[<a href='deconnexion.php'>Déconnexion</a>]</div>


<div id='accordion'>
    <!--      <h1>Gestion des Événements (et des Clubs)</h1>-->

    <h3>Nouvelle régate</h3>
    <div class="contenu">
        <form action='' method='post' id="formnewrace">

            <fieldset>

                <legend>Nouvelle régate</legend>

                <label for='org_login'>Login organisateur :</label>
                <input name='org_login' type='text' id='org_login' tabindex="2" class="required"/>

                <br />

                <label for='org_passe'>Mot de passe organisateur :</label>
                <input name='org_passe' type='text' id='org_passe' tabindex="2" class="required"/>
                <br />
                <label for='org_courriel'>Courriel organisateur :</label>
                <input name='org_courriel' type='text' id='org_courriel' tabindex="2" class="required"/>

                <br />

                <label for='date_destru'>Date de destruction :</label>
                <input name="date_destru" type="text" id="date_destru" 
                       value="<?php echo $in_one_year; ?>" class="required"/>
                <br />

                <input type='submit' name='submit' id='submit' value='Valider'/>

                <br />

            </fieldset>
        </form>
    </div>

    <h3>Liste des régates ouvertes</h3>
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
                        <form action="" method="post"  onSubmit="return validate_date_destr('<?php echo $destruction ?>')">
                            <input type="submit" name="submit" value="X" />
                            <input name="IDR" type="hidden" value='<?php echo $regate['ID_regate'] ?>' />
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        </p>
    </div>
    <h3>Mettre à jour le fichier COUREUR.DBF</h3>
    <div class="contenu">
        <p>
            La dernière modification du fichier COUREUR.DBF remonte au <?php echo $cdbf_lastupdate; ?>.
            <br />
            <a href="coureur_dbf_update.php" onclick="alert('Cette operation peut prendre du temps');">Mettre à jour le fichier COUREUR.DBF</a>
        </p>
    </div>
</div><!-- accordion -->

<?php
$req->closeCursor();
xhtml_post();
?>