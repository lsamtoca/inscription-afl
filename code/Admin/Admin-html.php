<?php
require_once 'php/Administrateur.php';
date_default_timezone_set('Europe/Paris');
$in_one_year = date("d/m/Y", 31536000 + time());

// Affichage
xhtml_pre1('Administration des régates (événements et clubs)');
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>
<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>

<script src="js/myaccordion.js" type="text/javascript"></script>



<?php 
xhtml_pre2('Administration des régates (événements et clubs)'); 
doMenu();
?>



<div id='accordion'>
    <!--      <h1>Gestion des Événements (et des Clubs)</h1>-->

    <h3>Nouvelle régate</h3>
    <?php include 'Admin-html-nouvelle-regate.php'; ?>

    <h3 id='regatesouvertes'>Liste des régates ouvertes</h3>
    <?php include 'Admin-html-regates-ouvertes.php'; ?>

    <h3>Mettre à jour le fichier COUREUR.DBF</h3>
    <?php include 'Admin-html-update-COUREUR-DBF.php'; ?>

    <h3 id="celogiciel">A propos de ce logiciel</h3>
    <?php $administrateur=TRUE; include('code/About/news.php'); ?>

</div><!-- accordion -->

<?php
$req->closeCursor();
xhtml_post();
?>