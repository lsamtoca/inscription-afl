<?php

error_reporting(-1);
ini_set('display_errors', '1');

// Le fichier suivant, à placer dans un endroit protegé, definit les variables
// $host, $user, $pwd, $db, et $pdo_path="mysql:host=$host;dbname=$db"
// ainsi que la fonction génerique connect()

// require "/home/lsantoca/public_html/basedesnoms/.AFLdb.php";
// $path_to_site_inscription="www.cmi.univ-mrs.fr/~lsantoca/inscription_afl/";
// $racine="/home/lsantoca/public_html/inscription_afl/";

$unix_base="/homez.462/xnrgates/www/";
require "$unix_base"."basedesnoms/.AFLdb.php";

$www_site="régateslaser.info/";
$racine="inscriptions_afl/";
$path_to_site_inscription="$www_site"."$racine";


function format_url_regate($id){
  global $path_to_site_inscription;
  return sprintf("http://%sFormulaire.php?ID=%d",$path_to_site_inscription,$id); 
}

function format_confirmation_regate($id_coureur){
  global $path_to_site_inscription;
  return sprintf("http://%sConfirmation.php?ID=%d",$path_to_site_inscription,$id_coureur); 
}

function xhtml_pre1($title){//Afficher le prefixe xhtml
echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<link rel=\"STYLESHEET\" type=\"text/css\" href=\"afl.css\" >
<title>$title</title>";
}

function xhtml_pre2($title){//Afficher le prefixe xhtml
echo "
</head>
<body>
<h1>$title</h1>
";
}

function xhtml_pre($title){//Afficher le prefixe xhtml
  xhtml_pre1($title);
  xhtml_pre2($title);
}

function xhtml_post(){//Afficher le postfixe xhtml
echo "
</body>
</html>
";
}

function html_pre($title){ // Afficher le prefixe html

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

function html_post(){ // Afficher le postfixe html
echo "
</body>
</html>
"
;
}

function execute_sql($filesql){
  $con=connect();

  $requetes="";
 
  $sql=file($filesql); // on charge le fichier SQL

  foreach($sql as $l){ // on le lit
	if (substr(trim($l),0,2)!="--"){ // suppression des commentaires
		$requetes .= $l;
	}
  }
 
  $reqs = split(";",$requetes);// on sépare les requêtes
  foreach($reqs as $req){	// et on les éxécute
	if (!mysql_query($req,$con) && trim($req)!=""){
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
 
?>