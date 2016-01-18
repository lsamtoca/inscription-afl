<?php
global $regate, $confirmation;


$page_title1 = 'Pré-inscription à la régate ' . $regate['titre'];
$page_title2 = 'Pré-inscription à la régate <em>' . $regate['titre'] . '</em>';
xhtml_pre1($page_title1);
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="js/jquery.i18n.properties.js" type="text/javascript"></script>
<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>

<?php if(LANGUAGEON): ?>
    <script type="text/javascript">
        var documentLanguage='<?php echo LANGUAGE ; ?>';
    </script>
<?php endif;?>

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

<?php xhtml_pre2($page_title2); 
doMenu($menuLanguage);
?>



<div id="accordion">

    <h3 id="infos"><?php echo $regate['titre']; ?>, infos</h3>
    <?php include 'Formulaire-html-info_regate.php'; ?>

    <?php if (Regate_estOuverte($regate)): ?>
        <h3 id="formulaires"><span id="preregistration_form" class="msg"></span></h3>
        <div id="forms" class="contenu">
            <?php if (!$confirmation): ?> 
                <?php include 'searchform-html.php'; ?>

                <br />
            <?php else: ?> 
                <p style="color:red">
                    <span id="message_confirmation"  class="msg"></span>
                </p>
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
