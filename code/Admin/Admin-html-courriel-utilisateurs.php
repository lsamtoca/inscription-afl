<div class="contenu">

    <script type='text/javascript'>
        function set_mailsCoureurs() {
            document.getElementById('to').value = "{mailsCoureurs}";
        }
        function set_mailsClubs() {
            document.getElementById('to').value = "{mailsClubs}";
        }
        function set_mailsAdmins() {
            document.getElementById('to').value = "{mailsAdmins}";
        }
    </script>

    <form 
        action='{self}#courriel' 
        method='POST' 
        enctype='multipart/form-data'>
        <fieldset>
            Envoyer un courriel aux :
            <input type='radio' name='aqui' checked value='coureurs' onClick='set_mailsCoureurs()'><label>coureurs</label>
            <input type='radio' name='aqui' value='clubs' onClick='set_mailsClubs()'><label>clubs</label>
            <input type='radio' name='aqui' value='admins' onClick='set_mailsClubs()'><label>autres administrateurs</label>
            <hr />
            <label>To : </label>
            <br />
            <input type='text' name='to' id='to' style='width:100%;' 
                   readonly value='{mailsCoureurs}' />
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


</div><!--envoyer un courriel aux utilisateurs-->
