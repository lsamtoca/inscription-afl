<?php

//if(isset($partagephp)) exit(0);
//$partagephp=true;

error_reporting(-1);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Paris');

//$ip='/90\.34\.221\.[0-9]{1,3}/';
$ip = '/90\.34\.[0-9]{1,3}\.[0-9]{1,3}/';
//$ip='/82\.234\.226\.[0-9]{1,3}/';
$ua = '/MSIE/';

$remoteIp = $_SERVER['REMOTE_ADDR'];
$remoteUserAgent = $_SERVER['HTTP_USER_AGENT'];
$request = $_SERVER['REQUEST_URI'];
$date = date('Y-m-d H:i:s');
$separator = ',';

$logLine = "$date$separator$remoteIp$separator$request$separator$remoteUserAgent\n";
file_put_contents('connections.log', $logLine, FILE_APPEND | LOCK_EX);
;

//echo $_SERVER['REMOTE_ADDR'];
//echo $_SERVER['HTTP_USER_AGENT'];
//exit(0);

if (
        preg_match($ip, $_SERVER['REMOTE_ADDR']) === 1 &&
        preg_match($ua, $_SERVER['HTTP_USER_AGENT']) === 1
) {
    header('Location: http://www.mozilla.org/en-US/firefox/all/');
    exit(0);
}


require_once 'databases/bds.php';

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $www_site = 'localhost';
    $racine = dirname($_SERVER['REQUEST_URI']) . '/';
    $development = true;
} else {

    $development = false;

    // The one below is dangerous as it contains the accent. 
    // $www_site = 'régateslaser.info/';
    // echo $_SERVER['SERVER_NAME']; --- this with www.
    // this other without www.
    // echo $_SERVER['HTTP_HOST'];
    $www_site = $_SERVER['HTTP_HOST'] . '/';
    $racine = basename(dirname(realpath(__FILE__))) . '/';
}

if ($racine == 'inscriptions_afl_dev/' or $_SERVER['HTTP_HOST'] == 'localhost')
    $testing = true;
else
    $testing = false;

$path_to_site_inscription = $www_site . $racine;

// Activation des assertions et mise en mode discret
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

// Création du gestionnaire d'assertions
function my_assert_handler($file, $line, $code) {
    global $testing;
    if ($testing) {
        echo "<hr>Assertion failure:
        File: $file<br />
        Line: $line<br />
        Code: $code<br /><hr />";
    } else {
        xhtml_pre("Server internal misconfiguration");
        xhtml_post();
        exit;
    }
}

// Configuration de la méthode de callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');

function format_url_regate($id_regate, $gets = "") {
    global $path_to_site_inscription;
    if ($gets != "")
        $gets = "&gets";
    return sprintf("http://%sFormulaire.php?regate=%d%s", $path_to_site_inscription, $id_regate, $gets);
}

function format_url_forms($id_regate, $gets = "") {
    $url = format_url_regate($id_regate, $gets);
    return "$url#forms";
}

function format_url_preinscrits($id_regate, $gets = "") {
    $url = format_url_regate($id_regate, $gets);
    return "$url#preinscrits";
}

//function format_url_preinscrits($id_regate) {
//    global $path_to_site_inscription;
//    return sprintf("http://%sPreinscrits.php?regate=%d", $path_to_site_inscription, $id_regate);
//}

function format_confirmation_regate($id_coureur) {
    global $path_to_site_inscription;
    return sprintf("http://%sConfirmation.php?ID=%d", $path_to_site_inscription, $id_coureur);
}

function xhtml_pre1($title) {//Afficher le prefixe xhtml
    echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
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
    passthru('ls img/*.jpg', $ret_val);
    $listing = ob_get_contents();
    ob_end_clean();
    $images = explode("\n", trim($listing));
    $bgimage = $images[rand(0, sizeof($images) - 1)];
    //          $bg='img/background.jpg';

    lgtrick($bgimage);
    //echo "<div><img src='$bgimage' alt='background image' id='bg' /></div><!-- background -->" . "\n\n";
}

function xhtml_pre2($title) {//Afficher le prefixe xhtml
    echo "</head>\n<body>\n\n";
    background();
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

function execute_sql($filesql) {
    $con = connect();

    $requetes = "";

    $sql = file($filesql); // on charge le fichier SQL

    foreach ($sql as $l) { // on le lit
        if (substr(trim($l), 0, 2) != "--") { // suppression des commentaires
            $requetes .= $l;
        }
    }

    $reqs = split(";", $requetes); // on sépare les requêtes
    foreach ($reqs as $req) { // et on les éxécute
        if (!mysql_query($req, $con) && trim($req) != "") {
            die("ERROR : " . $req); // stop si erreur
        }


        echo '<quote>';
        echo $req;
        echo '</quote>';
        echo '<br>';
        echo '<br>';
    }

    mysql_close($con);

    echo "Fichier " . $filesql . " executé.";
}

function nom_normaliser($nom) {
    $noms = explode(' ', $nom);

    $i = 0;
    foreach ($noms as $n) {
        $ns = explode('-', $n);
        $j = 0;
        foreach ($ns as $m)
            $ns[$j++] = ucwords(strtolower($m));
        $noms[$i++] = implode('-', $ns);
    }
    return implode(' ', $noms);
}

function clean_post_var($var) {
    if (get_magic_quotes_gpc())
        return stripslashes($var);
    else
        return $var;
}

function redirect($message, $time, $gowhere) {
    echo $message;
    printf("<script type=\"text/javascript\">setTimeout('location=(\"%s\")' ,%d);</script>", $gowhere, $time);
}

function self() {
    return $_SERVER['PHP_SELF'] . "?regate=" . $_GET['regate'];
}

// Definition des constantes
//define('HIDEMAILSTRING', str_repeat('*', 8));
// Errors

function pageErreur($message, $goback = NULL) {

    xhtml_pre('Erreur');
    $messageHtml = str_replace("\n", "<br />\n", $message);

    echo "<p><span class=\"error_strings\">$messageHtml</span></p>";

    if ($goback != NULL) {
        echo "Retourner à la page <a href=\"$goback\">$goback</a><h3>";
    } else {
        echo "<A HREF=\"javascript:javascript:history.go(-1)\">Retourner à la page precedente</A>";
    }

    xhtml_post();

    exit(1);
}

function pageAnswer($message) {

    xhtml_pre('Mission accomplie');

    $messageHtml = str_replace("\n", "<br />\n", $message);

    echo "<p><span class=\"error_strings\">$messageHtml</span></p>";

    echo "<A HREF=\"javascript:javascript:history.go(-1)\">Retourner à la page precedente</A>";

    xhtml_post();

    exit(0);
}

function pageServerMisconfiguration($message) {
    xhtml_pre('Erreur');

    echo '<h3>';
    echo "Server misconfiguration : $message";
    echo '</h3>';

    xhtml_post();

    exit(1);
}

/* Fonctions pour formatter les dates */

function dateReformatJqueryToMysql($string) {
    list($day, $month, $year) = sscanf($string, '%02d/%02d/%04d');
    return sprintf('%d-%d-%d', $year, $month, $day);
}

function dateReformatMysqlToJquery($string) {
    list($year, $month, $day) = sscanf($string, '%04d-%02d-%02d');
    return sprintf('%02d/%02d/%04d', $day, $month, $year);
}

function dateReformatDbfToJquery($string) {
    list($year, $month, $day) = sscanf($string, '%04d%02d%02d');
    return sprintf('%02d/%02d/%04d', $day, $month, $year);
}

/* Tests 
 * 
 */
//echo dateReformatMysqlToJquery('1967-11-13');
?>