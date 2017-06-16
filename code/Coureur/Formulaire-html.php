<?php
global $config, $regate, $confirmation;

$page_title1 = 'Pré-inscription à la régate ' . $regate['titre'];
$page_title2 = 'Pré-inscription à la régate <em>' . $regate['titre'] . '</em>';
xhtml_pre1($page_title1);
Layouts::requireJquery();
//Layouts::requireJqueryDatePicker();
//Layouts::requireJqueryI18n();

?>


<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>

<script src="js/jquery.i18n.properties.js" type="text/javascript"></script>
<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>

<!-- Parametrisation de javascript -->
<script type="text/javascript">
<?php if ($config['moduleLanguage']): ?>
        var documentLanguage = '<?php echo LANGUAGE; ?>';
<?php endif; ?>
<?php if (Regate::estLaser($regate)): ?>
        var iAmAFL = true;
<?php endif; ?>
<?php if ($comingFromSearchDbf): ?>
        window.location.hash = "formulaires";
<?php endif; ?>
</script>


<script src="js/Formulaire-i18n.js" type="text/javascript"></script>
<script src="js/Formulaire-validation.js" type="text/javascript"></script>
<script src="js/Formulaire-dynamic.js" type="text/javascript"></script>

<script src="js/myaccordion.js" type="text/javascript"></script>


<script type="text/javascript">

    $(document).ready(function () {

        $("#naissance").datepicker({
            dateFormat: "dd/mm/yy",
            defaultDate: "01/01/98",
            changeYear: true,
            yearRange: "c-20:c+20"
        });

        myaccordion_set_accordion();

        $('mainform').submit(function () {
            alert($(this).serialize());
            return false;
        });
    });

</script>

<?php
xhtml_pre2($page_title2);
doMenu($menuLanguage);
?>



<div id="accordion">

    <h3 id="infos"><?php echo $regate['titre']; ?>, infos</h3>
    <div class="contenu" id='infos_regate'>
        <?php include 'Formulaire-html-info_regate.php'; ?>
    </div> <!--infos_regate-->

    <?php if (Regate_estOuverte($regate)): ?>
        <h3 id="formulaires"><span id="preregistration_form" class="msg"></span></h3>
        <div id="forms" class="contenu">
            <?php if (!$confirmation && !$comingFromSearchDbf): ?> 
                <?php include 'searchform-html.php'; ?>

                <br />
            <?php else: ?> 
                <?php if (!$comingFromSearchDbf): ?> 
                    <p style="color:red">
                        <span id="message_confirmation"  class="msg"></span>
                    </p>
                <?php endif; ?>
            <?php endif; ?> 

            <?php include 'mainform-html.php'; ?>
        </div> <!-- forms -->
    <?php endif; ?>

    <h3 id="preinscrits"><span id="preregistered_sailors" class="msg"></span></h3>
    <?php include 'Preinscrits-html.php'; ?>

    <?php if (isset($regate['resultats']) && $regate['resultats'] != ''): ?>
        <h3 id="resultats"><span id="results_iframe" class="msg"></span></h3>
            <?php include 'Resultats-html.php'; ?>        
        <?php endif; ?>

</div> <!-- accordion -->
<?php
xhtml_post();
