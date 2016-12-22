<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

xhtml_pre1('A propos de ce programme');
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>
<script src="js/myaccordion.js" type="text/javascript"></script>

<script type="text/javascript">

    $(document).ready(function () {
        myaccordion_set_accordion();
    });

<?php if ($config['moduleDonate']): ?>
        window.location.hash = "faireUnDon";
<?php endif; ?>

</script>


<?php
xhtml_pre2('WebRegatta 4.0');
doMenu();
//$debugger = new debugger();
//$debugger->config();
?>

<div id='accordion'>
    <!--<div class="contenu" style="padding:20pt;font-size:120%"-->


    <?php if ($config['moduleDonate']): ?>
        <h3 id='faireUnDon'>Faire un don</h3>
        <?php include('code/About/boutonPayPal.php'); ?>
    <?php endif; ?>

    <h3>A propos de ce logiciel</h3>
    <?php
    include('code/About/news.php');
    //echoGoBack();
    ?>
<!--
    <h3>Demandez ce logiciel</h3>
    <?php
    //include('code/About/About-html-helpdesk.php');
    //echoGoBack();
    ?>
-->
    <h3>Demandez ce logiciel</h3>
    <div class="contenu">
        <?php
        $form->displayValidation(2);
        $form->display(2);
        ?>
    </div>
</div>
<?php
xhtml_post();
