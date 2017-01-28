<?php
// It seems that we do not need the line below
//require_once 'php/Administrateur.php';
date_default_timezone_set('Europe/Paris');

Layouts::xhtml_pre1('Administration des régates (événements et clubs)');

Layouts::requireJquery();
Layouts::requireJqueryValidations();
Layouts::requireJqueryDatePicker();
Layouts::requireMyAccordion();

$nouvelleRegate->form->displayJQueryDatePickers();

Layouts::xhtml_pre2('Administration des régates (événements et clubs)');

$menus=new Menus();
$menus->html();
?>

<div id='accordion'>

    <h3 id='nouvregate'>Nouvelle régate</h3>
    <div class="contenu">
        <?php $nouvelleRegate->html(2); ?>
    </div>

    <h3 id='listeRegates'>Liste des régates ouvertes</h3>
    <div class="contenu">
        <?php $listeRegates->html(2); ?>
    </div>

    <h3 id="fichiersFFV">Mettre à jour les fichiers de la Fédération Française de Voile</h3>
    <div class="contenu">
        <?php $fichiersFFV->html(2); ?>
    </div>

    <h3>Contacter les utilisateurs</h3>
    <?php $courrielUtilisateurs->html(2); ?>

    <h3 id="celogiciel">A propos du logiciel</h3>
    <?php
    $administrateur = TRUE;
    include('code/About/news.php');
    ?>

</div><!-- accordion -->

<?php
Layouts::xhtml_post();
