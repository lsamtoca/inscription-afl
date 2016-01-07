<?php

// HTML PRE AND POST
function xhtml_pre1($title, $type = 'transitional') {//Afficher le prefixe xhtml
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
    
    $base=dirname($_SERVER['PHP_SELF']);
    echo "$docType
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta name=\"robots\" content=\"noindex,nofollow\" />
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<base href=\"$base/\" />
<link rel=\"STYLESHEET\" type=\"text/css\" href=\"afl.css\" />
<link rel=\"icon\" type=\"image/png\" href=\"img/favicon.png\" />
<title>$title</title>\n";
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
    echo "<div id=\"title\"><h2>Pré-inscriptions aux régates de l'AFL</h2></div>\n";
    echo "<h2>$title</h2>\n";
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

function goback() {
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

function pageErreur($message, $goback = NULL) {

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

function pageAnswer($message, $goback = NULL, $title = 'Mission accomplie') {

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

function echoMenu($choices) {
    echo '<div id="deconnexion" class="white_over_dark">' . "\n";
    echo '<ul>' . "\n";
    foreach ($choices as $choice) {
        $msg = $choice['message'];
        $link = '';
        if (isset($choice['link'])) {
            $link = $choice['link'];
        }
        echo '<li>';
        if ($link != '') {
            echo "<a href=\"$link\">";
        } else {
            echo '<a>';
        }
        echo $msg;
        echo '</a></li>';
    }
    echo '</ul>' . "\n";
    echo '</div><!--menu-->';
}

$menuItem_Home = array('message' => '<span id="home" class="msg">Accueil</span>', 'link' => 'index');
$menuItem_Club = array('message' => 'Accueil Club', 'link' => 'Regate');
$menuItem_Admin = array('message' => 'Accueil Administrateur', 'link' => 'Admin');
$menuItem_Login = array('message' => 'Connexion', 'link' => 'Login');
$menuItem_Logout = array('message' => 'Deconnexion', 'link' => 'Logout');
$menuItem_Language = array('message' => '<span id="lang" class="msg"></span>');
$menuItem_ChPwd = array('message' => 'Modifiez le mot de passe', 'link' => 'changePwd');

$menuUsual = array($menuItem_Home, $menuItem_Login);
$menuLanguage = array($menuItem_Home, $menuItem_Language);
$menuHome = array($menuItem_Home);
$menuHomeClub = array($menuItem_Home,
    $menuItem_Club,
    $menuItem_ChPwd,
    $menuItem_Logout
);
$menuHomeAdmin = array(
    $menuItem_Home,
    $menuItem_Admin,
    $menuItem_ChPwd,
    $menuItem_Logout);

function doMenu($menu = array()) {
    global $menuHomeAdmin, $menuHomeClub, $menuHome,$menuUsual;

    if (count($menu) == 0) {
        $login = new Login();
        if ($login->adminCorrectlyLogged()) {
            $menu = $menuHomeAdmin;
        } elseif ($login->clubCorrectlyLogged()) {
            $menu = $menuHomeClub;
        } else {
            $menu = $menuUsual;
        }
    }
    echoMenu($menu);
}

/* This is not used anywhere
function self() {
    return $_SERVER['PHP_SELF'] . "?regate=" . $_GET['regate'];
}
*/

