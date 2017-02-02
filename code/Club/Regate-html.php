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

Layouts::xhtml_pre1('Gestion de votre régate');
Layouts::requireJquery();
Layouts::requireJqueryValidations();
Layouts::requireJqueryDatePicker();
Layouts::requireMyAccordion();
Layouts::xhtml_pre2('Gestion de votre régate');
$menus= new Menus();
$menus->html();
?>

<div id='accordion'>

    <h3 id="renseignements">Formulaire renseignements sur la régate</h3>
    <?php include('Regate-html-renseignements.php'); ?>

    <h3>Adresses du formulaire pré-inscription et de la liste des pré-inscrits</h3>
    <?php include('Regate-html-adresses.php'); ?>

    <h3>Liste des inscrits</h3>
    <?php include('Regate-html-liste-inscrits.php'); ?>

    <h3>Fiches d'enregistrement des participants</h3>
    <?php include('Regate-html-fiches.php'); ?>

    <h3 id="exportation">Exportation des données et intégration avec FREG</h3>
    <div class="contenu">
        <?php $exportation->html(2)?>
    </div>

    <h3 id="courriel">Envoyer un courriel aux coureurs</h3>
    <?php include('Regate-html-courriel-coureurs.php'); ?>

    <h3 id="assistance">Assistance</h3>
    <?php include('Regate-html-helpdesk.php'); ?>

    <h3 id="celogiciel">A propos de ce logiciel</h3>
    <?php include('code/About/news.php'); ?>

</div>


<?php 
Layouts::xhtml_post(); 