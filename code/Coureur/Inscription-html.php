<?php
/// We are inside a function !!!
global $menuHome; 

global $dear,$ask,$sentyou, $lastemail, $modifyit, $youremail,$checkitout;
global $hello, $regok, $verifier;

if ($modeInsert):
    ?>

    <?php
    xhtml_pre($titleModeInsert);
    doMenu($menuHome);
    ?>

    <big>
        <?php
        echo $dear . " ";
        echo $post['Prenom'] . ' ' . $post['Nom'];
        ?>,

        <br /><br />

        <?php echo $ask; ?>
        <br />

        <?php echo $sentyou; ?>
        <?php if (isset($post['no_email'])): ?> 
            <?php echo $lastemail; ?>
    <?php else: ?>¯
        <?php echo $youremail; ?>
            <br />
            <div style="margin-left:20mm;margin-top:5mm">
                <address><?php echo $post['mail'] ?></address>
            </div>
        <?php endif; ?>
        <br />
        <?php echo $modifyit; ?>
        <br />
    <?php echo " " . $checkitout;
    ?>
    </big>

    <br/><br/>

    <p>
        Retour au
        <a href="<?php echo format_url_regate($post['IDR']); ?>"> formulaire d'inscription</a>
    </p>

    <?php xhtml_post(); ?>

<?php endif; ?>

<?php if ($modeConfirm): ?>

      <?php
  
    if($post['lang'] == 'it'){
        $message=messageAckConfirmation($inscrit['prenom'],$post['IDR']);
        pageAnswer($message, NULL, 'Vous etes cofirmé');
        exit(0);
    }
    
    xhtml_pre($titleModeConfirm);
    doMenu($menuHome);
    
    
    ?>

    <div>
        <big>
            <p>
                <?php
                echo $hello . ' ';
                echo $inscrit['prenom'] . ' ' . $inscrit['nom'];
                ?>,<br />
                <?php
                echo $regok . ' !!!';
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


    <?php
 endif; 
