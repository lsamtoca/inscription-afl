<?php

class Layouts {

// HTML PRE AND POST
    static function xhtml_pre1($title, $type = 'transitional', $base = '') {//Afficher le prefixe xhtml
        global $testing, $development, $config;

        $xhtmlStrict = "<!DOCTYPE html PUBLIC "
                . "\"-//W3C//DTD XHTML 1.0 Strict//EN\" "
                . "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";

        $xhtmlTransitional = "<!DOCTYPE html PUBLIC "
                . "\"-//W3C//DTD XHTML 1.0 Transitional//EN\" "
                . "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";

        if ($type == 'strict') {
            $docType = $xhtmlStrict;
        } else {
            $docType = $xhtmlTransitional;
        }
        if (
                ($testing and ( !$development)) ||
                preg_match("/Formulaire/", $_SERVER['REQUEST_URI']) == 1
        ) {
            $noRobots = "<meta name=\"robots\" content=\"noindex,nofollow\" />";
        } else {
            $noRobots = "<!-- robots allowed. Please index this -->\n"
                    . "<meta name=\"robots\" content=\"nofollow\" />";
        }
        if ($base == '') {
            $base = dirname($_SERVER['PHP_SELF']);
        }
        $favicon = "img/" . $config['favIcon'];

        echo "$docType
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
$noRobots
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<base href=\"$base/\" />
<link rel=\"STYLESHEET\" type=\"text/css\" href=\"css/style.css\" />
<link rel=\"STYLESHEET\" type=\"text/css\" href=\"css/localStyle.css\" />
<link rel=\"icon\" type=\"image/png\" href=\"$favicon\" />
<title>$title</title>\n";
    }

    static function lgtrick($image) {

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

    static function background() {
        global $config;

        if (
                isset($_GET['nobackground'])
        ) {
            echo "<div><img alt='' id='bg' style=\"background-color: cadetblue;\"/></div><!-- background -->" . "\n\n";
            return;
        }

        $backgroundImage = $config['defaultBackground'];

        self::lgtrick($backgroundImage);
//echo "<div><img src='$bgimage' alt='background image' id='bg' /></div><!-- background -->" . "\n\n";
    }

    static function xhtml_pre2($title) {//Afficher le prefixe xhtml
        global $config;
        $mainMsg = $config['titleMsg'];

        self::background();
        echo "</head>\n<body>\n\n";
        echo "<div id='content' class='white_over_dark' >\n\n";
        echo "<div id=\"title\"><h2>$mainMsg</h2></div>\n";
        echo "<h2>$title</h2>\n";
    }

    static function xhtml_pre($title) {//Afficher le prefixe xhtml
        xhtml_pre1($title);
        xhtml_pre2($title);
    }

    static function valid_html() {
        echo '<div id="validhtml">' . "\n";
        echo '[<a href="http://validator.w3.org/check/referer">html</a>]' . "\n";
        echo '</div><!-- validhtml -->' . "\n\n";
    }

    static function xhtml_post() {//Afficher le postfixe xhtml
        self::valid_html();
        echo "</div><!-- content -->\n\n";
        echo "</body>\n</html>\n";
    }

    static function goback() {
        $referer = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        }
        if (!$referer == '') {
            $goback = $referer;
        } else {
            $goback = "javascript:history.go(-1)";
        }
        return $goback;
    }

    static function echoGoBack() {
        $goback = goback();
        echo "<a href=\"$goback\">&laquo; Retour</a><h3>";
    }

    static function pageErreur($message, $goback = NULL) {

        if ($goback == NULL) {
            $goback = goback();
        }
        $messageHtml = str_replace("\n", "<br />\n", $message);

        xhtml_pre('Erreur');
        doMenu();

        echo '<div class="contenu smallform">' . "\n";
        echo "<p><span class=\"error_strings\">$messageHtml</span></p>";
        echo "<a href=\"$goback\">&laquo; Retour</a><h3>";
        echo "\n</div>";
        xhtml_post();

        exit(1);
    }

    static function pageErreurMail($goback = NULL) {
        $message = "Un problème avec l'evoi du courriel est survenu";
        self::pageErreur($message, $goback);
        exit(1);
    }

    static function pageAnswer($message, $goback = NULL, $title = 'Mission accomplie') {

        if ($goback == NULL) {
            $goback = goback();
        }
        $messageHtml = str_replace("\n", "<br />\n", $message);

        xhtml_pre($title);
        doMenu();
        echo '<div class="contenu smallform">' . "\n";
        echo "<p><span>$messageHtml</span></p>";
        echo "<a href=\"$goback\">&laquo; Retour</a><h3>";
        echo "\n</div>";
        xhtml_post();

        exit(0);
    }

    static function pageServerMisconfiguration($message) {
        xhtml_pre('Erreur');

        $messageHtml = str_replace("\n", "<br />\n", $message);
        echo '<h3>';
        echo "Server misconfiguration";
        echo '</h3>';
        echo "<p><span class=\"error_strings\">$messageHtml</span></p>";

        xhtml_post();

        exit(1);
    }

    static function redirect($message, $time, $gowhere) {
        echo $message;
        printf("<script type=\"text/javascript\">setTimeout('location=(\"%s\")' ,%d);</script>", $gowhere, $time);
    }


    static function requireJquery() {
        echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />' . "\n";
        echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>' . "\n";
        echo '<script src="//code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>."\n"';
    }

    static function requireJqueryValidations() {
        echo '<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>' . "\n";
    }

    static function requireJqueryDatePicker() {
        echo '<script src="js/ui.datepicker-fr.js" type="text/javascript"></script>' . "\n";
    }

    static function requireMyAccordion() {
        echo '<script src="js/myaccordion.js" type="text/javascript"></script>' . "\n";
        echo '<script type="text/javascript">' . "\n"
        . "\t$(document).ready(function () {\n"
        . "\t\tmyaccordion_set_accordion();\n"
        . "\t});\n"
        . "</script>\n";
    }

    static function  requireJqueryI18n(){
        echo '<script src="js/jquery.i18n.properties.js" type="text/javascript"></script>'."\m"; 
    }
    

}
