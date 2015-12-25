<?php
global $regate;
require_once 'php/Regate.php';
?>
<div class="contenu" id='infos_regate'>

    <!--Dates, titre, description-->
    <p>
        <?php if ($regate['date_debut'] != "00-00-0000" and $regate['date_fin'] != "00-00-0000"): ?>
            Du <?php echo Regate_formatDebut($regate); ?> au <?php echo Regate_formatFin($regate); ?> :
        <?php endif; ?>
        <b><?php echo $regate['titre']; ?></b>
        <?php if ($regate['lieu'] != ""): ?>
            à <?php echo $regate['lieu']; ?>
        <?php endif; ?>.
    </p>
    <p><?php echo $regate['description'] ?></p>


    <!--Date limite pré-inscription-->
    <?php if ($regate['date_limite_preinscriptions'] != ''): ?>
        <p>
            <span id="deadline" class="msg"></span>
            <?php echo Regate_formatDeadline($regate); ?>
        </p>

        <?php if (!Regate_estOuverte($regate)): ?>
            <p><span class="msg" id="raceIsClosed"></span></p>
        <?php endif; ?>
    <?php endif; ?>

    <!--Droits-->
    <?php if ($regate['droits'] != '0'): ?>
        <p><span class="msg" id="droits"></span>
            <?php echo $regate['droits']; ?>
            &#8364;</p>
    <?php endif; ?>

    <!--Autres informations-->
    <?php if ($regate['informations'] != ''): ?>
        <p>
            <span class="msg" id="autresInformations"></span>
            <?php echo $regate['informations']; ?>
        </p>
    <?php endif; ?>
</div> <!--infos_regate-->
