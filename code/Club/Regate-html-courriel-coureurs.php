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
            function set_tous() {
                document.getElementById('to').value = "<?php echo $mails_all; ?>";
            }
            function set_confirmes() {
                document.getElementById('to').value = "<?php echo $mails_confirme; ?>";
            }
            function set_pas_confirmes() {
                document.getElementById('to').value = "<?php echo $mails_pas_confirme; ?>";
            }
        </script>

        <form 
            action='<?php echo urlSelf(); ?>#courriel' 
            method='POST' 
            enctype='multipart/form-data'>
            <fieldset>
                Envoyer un courriel à :
                <input type='radio' name='aqui' checked value='tous' onClick='set_tous()'><label>tous les préinscrits</label>
                <input type='radio' name='aqui' value='confirmes' onClick='set_confirmes()'><label>les préinscrits ayant confirmé</label>
                <input type='radio' name='aqui' value='pas_confirmes' onClick='set_pas_confirmes()'><label>ceux qui n'ont pas encore confirmé</label>
                <hr />
                <label>To : </label>
                <br />
                <input type='text' name='to' id='to' style='width:100%;' 
                       readonly value='<?php echo $mails_all; ?>' />
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

</div><!--envoyer email aux coureurs-->
