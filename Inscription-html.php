<?php if ($modeInsert): ?>

    <?php xhtml_pre("Vous êtes (presque) préinscrit"); ?>

    <?php
    if ($_POST['lang'] == 'en') {
        $dear = 'Dear';
        $ask = 'we ask you to confirm your pre-registration.';
        $sentyou = 'We have sent you a message';
        $lastemail = 'at your email address '
                . '(the last address you used with this system).';
        $youremail = 'at the email address';
        $modifyit = 'The message contains a link '
                . 'by which you will be able to '
                . 'confirm and/or modify your pre-registration.';
    } else {
        $dear = 'Cher(e)';
        $ask = 'nous vous demandons de confirmer votre pré-inscription';
        $sentyou = 'Nous vous avons envoyé un message';
        $lastemail = 'à votre adresse email (le dernier utilisé sur ce système).';
        $youremail = 'à l\'adresse';
        $modifyit = 'Le message contient un lien '
                . 'qui vous permettra de confirmer '
                . 'et/ou modifier votre pré-inscription.';
    }
    ?>
    <big>
        <?php
        echo "$dear ";
        echo $_POST['Prenom'] . ' ' . $_POST['Nom'];
        ?>,

        <br /><br />

        <?php echo $ask; ?>
        <br />

        <?php echo $sentyou; ?>
        <?php if (isset($_POST['no_email'])): ?> 
            <?php echo $lastemail; ?>
        <?php else: ?>
            <?php echo $youremail; ?>
            <br />
            <div style="margin-left:20mm;margin-top:5mm">
                <address><?php echo $_POST['mail'] ?></address>
            </div>
        <?php endif; ?>
        <br />
        <?php
        echo $modifyit;
        ?>
    </big>

    <br/><br/>

    <p>
        Retour au
        <a href="<?php echo format_url_regate($_POST['IDR']); ?>"> formulaire d'inscription</a>
    </p>

    <?php xhtml_post(); ?>

<?php endif; ?>

<?php
if ($modeConfirm):
    if ($_POST['lang'] == en) {
        $hello = 'Hello';
        $regok = 'your pre-registration is now completed.';
        $verifier = 'You can verify it on the';
        $liste = 'list of preregistered sailors';
    } else {
        $hello = 'Bonjour';
        $regok = 'votre inscription est maintenant confirmée';
        $verifier = 'Vous pouvez vérifier votre inscription sur la';
        $liste = 'liste des pré-inscrits';
    }
    ?>

    <?php xhtml_pre('Confirmation'); ?>

    <div>
        <big>
            <p>
                <?php
                echo "$hello ";
                echo $inscrit['prenom'] . ' ' . $inscrit['nom'];
                ?>,<br />
                <?php
                echo "$regok !!!";
                ?>
            </p>

            <p>
                <?php
                echo $verifier;
                ?>
                <a href="<?php echo $URLPRE; ?>">
                    <?php echo $liste; ?></a>.
            </p>
        </big>
    </div>

    <?php xhtml_post(); ?>


<?php endif; ?>
