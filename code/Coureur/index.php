<?php
// TODO : is thi script used anymore
$title = "Inscription aux régates de l'AFL";
Layouts::xhtml_pre1($title);
Layouts::requireJquery();
Layouts::requireMyAccordion();
Layouts::xhtml_pre2($title); 
doMenu();
?>

<!-- Vous etes -->

<div id="accordion">

    <h3>Coureur</h3>
    <div class="contenu">
        <ul>
            <li>Consultez la <A href="Liste_regates.php">liste des régates</A>  ouvertes à l'inscription. 
            </li>
        </ul>
    </div>

    <h3>Club de voile</h3>
    <div class="contenu">
        <ul>
            <li>Gérez <A href="Regate.php">votre régate</A>. </li>
        </ul>

    </div>
    <!--
    <h3>Administrateur AFL :</h3>
    
    <ul>
       <li> Gérez  <A href="Admin.php">les événements et les clubs</A>. </li>
    </ul>
    -->

</div>

<?php
xhtml_post();
?>