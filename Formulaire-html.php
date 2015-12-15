<?php
global $regate, $confirmation;


$page_title1 = 'Pré-inscription à la régate ' . $regate['titre'];
$page_title2 = 'Pré-inscription à la régate <em>' . $regate['titre'] . '</em>';
xhtml_pre1($page_title1);
?>

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

<script src="js/myaccordion.js" type="text/javascript"></script>


<script type="text/javascript">

    $(document).ready(function () {

        $("#naissance").datepicker({
            dateFormat: "dd/mm/yy",
            defaultDate: "01/01/94",
            changeYear: true,
            yearRange: "c-20:c+20"
        });

        myaccordion_set_accordion();

        $('mainform').submit(function () {
            alert($(this).serialize());
            return false;
        });
    });

</script>

<?php xhtml_pre2($page_title2); ?>

<div id='choix_langue' class="white_over_dark">
    [<a id='lang'></a>]
</div><!--choix langue-->

<div id="accordion">

    <h3 id="infos"><?php echo $regate['titre']; ?>, infos</h3>

    <div id='infos_regate' class="contenu">

        <!--Dates, titre, description-->

        <p>
            <?php if ($regate['date_debut'] != "00-00-0000" and $regate['date_fin'] != "00-00-0000"): ?>
                Du <?php echo Regate_formatDebut($regate); ?> au <?php echo Regate_formatFin($regate); ?> :
            <?php endif; ?>
            <b><?php echo $regate['titre']; ?></b>
            <?php if ($regate['lieu'] != ""): ?>
                à <?php echo $regate['lieu']; ?>
            <?php endif; ?>.
        </p>
        <p><?php echo $regate['description'] ?></p>

        <!--Lien sur la liste des préinscrits-->
        <!--
        <p>
            <a href="<\\?php echo $URLPRE; ?>">
                <span id='liste_preinscrits'></span>
            </a>
        </p>
        -->

        <!--Date limite pré-inscription-->

        <?php if ($regate['date_limite_preinscriptions'] != ''): ?>
            <p>
                <span id="deadline"></span>
                <?php echo Regate_formatDeadline($regate); ?>
            </p>

            <?php if (!Regate_estOuverte($regate)): ?>
                <p>
                    La date limite pour se pré-inscrire à cette régate,
                    le <?php echo Regate_formatDeadline($regate); ?> est passée.
                    <br />
                    Il n'est plus possible se pré-inscrire à cette régate :-(
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($regate['droits'] != '0'): ?>
            <p>Droits d'inscription : <?php echo $regate['droits']; ?>
                &#8364;</p>
        <?php endif; ?>
        <?php if ($regate['informations'] != ''): ?>
            <p>
                Autres informations : <?php echo $regate['informations']; ?>
            </p>
        <?php endif; ?>
    </div> <!--infos_regate-->

    <?php if (Regate_estOuverte($regate)): ?>
        <h3 id="formulaires"><span id="preregistration_form"></span></h3>
        <div id="forms" class="contenu">
            <?php
            if (!$confirmation):
                ?> 

                <div id='search'>

                    <form name="searchform" id="searchform" action="" method="post">

                        <fieldset>
                            <legend >
                                <span id='search_legend'></span>
                                <span class='help'>
                                    <span id="searchform_help"></span>
                                </span>
                            </legend>



                            <input name="lang" type="hidden" id="search_input_lang" value="fr"/>
                            <label class="left" id='l_search_lic'></label>
                            <input name="search_lic" id="search_lic" type="text"/>
                  <!--          <input name="search_submit" type='submit' value="Chercher">
                            -->          


                            <label class="left" id='l_search_isaf'></label>
                            <input name="search_isaf"
                                   id="search_isaf" type="text" />

                            <input name="search_submit" type='submit' value="Chercher">


                            <div id="searchform_caveat" style="padding:5px" class="caveat"></div>


                        </fieldset>

                    </form>

                </div><!-- recherche par licence ou numero ISAF -->

                <br />
                <?php
            else:
                ?> 
                <p style="color:red">
                    <span id="message_confirmation"></span>
                </p>
            <?php
            endif;
            ?> 

            <div id='formulaire'>

                <form id="mainform" action="Inscription.php" method="post">
                    <fieldset>
                        <legend id='mainform_legend'></legend>

                        <!-- Hidden inputs to handle control -->
                        <?php put_element('lang'); ?> 
                        <?php put_element('IDR'); ?> 
                        <?php put_element('conf'); ?> 
                        <?php put_element('ID_inscrit'); ?> 

                        <!-- Donnés personnels : nom prenom, date naissance, sexe -->

                        <?php put_element('Nom'); ?> 
                        <?php put_element('Prenom'); ?> 

                        <br />

                        <?php put_element('naissance'); ?> 


                        <br />

                        <?php put_element('sexe'); ?> 

                        <hr />

                        <!-- Taille Polo -->


                        <?php
                        put_element('taillepolo');
                        if ($regate['polo'] == '1')
                            echo "<hr />";
                        ?> 

                        <!-- Contact -->
                        <?php put_element('mail'); ?>

                        <hr />

                        <!-- Club -->
                        <?php put_element('nom_club'); ?>

                        <?php put_element('num_club'); ?>

                        <hr />



                        <!-- No-voile -->
                        <label class='left' id='l_Nvoile'></label>
                        <?php put_element('Cvoile'); ?>
                        <?php put_element('Nvoile'); ?>
                        <br />

                        <!-- Serie -->
                        <?php put_element('serie'); ?>
                        <hr />

                        <!-- Statut : Licence et AFL -->

                        <?php put_element('statut'); ?>

                        <br />

                        <?php put_element('adherant'); ?>


                        <br />

                        <?php put_element('lic'); ?>

                        <?php put_element('isaf_no'); ?>

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
        </div> <!-- forms -->
    <?php endif; ?>

    <h3 id="preinscrits"><span id="preregistered_sailors"></span></h3>
    <div class="contenu">
        <?php include 'Preinscrits-html.php'; ?>
    </div><!-- prinscrits -->

    <?php if(isset($regate['resultats']) && $regate['resultats'] != ''): ?>
    <h3>Résultats</h3>
    <div class="contenu" style="padding:10px;height:500px">
        <?php include 'Resultats-html.php'; ?>
    </div><!-- prinscrits -->
    <?php endif; ?>
    
</div> <!-- accordion -->
<?php
xhtml_post();
