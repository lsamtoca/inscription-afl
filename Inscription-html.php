<?php if ($modeInsert): ?>

    <?php xhtml_pre("Vous êtes (presque) préinscrit"); ?>

    <?php if ($_POST['lang'] == 'en'): ?>

        <?php echo $_POST['Prenom'] . ' ' . $_POST['Nom']; ?>,
        we ask you to confirm your preregistration. 
        <br /><br/>
        You'll receive an email at your address <br />
        <div style="margin-left:20mm;margin-top:5mm">
            <address><?php echo $_POST['mail'] ?></address>
        </div><br />
        This email contains a link by which you'll be able to confirm your preregistration.

    <?php else: ?>
        <?php echo $_POST['Prenom'] . ' ' . $_POST['Nom']; ?>,
        nous vous demandons de confirmer votre préinscription. 
        <br /><br/>

        <?php if (isset($_POST['no_email'])): ?>

            Vous allez recevoir un courriel 
            à votre adresse.
            Ce courriel contient un lien qui vous permettra de confirmer votre préinscription.

        <?php else: ?>
            Vous allez recevoir un courriel 
            à l'adresse <br />
            <div style="margin-left:20mm;margin-top:5mm">
                <address><?php echo $_POST['mail'] ?></address>
            </div><br />
            Ce courriel contient un lien qui vous permettra de confirmer votre préinscription.
        <?php endif; ?>
    <?php endif; ?>


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
