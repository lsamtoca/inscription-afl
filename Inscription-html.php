<?php if ($modeInsert): ?>



    <?php xhtml_pre("Vous êtes (presque) préinscrit"); ?>

    <?php
    //echo $_POST['lang']; 
    //var_dump($_POST);
//    echo "<pre>";
//    print_r($_POST);
//    echo "</pre>";
    ?>
    <big>
        <?php if ($_POST['lang'] == 'en'): ?>
            Dear
        <?php else: ?>
            Cher(e)
        <?php endif; ?>
        <?php echo $_POST['Prenom'] . ' ' . $_POST['Nom']; ?>,

        <br /><br />

        <?php if ($_POST['lang'] == 'en'): ?>
            we ask you to confirm your pre-registration.
            <br />

            We have sent you a message
            <?php if (isset($_POST['no_email'])): ?>
                at your email address 
                (the last address you used with this system).      
            <?php else: ?>
                at the email address 
                <br />
                <div style="margin-left:20mm;margin-top:5mm">
                    <address><?php echo $_POST['mail'] ?></address>
                </div>
            <?php endif; ?>
            <br />
            The message contains
            a link by which you will be able to confirm and/or modify your pre-registration.

        <?php else: ?>
            nous vous demandons de confirmer votre pré-inscription. 
            <br /><br/>

            Vous allez recevoir un courriel 
            <?php if (isset($_POST['no_email'])): ?>
                à votre adresse (le dernier que vous avez utilisé avec ce système).
            <?php else: ?>
                à l'adresse <br />
                <div style="margin-left:20mm;margin-top:5mm">
                    <address><?php echo $_POST['mail'] ?></address>
                </div><br />
            <?php endif; ?>
            <br/>
            Ce courriel contient un lien qui vous permettra de confirmer votre pré-inscription.
        <?php endif; ?>
    </big>

    <p>
        Retour au
        <a href="<?php echo format_url_regate($_POST['IDR']); ?>"> formulaire d'inscription</a>
    </p>

    <?php xhtml_post(); ?>

<?php endif; ?>

<?php if ($modeConfirm): ?>

    <?php xhtml_pre('Confirmation'); ?>

    <div>
        <p>
            Bonjour <?php echo $inscrit['prenom'] . ' ' . $inscrit['nom'] ?>,<br />
            votre inscription est maintenant confirmée !!!
        </p>

        <p>
            Vous pouvez vérifier votre inscription sur la
            <a href="<?php echo $URLPRE; ?>">
                <span id='liste_preinscrits'>liste des pré-inscrits</span>
            </a> à cette régate.
        </p>

    </div>

    <?php xhtml_post(); ?>


<?php endif; ?>
