<?php
global $listings;
$divisor = "\n<tr class='divisor'><td></td></tr>\n\n";
$tableContent = implode($divisor, $listings);
?>

<div class="contenu">
    <div style="margin-left:auto;margin-right:auto;margin-top:10mm;margin-bottom:10mm">

        <table border="0" align="center">
            <?php echo $tableContent; ?>
        </table>

    </div>
</div><!-- pré-inscrits -->
