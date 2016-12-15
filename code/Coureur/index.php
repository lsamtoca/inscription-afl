<?php
// TODO : is thi script used anymore
$title = "Inscription aux régates de l'AFL";
xhtml_pre1($title);
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
<script src="js/myaccordion.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function () {
        myaccordion_set_accordion();
        $('#accordion').accordion({
            active: 0
        });
    });

</script>

<?php xhtml_pre2($title); 

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