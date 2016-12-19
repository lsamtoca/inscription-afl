<?php
xhtml_pre('A propos de ce programme');
doMenu();

//$debugger = new debugger();
//$debugger->config();

?>
<div class="contenu" style="padding:20pt;font-size:120%">
    <h3>WebRegatta 4.0</h3>

    
    <?php
    if ($config['moduleDonate']) {
        include('code/About/boutonPayPal.php');
    }
    include('code/About/news.php');
    echoGoBack();
    ?>

</div>
<?php
xhtml_post();
