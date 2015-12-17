<?php

// HTML PRE AND POST
function xhtml_pre1($title,$type='transitional') {//Afficher le prefixe xhtml
    
    $xhtmlStrict="<!DOCTYPE html PUBLIC "
            ."\"-//W3C//DTD XHTML 1.0 Strict//EN\" "
            . "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
    
    $xhtmlTransitional="<!DOCTYPE html PUBLIC "
            ."\"-//W3C//DTD XHTML 1.0 Transitional//EN\" "
            ."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";

    if($type=='strict'){
        $docType=$xhtmlStrict;
    }else{
        $docType=$xhtmlTransitional;
    }
    
    echo "$docType
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta name=\"robots\" content=\"noindex,nofollow\" />
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<link rel=\"STYLESHEET\" type=\"text/css\" href=\"afl.css\" />
<link rel=\"icon\" type=\"image/png\" href=\"img/favicon.png\" />
<title>$title</title>";
}

function lgtrick($image) {

    echo "<style type='text/css'>\n";
    echo "<!--\n"
    . "body {\n"
    . "\tmargin:0;\n"
    . "\tpadding:0;\n"
    . "\tbackground: url('$image') no-repeat center fixed;\n"
    . "\t-webkit-background-size: cover; /* pour Chrome et Safari */\n"
    . "\t-moz-background-size: cover; /* pour Firefox */\n"
    . "\t-o-background-size: cover; /* pour Opera */\n"
    . "\tbackground-size: cover; /* version standardisée */\n"
    . "}\n"
    . "-->\n"
    . "</style>\n";
}

function background() {

    if (
            isset($_GET['nobackground'])
    ) {
        echo "<div><img alt='' id='bg' style=\"background-color: cadetblue;\"/></div><!-- background -->" . "\n\n";
        return;
    }

    ob_start();
    passthru('ls img/backgrounds/*.jpg', $ret_val);
    $listing = ob_get_contents();
    ob_end_clean();
    $images = explode("\n", trim($listing));
    $bgimage = $images[rand(0, sizeof($images) - 1)];
    //          $bg='img/background.jpg';

    lgtrick($bgimage);
    //echo "<div><img src='$bgimage' alt='background image' id='bg' /></div><!-- background -->" . "\n\n";
}

function xhtml_pre2($title) {//Afficher le prefixe xhtml
    background();
    echo "</head>\n<body>\n\n";
    echo "<div id='content' class='white_over_dark' >\n\n";
    echo "<h1>$title</h1>\n";
}

function xhtml_pre($title) {//Afficher le prefixe xhtml
    xhtml_pre1($title);
    xhtml_pre2($title);
}

function valid_html() {
    echo '<div id="validhtml">' . "\n";
    echo '[<a href="http://validator.w3.org/check/referer">html</a>]' . "\n";
    echo '</div><!-- validhtml -->' . "\n\n";
}

function xhtml_post() {//Afficher le postfixe xhtml
    valid_html();
    echo "</div><!-- content -->\n\n";
    echo "</body>\n</html>\n";
}

function html_pre($title) { // Afficher le prefixe html
    echo "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">

<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" >
<link rel=\"STYLESHEET\" type=\"text/css\" href=\"afl.css\" >
<title>
    $title
</title>
    ";

// Pour la feuille de style
// echo "<link rel=\"STYLESHEET\" type=\"text/css\" href=\"../../active.css\" >";

    echo "</head>
<body>
<h1>$title</h1>
    "
    ;
}

function html_post() { // Afficher le postfixe html
    xhtml_post();
}

function pageErreur($message, $goback = NULL) {

    xhtml_pre('Erreur');
    $messageHtml = str_replace("\n", "<br />\n", $message);

    echo "<p><span class=\"error_strings\">$messageHtml</span></p>";

    if ($goback != NULL) {
        echo "Retourner à la page <a href=\"$goback\">$goback</a><h3>";
    } else {
        $referer = $_SERVER['HTTP_REFERER'];
        if (!$referer == '') {
            echo '<p><a href="' . $referer . '" title="Return to the previous page">&laquo; Retour</a></p>';
        } else {
            echo '<p><a href="javascript:history.go(-1)" title="Return to the previous page">&laquo; Retour</a></p>';
        }
        //  echo "<A HREF=\"javascript:javascript:history.go(-1)\">Retourner à la page precedente</A>";
    }

    xhtml_post();

    exit(1);
}

function pageAnswer($message, $title = 'Mission accomplie') {

    xhtml_pre($title);

    $messageHtml = str_replace("\n", "<br />\n", $message);

    echo "<p><span class=\"error_strings\">$messageHtml</span></p>";

    echo "<A HREF=\"javascript:javascript:history.go(-1)\">Retourner à la page precedente</A>";

    xhtml_post();

    exit(0);
}

function pageServerMisconfiguration($message) {
    xhtml_pre('Erreur');
    $messageHtml = str_replace("\n", "<br />\n", $message);

    echo '<h3>';
    echo "Server misconfiguration";
    echo '</h3>';
    echo "<p><span class=\"error_strings\">$messageHtml</span></p>";

    xhtml_post();

    exit(1);
}

function redirect($message, $time, $gowhere) {
    echo $message;
    printf("<script type=\"text/javascript\">setTimeout('location=(\"%s\")' ,%d);</script>", $gowhere, $time);
}

/* This is not used anywhere
function self() {
    return $_SERVER['PHP_SELF'] . "?regate=" . $_GET['regate'];
}
*/

