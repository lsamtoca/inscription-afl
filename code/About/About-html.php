<?php
Layouts::xhtml_pre1('A propos de ce programme');
Layouts::requireJquery();
Layouts::requireJqueryValidations();
Layouts::requireMyAccordion();
?>

<?php if ($config['moduleDonate']): ?>
    //  window.location.hash = "faireUnDon";
<?php endif; ?>

</script>


<?php
xhtml_pre2('WebRegatta 4.0');
doMenu();
?>

<div id='accordion'>
    <!--<div class="contenu" style="padding:20pt;font-size:120%"-->

    <?php if ($config['moduleDonate']): ?>
        <h3 id='faireUnDon'>Faire un don</h3>
        <?php include('code/About/boutonPayPal.php'); ?>
    <?php endif; ?>

    <?php if (!$config['isClub']): ?>
        <h3 id="openRace">Demandez l'ouverture d'une régate</h3>
        <div class="contenu">
            <?php
            $ouvrirUneRegate->html(2);
            ?>
        </div>
    <?php endif; ?>


    <h3 id="aPropos">A propos de ce logiciel</h3>
    <?php
    include('code/About/news.php');
    //echoGoBack();
    ?>

    <?php if ($config['moduleAdvertise']): ?>
        <h3 id='ceLogiciel'>Demandez ce logiciel</h3>
        <div class="contenu">
            <?php $advertise->html(2); ?>
        </div>
    <?php endif; ?>

</div>
<?php
Layouts::xhtml_post();
