<div class="contenu">

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

    <form action='<?php echo urlSelf(); ?>#regatesouvertes' method='post' id="formnewrace">

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
