<div class="contenu" class="form">
    <script type="text/javascript">
        $(document).ready(function () {
            //function set_form_info_regate() = {

            // Date pickers
            $("#date_debut").datepicker({
                dateFormat: "dd/mm/yy",
                defaultDate: "<?php echo $DATE_DEBUT_REGATE; ?>",
                changeYear: true,
                yearRange: "c-2:c+2"
            });
            $("#date_fin").datepicker({
                dateFormat: "dd/mm/yy",
                defaultDate: "<?php echo $DATE_FIN_REGATE; ?>",
                changeYear: true,
                yearRange: "c-2:c+2"
            });
            $("#date_limite_preinscriptions").datepicker({
                dateFormat: "dd/mm/yy",
                defaultDate: "<?php echo $DATE_LIMITE_PREINSCRIPTIONS; ?>",
                changeYear: true,
                yearRange: "c-2:c+2"
            });

            // Form validation
            var req_msg = "Champ obligatoire";
            var em_msg = "Entrez un adresse courriel valide";
            var url_msg = "Entrez un url valide";
            var nombre_msg = "Rentrez un nombre";
            var max100_msg = "Au plus 100 caractères";
            var max50_msg = "Au plus 50 caractères";
            $.validator.addMethod(
                    "pattern",
                    function (value, element, regexp) {
                        var re = new RegExp(regexp);
                        return this.optional(element) || re.test(value);
                    },
                    "Please check your input."
                    );

            $("#form_info_regate").validate({
                rules: {
                    titre: {
                        required: true
                    },
                    lieu: {
                        required: true
                    },
                    courriel: {
                        required: true,
                        email: true
                    },
                    date_debut: {
                        required: true
                    },
                    date_fin: {
                        required: true
                    },
                    date_limite_preinscriptions: {
                        required: true
                    },
                    droits: {
                        pattern: /^\d+|\d+,\d{1,2}$/
                    },
                    paiement_en_ligne: {
                        url: true,
                        maxlength: 50
                    },
                    resultats: {
                        url: true,
                        maxlength: 100
                    }
                },
                messages: {
                    titre: {
                        required: req_msg
                    },
                    lieu: {
                        required: req_msg
                    },
                    courriel: {
                        required: req_msg,
                        email: em_msg
                    },
                    date_debut: {
                        required: req_msg
                    },
                    date_fin: {
                        required: req_msg
                    },
                    date_limite_preinscriptions: {
                        required: req_msg
                    },
                    paiement_en_ligne: {
                        url: url_msg,
                        maxlength: max50_msg
                    },
                    resultats: {
                        url: url_msg,
                        maxlength: max100_msg
                    },
                    droits: {
                        pattern: nombre_msg
                    }
                }

            });
            $(function () {
                $(".sortable").sortable();
                $(".sortable").disableSelection();
            });
        }); // Document ready
    </script>
    <form id='form_info_regate' 
          action='<?php echo urlSelf(); ?>#renseignements' 
          method='post'>
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

            <hr /><!-- Dates -->
            <label>Date début :</label>
            <input name="date_debut" type="text" id="date_debut" size="10"
                   value="<?php echo $DATE_DEBUT_REGATE; ?>" class="required"/>

            <label>Date fin :</label>
            <input name="date_fin" type="text" id="date_fin"  size="10"
                   value="<?php echo $DATE_FIN_REGATE; ?>" class="required"/>

            <br />
            <label>Date limite pour se pré-inscrire sur le web :</label>
            <input name="date_limite_preinscriptions" type="text" id="date_limite_preinscriptions" 
                   size="10"
                   value="<?php echo $DATE_LIMITE_PREINSCRIPTIONS; ?>" class="required"/>

            <span class="help">
                <span class="normalwidth">Après cette date il ne sera plus possible se pré-inscrire avec ce logiciel.</span>
            </span>

            <hr /><!-- Series -->
            <!-- <label>Séries :</label> <br /> -->
            <?php
            require_once 'php/Forms.php';
            function formatLabelSerie($serie){
                //return $serie['nomLong'] . " ($serie[nom])";
                //return "[$serie[nom]]  $serie[nomLong]";
                return "$serie[nomLong] [$serie[nom]]";
            }
            $series = new Series();

            /*
              foreach ($availableSeries as $serie) {
              $value = 0;
              if (isset($regate['series'][$serie['nom']])) {
              $value = 1;
              }
              $formInputs = array(
              $serie['nom'] =>
              array(
              'name' => $serie['nom'],
              'rendering' => 'checkbox',
              'label' => $serie['nomLong'],
              'value' => $value
              )
              );
              echo_input($serie['nom'], $formInputs);
              }
             */
            $choices = array();
            foreach ($regate['series'] as $nom => $serie) {
                $choices[$nom] = array(
                    'label' => formatLabelSerie($serie),
                    'checked' => true
                );
            }
            foreach ($series->available as $nom => $serie) {
                if (!isset($choices[$nom])) {
                    $choices[$nom] = array(
                        'label' => formatLabelSerie($serie),
                        'checked' => false
                    );
                }
            }

            $formInputs = array(
                'series' =>
                array(
                    'rendering' => 'orderedMChoice',
                    'label' => 'Series',
                    'choices' => $choices
                )
            );
            echo_input('series', $formInputs);
            ?>

            <hr /><!-- Sous -->
            <label>Droits d'inscription :</label>
            <input name="droits" type="text" id="droits_inscription" 
                   value="<?php echo $DROITS_INSCRIPTION; ?>" />
            <span class="help">
                <span class="normalwidth">Combien coûte s'inscrire à la régate ? 
                    Si la valeur est 0, rien ne sera affiché 
                    sur la page d'informations sur la régate du logiciel.</span>
            </span>

            <br />
            <label>Lien pour le paiement en ligne :</label>
            <!--
            <input name="paiement_en_ligne" type="text" id="payment_en_ligne" 
                   value="<?php echo $paiement_EN_LIGNE; ?>" />
            -->
            <textarea id='paiement_en_ligne' name='paiement_en_ligne' cols='41' rows='1'><?php echo $paiement_EN_LIGNE ?></textarea>
            <span class="help">
                <span class="facultatif thin">Lien vers le site de paiement en ligne. 
                    Ce lien apparaîtra seulement sur le courriel 
                    demandant au coureur de confirmer son pré-inscription 
                    et de payer via le lien donné.</span>
            </span>

            <br />
            Visible :
            Oui <input type="radio" name="istest" value="0" 
            <?php if ($regate['istest'] == 0) echo "checked" ?>
                       >

            Non<input type="radio" name="istest" value="1"
            <?php if ($regate['istest'] == 1) echo "checked" ?>
                      >
            <span class="help">
                <span class="wide">Si cette régate est cochée comme n'étant pas visible, 
                    elle apparaîtra seulement sur le 
                    site de développement 
                    du logiciel, http://regateslaser.info/inscriptions_afl_dev.
                    Vous pouvez décider de cacher cette régate pour le temps nécessaire à
                    compléter tous les renseignements de la régate, 
                    par exemple et en particulier, 
                    si vous utilisez le lien pour le paiement en ligne.
                </span>
            </span>


            <hr />
            <label>Lien vers les résultats :</label>
            <textarea id='resultats' name='resultats' cols='48' rows='1'><?php echo $regate['resultats'] ?></textarea>
            <span class="help">
                <span class="facultatif thin">
                    Vous pouvez ajouter une lien vers les résultats
                    une fois la régate est en cours ou terminée.
                </span>
            </span>

            <hr />
<!--            <span class="line"> -->
            <label>Autres informations  :</label>
            <textarea id='informations' name='informations' 
                      cols='50' rows='10'><?php echo $INFORMATIONS ?></textarea>
            <span class="help">
                <span class="facultatif thin">Ajoutez ici tout autre renseignement qui vous 
                    semble intéressant transmettre  au coureur :
                    frais d'inscriptions majorées, à quelles conditions, 
                    lien vers le site du club, lien vers l'avis de course, 
                    lien vers les instructions de course. 
                </span>
            </span>
            <!--            </span> -->

            <br>

            <input type="submit" name="submitRenseignements" value="Modifier" />

        </fieldset>
    </form>
</div>

