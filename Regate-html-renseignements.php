<div class="contenu">
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
                        url: true
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
                        url: url_msg
                    },
                    droits: {
                        pattern: nombre_msg
                    }
                }

            });
        });
    </script>

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

            <hr /><!-- Dates -->
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

            <span class="help">
                <span>Après cette date il ne sera plus possible se pré-inscrire avec ce logiciel.</span>
            </span>

            <hr /><!-- Sous -->
            <label>Droits d'inscription :</label>
            <input name="droits" type="text" id="droits_inscription" 
                   value="<?php echo $DROITS_INSCRIPTION; ?>" />
            <span class="help">
                <span >Combien coute s'inscrire à la régate ? 
                        Si la valeur est 0, rien ne sera affiché 
                        sur la page d'informations sur la régate du logiciel.</span>
            </span>

            <br />
            <label>Lien pour le paiement en ligne :</label>
            <!--
            <input name="paiement_en_ligne" type="text" id="payment_en_ligne" 
                   value="<?php echo $paiement_EN_LIGNE; ?>" />
            -->
            <textarea id='paiement_en_ligne' name='paiement_en_ligne' cols='40' rows='1'><?php echo $paiement_EN_LIGNE ?></textarea>
            <span class="help">
                <span class="facultatif">Lien vers le site de paiement en ligne. 
                    Ce lien apparaitra seulement sur le courriel 
                    demandant au coureur de confirmer son pré-inscription 
                    et de payer via le lien donné.</span>
            </span>


            <hr />
            <span class="line">
                <label>Autres informations  :</label>
                <textarea id='informations' name='informations' 
                          cols='50' rows='10'><?php echo $INFORMATIONS ?></textarea>
                <span class="help">
                    <span class="facultatif">Ajoutez ici tout autre renseignement qui vous 
                        semble intéressant transmettre  au coureur :
                        frais d'inscriptions majorées, à quelles conditions, 
                        lien vers le site du club, lien vers l'avis de course, 
                        lien vers les instructions de course. 
                    </span>
                </span>
            </span>

            <hr />

            <br>

            <input type="submit" id="Modifier" value="Modifier" />

        </fieldset>
    </form>
</div>

