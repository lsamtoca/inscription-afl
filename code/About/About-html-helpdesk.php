<div class="contenu">
    <script type="text/javascript">
        $(document).ready(function () {
            var req_msg = "Champ obligatoire";
            $("#form_helpdesk").validate({
                rules: {
                    objet: {
                        required: true
                    },
                    message: {
                        required: true
                    }
                },
                messages: {
                    objet: {
                        required: req_msg
                    },
                    message: {
                        required: req_msg
                    }
                }
            });
        });
    </script>

    <form id='form_helpdesk'
          action='<?php echo urlSelf(); ?>' 
          method='POST'>
        <fieldset>
            Demandez au développeur d'installer ce logiciel su votre site web.
            <br />
            <hr />
            <label>Objet : </label>
            <br />
            <input id='form_helpdesk-objet' type='text' name='objet' style='width:100%;' 
                   value="Installer WebRegatta" disabled="disabled"
                   /> 
            <br />
            <br />


            <label>Votre courriel : </label>
            <br />
            <textarea id='form_helpdesk-courriel' name='courriel' rows='1' style='width:50%;'></textarea>  


            <br />
            <label>Votre message : </label>
            <br />
            <textarea id='form_helpdesk-message' name='message' rows='7' style='width:100%;'></textarea>  
            <hr />
            <img src="captcha_code_file.php?rand=<?php echo rand(); ?>"
                 id="captchaimg" >
            <!--
            <img id="captcha" src="externals/securimage/securimage_show.php" alt="CAPTCHA Image" />
            <label for="message">Enter the code above here :</label>
            <input id="6_letters_code" name="6_letters_code" type="text">
            -->
            <input type='submit' name='helpdesk' value='Envoyer' />
        </fieldset>
    </form>
</div> <!-- helpdesk -->
