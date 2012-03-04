<?php

error_reporting(-1);
ini_set('display_errors', '1');

require_once 'databases/bds.php';

if($_SERVER['HTTP_HOST'] == 'localhost') {
    $www_site='localhost/';
    $racine='~lsantoca/inscriptions_afl/';
}
else {
    $www_site='régateslaser.info/';
    $racine=basename(dirname(realpath(__FILE__))).'/';
}

if($racine=='inscriptions_afl_dev/' or $_SERVER['HTTP_HOST'] == 'localhost')
    $testing=true;
else
    $testing=false;

$path_to_site_inscription=$www_site.$racine;


// Activation des assertions et mise en mode discret
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

// Création d'un gestionnaire d'assertions
function my_assert_handler($file, $line, $code) {
    global $testing;
    if($testing) {
        echo "<hr>Assertion failure:
        File: $file<br />
        Line: $line<br />
        Code: $code<br /><hr />";
    }
    else {
        xhtml_pre("Server internal misconfiguration");
        xhtml_post();
        exit;
    }
}

// Configuration de la méthode de callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');


function format_url_regate($id_regate) {
    global $path_to_site_inscription;
    return sprintf("http://%sFormulaire.php?regate=%d",$path_to_site_inscription,$id_regate);
}

function format_url_preinscrits($id_regate) {
    global $path_to_site_inscription;
    return sprintf("http://%sPreinscrits.php?regate=%d",$path_to_site_inscription,$id_regate);
}

function format_confirmation_regate($id_coureur) {
    global $path_to_site_inscription;
    return sprintf("http://%sConfirmation.php?ID=%d",$path_to_site_inscription,$id_coureur);
}

function xhtml_pre1($title) {//Afficher le prefixe xhtml
    echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<link rel=\"STYLESHEET\" type=\"text/css\" href=\"afl.css\" />
<title>$title</title>";
}

function background() {
    if(!isset($_GET['nobackground'])) {
        ob_start();
        passthru('ls img/*.jpg',$ret_val);
        $listing=ob_get_contents();
        ob_end_clean();
        $images=explode("\n",trim($listing));

//       foreach($images as $img)
//         echo 'Cucu'. $img . "\n";      
//       echo sizeof($images) . "\n";

        //$bg=$images[rand(0,sizeof($images)-1)];
        $bg='img/background.jpg';

        echo "<div><img src='$bg' alt='background image' id='bg' /></div><!-- background -->"."\n\n";

    }
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
    echo '<div id="validhtml">'."\n";
    echo '[<a href="http://validator.w3.org/check/referer">html</a>]'."\n";
    echo '</div><!-- validhtml -->' ."\n\n" ;
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
    $con=connect();

    $requetes="";

    $sql=file($filesql); // on charge le fichier SQL

    foreach($sql as $l) { // on le lit
        if (substr(trim($l),0,2)!="--") { // suppression des commentaires
            $requetes .= $l;
        }
    }

    $reqs = split(";",$requetes);// on sépare les requêtes
    foreach($reqs as $req) {	// et on les éxécute
        if (!mysql_query($req,$con) && trim($req)!="") {
            die("ERROR : ".$req); // stop si erreur
        }


        echo '<quote>';
        echo $req;
        echo '</quote>';
        echo '<br>';
        echo '<br>';
    }

    mysql_close($con);

    echo "Fichier ".$filesql." executé.";

}


function nom_normaliser($nom) {
    $noms=explode(' ',$nom);

    $i=0;
    foreach($noms as $n) {
        $ns=explode('-',$n);
        $j=0;
        foreach($ns as $m)
            $ns[$j++]=ucwords(strtolower($m));
        $noms[$i++]=implode('-',$ns);
    }
    return implode(' ',$noms);

}

function clean_post_var($var) {
    if(get_magic_quotes_gpc())
        return stripslashes($var);
    else
        return $var;
}

function redirect($message,$time,$gowhere) {
    echo $message;
    printf("<script type=\"text/javascript\">setTimeout('location=(\"%s\")' ,%d);</script>",$gowhere,$time);
}

?>