<?php xhtml_pre1('Pré-inscription à la régate'); ?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<style type="text/css">
    form label.error {
        margin-left: 10px;
        width: auto;
        display: inline;
        color:red;
    }
    
</style>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="js/jquery.i18n.properties.js" type="text/javascript"></script>
<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>

<script src="js/Formulaire-i18n.js" type="text/javascript"></script>
<script src="js/Formulaire-validation.js" type="text/javascript"></script>
<script src="js/Formulaire-dynamic.js" type="text/javascript"></script>

<script type="text/javascript">

    $(document).ready( function () {

        $("#naissance" ).datepicker({
            dateFormat : "dd/mm/yy",
            defaultDate : "01/01/98",
            changeYear : true,
            yearRange : "c-20:c+20"
        });
    
    });

</script>

<?php xhtml_pre2('Pré-inscription à la régate'); ?>

<div id='choix_langue' class="white_over_dark">
    [<a id='lang'></a>]
</div><!--choix langue-->


<div id='infos_regate'>

    <!--Dates, titre, description-->

    <p>
        <?php if ($regate['date_debut'] != "00-00-0000" and $regate['date_fin'] != "00-00-0000"): ?>
            Du <?php echo $regate['date_debut']; ?> au <?php echo $regate['date_fin']; ?> :
        <?php endif; ?>
        <b><?php echo $regate['titre']; ?></b>
        <?php if ($regate['lieu'] != ""): ?>
            à <?php echo $regate['lieu']; ?>
        <?php endif; ?>.
    </p>
    <p><?php echo $regate['description'] ?></p>

    <!--Lien sur la liste des préinscrits-->
    <p>
        <a href="<?php echo $URLPRE; ?>">
            <span id='liste_preinscrits'></span>
        </a>
    </p>

    <!--Date limite pré-inscription-->

    <?php if ($regate['date_limite_preinscriptions'] != ''): ?>
        <p>
            <span id="deadline"></span>
            <?php echo $limite->format('d-m-Y'); ?>
        </p>

        <?php if ($now > $limite): ?>
            <p>
                La date limite pour se préinscrire à cette régate,
                le <?php echo $limite->format('d-m-Y'); ?> est passée.
                <br />
                Il n'est plus possible se préinscrire à cette régate :-(
            </p>
            <?php xhtml_post();
            die('');
            ?>
        <?php endif; ?>
<?php endif; ?>

</div> <!--infos_regate-->



<div id='search'>

    <form name="searchform" id="searchform" action="" method="post">
            
        <fieldset>
            <legend id='search_legend'></legend>
            <label class="left" id='l_search_lic'></label>
            <input name="search_lic" id="search_lic" type="text"/>
            <input name="search_submit" type='submit' value="Chercher">
                
                
            <label class="left" id='l_search_isaf'></label>
            <input name="search_isaf"
                   id="search_isaf" type="text" />
                       
            <input name="search_submit" type='submit' value="Chercher">
                
                
        </fieldset>

    </form>
                        
</div><!-- recherche par licence ou numero ISAF -->

<br />


<div id='formulaire'>

    <form id="mainform" action="Inscription.php" method="post">
        <fieldset>
            <legend id='mainform_legend'></legend>

            <input name="lang" type="hidden" id="input_lang" value="fr" />
            <input name="IDR" type="hidden" id="IDR" value=<?php echo '"' . $_GET['regate'] . '"'; ?>/>

            <!-- Donnés personnels : nom prenom, date naissance, sexe -->
            <label class="left" for="Nom"id='l_Nom'></label>
            <input name="Nom" type="text" id="Nom" value="<?php echo $data['Nom']; ?>"/>

            <label class="left" for="Prenom" id='l_Prenom'></label>
            <input name="Prenom" type="text" id="Prenom" value="<?php echo $data['Prenom']; ?>"/>
            <br />


            <label class="left" id='l_naissance'></label>
            <input name="naissance" type="text" id="naissance" value="<?php echo $data['naissance']; ?>" />
	    <br />
            
	    <input type="radio" name="sexe" id="radio_F" 
                   value="F" <?php echo $data['F']; ?> />
            <label id='l_femme'></label>

            <input type="radio" name="sexe" id="radio_H" class="required"
                    value="M" <?php echo $data['M']; ?> />
            <label id='l_homme'></label>

            <hr />

            <!-- Contact -->

            <label class="left" id='l_mail'></label>
            <input type="text" name="mail" id="mail"  
                    value="<?php echo $data['mail']; ?>" />


            <hr />

            <!-- Club -->
            <label class="left" for="nom_club" id='l_nom_club'></label>
            <input name="nom_club" id="nom_club" type="text" 
                    value="<?php echo $data['nom_club']; ?>"/>

            <label class="left" for="num_club" id='l_num_club'></label>
            <input name="num_club" id="num_club" type="text" size="5"/>
            <hr />

            <!-- Serie -->

            <input type="radio" name="serie" id="radio_LA4" value="LA4" <?php echo $data['LA4']; ?> />
            <label for="radio_LA4">Laser 4.7</label>
            
            <input type="radio" name="serie" id="radio_LAR" value="LAR" <?php echo $data['LAR']; ?> />
            <label for="radio_LAR">Laser Radial</label>

            <input type="radio" name="serie" id="radio_LAS" class="required"
                   value="LAS" <?php echo $data['LAS']; ?>/>
            <label for="radio_LAS">Laser Standard</label>

            <br />

            <label class="left" for="Cvoile" id='l_Nvoile'></label>
            <input name="Cvoile" type="text" id="Cvoile" size="3" maxlength="3" value="<?php echo $data['Cvoile']; ?>"/>
            <input name="Nvoile" type="text" id="Nvoile" size="6" maxlength="6" value="<?php echo $data['Nvoile']; ?>"/>
            <hr />

            <!-- Statut : Licence et AFL -->

            <input type="radio" class="required" name="statut" id="radio_ffv" value="Licencie" <?php echo $data['Licencie']; ?> />
            <label id='l_ffv'></label>

            <input type="radio" name="statut" id="radio_etranger" value="Etranger" <?php echo $data['Etranger']; ?> />
            <label id='l_etranger'></label>

            <input type="radio" name="statut" id="radio_autre" value="Autre" <?php echo $data['Autre']; ?> />
            <label id='l_autre'></label>

            <br />

            <label class="left" id='l_afl'></label>
            <input type="radio" name="adherant" id="radio_adherant_oui" value="1" <?php echo $data['ad_AFL']; ?> />

            <label id='l_oui'></label>

            <input type="radio" name="adherant" id="radio_adherant_non" value="0" <?php echo $data['non_ad_AFL']; ?>/>
            <label id='l_non'></label>

            <br />

            <label class="left" for="lic" id='l_lic'></label>
            <input type="text" name="lic" id="lic" 
                   size="8" value="<?php echo $data['lic']; ?>"/>

            <label class="left" for="isaf_no" id="l_isaf_no"></label>
            <input type="text" name="isaf_no" id="isaf_no" 
                   size="10" value="<?php echo $data['isaf_no']; ?>"
                   />
            <br />

            <div id="licencie_ffv">
            </div>

            <div id="non_licencie">
            </div>

            <div id="etranger">
            </div>

            <hr />

            <input type="submit" name="maSoumission" id="soumission" value="Valider"/>
        </fieldset>
    </form>
</div> <!-- formulaire -->

<?php xhtml_post(); ?>
