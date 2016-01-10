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
            Vous pouvez signaler aux développeurs un problème 
            ou un souhait d'évolution du logiciel.
            <br />

            N'envoyez pas des messages à la dernière minute.
            <br />

            Soyez, dans votre message, le plus précis possible !!!
            <br />

            <br />
            <hr />
            <label>Objet : </label>
            <br />
            <input id='form_helpdesk-objet' type='text' name='objet' style='width:100%;' /> 
            <br />
            <br />

            <label>Votre message : </label>
            <br />
            <textarea id='form_helpdesk-message' name='message' rows='20' style='width:100%;'></textarea>  
            <hr />
            <input type='submit' name='helpdesk' value='Envoyer' />
        </fieldset>
    </form>
</div> <!-- helpdesk -->
