<?php
require_once 'php/Forms.php';
// We do not need to load this,
// as it has been loaded and modified by logique.php 
//require_once 'mainform-elements.php';
global $mainformInputs;

function echoInput($name) {
    global $mainformInputs;
    echo_input($name, $mainformInputs);
}
?>
<div id='formulaire'>
    <form id="mainform" action="Inscription.php" method="post">
        <fieldset>
            <legend id='mainform_legend' class="msg">
                <?php echoMsg($id); ?>
            </legend>

            <!-- Hidden inputs to handle control -->
            <?php echoInput('lang'); ?> 
            <?php echoInput('IDR'); ?> 
            <?php echoInput('conf'); ?> 
            <?php echoInput('ID_inscrit'); ?> 

            <!-- Donnés personnels : nom prénom, date naissance, sexe -->
            <?php echoInput('Nom'); ?> 
            <?php echoInput('Prenom'); ?> 

            <br />

            <?php echoInput('naissance'); ?> 


            <br />

            <?php echoInput('sexe'); ?> 

            <hr />

            <!-- Taille Polo -->


            <?php
            echoInput('taillepolo');
            if ($regate['polo'] == '1')
                echo "<hr />";
            ?> 

            <!-- Contact -->
            <?php echoInput('mail'); ?>

            <hr />

            <!-- Club -->
            <?php echoInput('nom_club'); ?>
            <?php echoInput('num_club'); ?>

            <hr />



            <!-- No-voile -->
            <?php echoLeftLabel('Nvoile'); ?>
            <?php echoInput('Cvoile'); ?>
            <?php echoInput('Nvoile'); ?>
            <br />

            <!-- Serie -->
            <?php echoInput('serie'); ?>
            <hr />

            <!-- Statut : Licence et AFL -->

            <?php echoInput('statut'); ?>

            <br />

            <?php echoInput('adherant'); ?>


            <br />

            <?php echoInput('lic'); ?>

            <?php echoInput('isaf_no'); ?>

            <br />

            <div id="licencie_ffv" class="msg">
            </div>

            <div id="non_licencie" class="msg">
            </div>

            <div id="etranger" class="msg">
            </div>

            <hr />

            <input type="submit" name="maSoumission" id="soumission" value="Valider"/>
        </fieldset>
    </form>
</div> <!-- formulaire -->
