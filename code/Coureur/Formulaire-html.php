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
            defaultDate: "01/01/98",
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

<div id='deconnexion' class="white_over_dark">
    <ul>    
        <li><a><span id='lang' class="msg"></span></a></li>
    </ul>
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
                <span id="deadline" class="msg"></span>
                <?php echo Regate_formatDeadline($regate); ?>
            </p>

            <?php if (!Regate_estOuverte($regate)): ?>
                <p><span id="raceIsClosed"  class="msg"></span></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($regate['droits'] != '0'): ?>
            <p><span id="droits" class="msg"></span>
                <?php echo $regate['droits']; ?>
                &#8364;</p>
        <?php endif; ?>
        <?php if ($regate['informations'] != ''): ?>
            <p>
                <span id="autresInformations"  class="msg"></span>
                <?php echo $regate['informations']; ?>
            </p>
        <?php endif; ?>
    </div> <!--infos_regate-->

    <?php if (Regate_estOuverte($regate)): ?>
        <h3 id="formulaires"><span id="preregistration_form" class="msg"></span></h3>
        <div id="forms" class="contenu">
            <?php
            if (!$confirmation):
                ?> 

                <div id='search'>

                    <form name="searchform" id="searchform" action="" method="post">

                        <fieldset>
                            <legend >
                                <span id='search_legend' class="msg"></span>
                                <span class='help wide'>
                                    <span id="searchform_help" class="wide msg"></span>
                                </span>
                            </legend>



                            <input name="lang" type="hidden" id="search_input_lang" value="fr"/>
                            <label class="left">
                                <span id='l_search_lic' class="msg"></span>                   
                            </label>
                            <input name="search_lic" id="search_lic" type="text"/>
                  <!--          <input name="search_submit" type='submit' value="Chercher">
                            -->          


                            <label class="left"><span class="msg" id='l_search_isaf'></span></label>
                            <input name="search_isaf"
                                   id="search_isaf" type="text" />

                            <input name="search_submit" type='submit' value="Chercher">


                            <div class="caveat" style="padding:5px;padding-left:0px">
                                <span id="searchform_caveat" class="msg"></span>
                            </div>


                        </fieldset>

                    </form>

                </div><!-- recherche par licence ou numero ISAF -->

                <br />
                <?php
            else:
                ?> 
                <p style="color:red">
                    <span id="message_confirmation"  class="msg"></span>
                </p>
            <?php
            endif;
            ?> 

            <div id='formulaire'>

                <form id="mainform" action="Inscription.php" method="post">
                    <fieldset>
                        <legend id='mainform_legend' class="msg"></legend>

                        <!-- Hidden inputs to handle control -->
                        <?php put_element('lang'); ?> 
                        <?php put_element('IDR'); ?> 
                        <?php put_element('conf'); ?> 
                        <?php put_element('ID_inscrit'); ?> 

                        <!-- Donnés personnels : nom prénom, date naissance, sexe -->

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
                        <?php echo_label('Nvoile'); ?>
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

                        <div id="licencie_ffv" class="msg">
                        </div>

                        <div id="non_licencie" class="msg">
                        </div>

                        <div id="etranger" class="msg">
                        </div>

                        <hr />

                        <input type="submit" name="maSoumission" id="soumission" value="Valider"/>
                    </fieldset>
                </form>
            </div> <!-- formulaire -->
        </div> <!-- forms -->
    <?php endif; ?>

    <h3 id="preinscrits"><span id="preregistered_sailors" class="msg"></span></h3>
    <div class="contenu">
        <?php include 'Preinscrits-html.php'; ?>
    </div><!-- prinscrits -->

    <?php if (isset($regate['resultats']) && $regate['resultats'] != ''): ?>
        <h3 id="resultats"  class="msg"><span id="results_iframe" class="msg"></span></h3>
            <?php include 'Resultats-html.php'; ?>        
    <?php endif; ?>

</div> <!-- accordion -->
<?php
xhtml_post();
