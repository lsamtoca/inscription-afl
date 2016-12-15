<div class="contenu">
    Imprimer les fiches d'enregistrement des participants :
    <ol> 
        <?php
        foreach ($regate['series'] as $serie):
            ?>
            <li><a href="accueil_participants?serie=<?php echo $serie['nom']; ?>"><?php echo $serie['nomLong']; ?></a></li>
        <?php endforeach; ?>
        <!--
                <li><a href="accueil_participants?serie=LA4">Laser 4.7</a></li>
                <li><a href="accueil_participants?serie=LAR">Laser radial</a></li>
                <li><a href="accueil_participants?serie=LAS">Laser standard</a></li>
        -->
    </ol>
</div>
