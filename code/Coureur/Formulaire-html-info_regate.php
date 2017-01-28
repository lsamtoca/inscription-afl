<?php
global $regate;
//require_once 'php/Regate.php';

$regateHasDates = ($regate['date_debut'] != "00-00-0000" and $regate['date_fin'] != "00-00-0000");
$regateHasLieu = $regate['lieu'] != '';
$regateHasSeries = !empty($regate['series']);
$series = array();
foreach ($regate['series'] as $serie) {
    array_push($series, $serie['nomLong']);
}
$seriesString = implode(', ', $series);
$regateHasLimitePreInscriptions = $regate['date_limite_preinscriptions'] != '';
$regateHasDroits = $regate['droits'] != '0';
$regateHasInformations = $regate['informations'] != '';
$regateIsClosed = !Regate::estOuverte($regate);
$regateHasClub=$regate['cv_organisateur'] != '';
?>

<!--Dates, titre, description-->
<p>
    <?php if ($regateHasDates) : ?>
        Du <?php echo Regate::formatDebut($regate); ?> au <?php echo Regate::formatFin($regate); ?> :
    <?php endif; ?>
    <b><?php echo $regate['titre']; ?></b>
    <?php if ($regateHasLieu): ?> à <?php echo $regate['lieu']; ?><?php endif; ?>.
</p>
<p><?php echo $regate['description'] ?></p>

<!--Club organisateur-->
<?php if ($regateHasClub): ?>
    <p>
        <span class="msg">Club organisateur :</span>
        <?php echo $regate['cv_organisateur']; ?>.
    </p>
<?php endif; ?>

<!-- Séries-->
<?php if ($regateHasSeries): ?>
    <p>
        <span class="msg">Séries :</span>
        <?php echo $seriesString; ?>.
    </p>
<?php endif; ?>

<!--Date limite pré-inscription-->
<?php if ($regateHasLimitePreInscriptions): ?>
    <p>
        <span id="deadline" class="msg"></span>
        <?php echo Regate::formatDeadline($regate); ?>
    </p>

    <?php if ($regateIsClosed): ?>
        <p><span class="msg" id="raceIsClosed"></span></p>
    <?php endif; ?>
<?php endif; ?>

<!--Droits-->
<?php if ($regateHasDroits): ?>
    <p><span class="msg" id="droits"></span>
        <?php echo $regate['droits']; ?>
        &#8364;</p>
<?php endif; ?>

<!--Autres informations-->
<?php if ($regateHasInformations): ?>
    <p>
        <span class="msg" id="autresInformations"></span>
        <?php echo $regate['informations']; ?>
    </p>
<?php endif; ?>
