<?php
// Affichage

if (!filter_var($_SESSION['courriel'], FILTER_VALIDATE_EMAIL)) {

    $message = "L'adresse '" . $_SESSION['courriel'] . "n'est pas un adresse email valide.\n"
            . "Veuillez compléter le champ Courriel dans le formulaire renseignements sur la régate.\n"
            . "Ensuite, déconnectez vous et reconnectez vous une autre fois.";
    pageErreur($message);
    exit(-1); // Here there is a problem 
}


global $TITRE_REGATE, $DESC_REGATE,
 $LIEU, $CV_ORGANISATEUR,
 $DATE_DEBUT_REGATE, $DATE_FIN_REGATE, $DATE_LIMITE_PREINSCRIPTIONS,
 $COURRIEL;

// global $DROITS;
// do we need the ones below
//global $pdo_path, $user, $pwd, $pdo_options;

global $mails_all, $mails_confirme, $mails_pas_confirme;

xhtml_pre1('Gestion de votre régate');
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>
<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>

<script src="js/myaccordion.js" type="text/javascript"></script>


<script type="text/javascript">

    $(document).ready(function () {

        myaccordion_set_accordion();

//        $('div.explication').accordion({
//            collapsible: true,
//            active: true,
//            heightStyle: "content",
//            event: "click hoverintent",
//            header: "h4"
//        });
    });

</script>

<?php
xhtml_pre2('Gestion de votre régate');
doMenu();
?>

<div id='accordion'>

    <!--
        <h3>Notice d'utilisation du logiciel</h3>
        <div class="contenu">
            Téléchargez la <a href="docs/Notice_07-03-12.pdf">notice</a> d'utilisation (mise à jour le 7/03/12). 
        </div>
    -->

    <h3 id="renseignements">Formulaire renseignements sur la régate</h3>
    <?php include('Regate-html-renseignements.php'); ?>

    <h3>Adresses du formulaire pré-inscription et de la liste des pré-inscrits</h3>
    <?php include('Regate-html-adresses.php'); ?>

    <h3>Liste des inscrits</h3>
    <?php include('Regate-html-liste-inscrits.php'); ?>

    <h3>Fiches d'enregistrement des participants</h3>
    <?php include('Regate-html-fiches.php'); ?>

    <h3>Exportation des données et intégration avec FREG</h3>
    <?php include('Regate-html-exportation.php'); ?>

    <h3 id="courriel">Envoyer un courriel aux coureurs</h3>
    <?php include('Regate-html-courriel-coureurs.php'); ?>

    <h3 id="assistance">Assistance</h3>
    <?php include('Regate-html-helpdesk.php'); ?>

    <h3 id="celogiciel">A propos de ce logiciel</h3>
    <?php include('code/About/news.php'); ?>

</div>


<?php xhtml_post(); ?>