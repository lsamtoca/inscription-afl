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


function format_url_regate($id_regate){
  global $path_to_site_inscription;
  return sprintf("http://%sFormulaire.php?regate=%d",$path_to_site_inscription,$id_regate); 
}

function format_url_preinscrits($id_regate){
  global $path_to_site_inscription;
  return sprintf("http://%sPreinscrits.php?regate=%d",$path_to_site_inscription,$id_regate); 
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
<link rel=\"STYLESHEET\" type=\"text/css\" href=\"afl.css\" />
<title>$title</title>";
}

function background(){
  if(!isset($_GET['nobackground']))
  {
      ob_start();
      passthru('ls img/*.jpg',$ret_val);
      $listing=ob_get_contents();
      ob_end_clean();
      $images=explode("\n",trim($listing));
      
//       foreach($images as $img)
//         echo 'Cucu'. $img . "\n";      
//       echo sizeof($images) . "\n";

      $bg=$images[rand(0,sizeof($images)-1)];        
      
      echo "<div><img src='$bg' alt='background image' id='bg' /></div><!-- background -->"."\n\n";
      
   }
}

function xhtml_pre2($title){//Afficher le prefixe xhtml
  echo "</head>\n<body>\n\n";
  background();
  echo "<div id='content'>\n\n";
  echo "<h1>$title</h1>\n";
}

function xhtml_pre($title){//Afficher le prefixe xhtml
  xhtml_pre1($title);
  xhtml_pre2($title);
}


function valid_html(){
  echo '<div id="validhtml">'."\n";
  echo '[<a href="http://validator.w3.org/check/referer">html</a>]'."\n";
  echo '</div><!-- validhtml -->' ."\n\n" ;
}


function xhtml_post(){//Afficher le postfixe xhtml
  valid_html();
  echo "</div><!-- content -->\n\n";
  echo "</body>\n</html>\n";
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
  xhtml_post();
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
 
 
function my_quoted_printable_encode($input, $line_max = 75, $subject) {
        /**    The Quoted Printable encodes only with 75 characters per ligne.
        *    For encoding the subject of message, we must split the subject every 58 characters,
        *    because of adding =?iso-8859-1?Q? at the beginning and ?= at the end of each split.

            *    @param    string        $input            string to encode
            *    @param    int        $line_max            max number of char per ligne
            *    @param    string        $subject            specify if we encode a subject or any other string

            *    @access    public
            */
        $hex = array('0','1','2','3','4','5','6','7',
                            '8','9','A','B','C','D','E','F');
        $lines = preg_split("/(?:\r\n|\r|\n)/", $input);
        $linebreak = "\r\n";
        /* the linebreak also counts as characters in the mime_qp_long_line
        * rule of spam-assassin */
        $line_max = $line_max - strlen($linebreak);
        $escape = "=";
        $output = "";
        $cur_conv_line = "";
        $length = 0;
        $whitespace_pos = 0;
        $addtl_chars = 0;

        // iterate lines
        for ($j=0; $j<count($lines); $j++) {
            $line = $lines[$j];
            $linlen = strlen($line);

            // iterate chars
            for ($i = 0; $i < $linlen; $i++) {
                $c = substr($line, $i, 1);
                $dec = ord($c);

                $length++;

                if ($dec == 32) {
                    // space occurring at end of line, need to encode
                    if (($i == ($linlen - 1))) {
                        $c = "=20";
                        $length += 2;
                    }

                    $addtl_chars = 0;
                    $whitespace_pos = $i;
                } else if ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) {
                    $h2 = floor($dec/16); $h1 = floor($dec%16);
                    $c = $escape . $hex["$h2"] . $hex["$h1"];
                    $length += 2;
                    $addtl_chars += 2;
                }

                // length for wordwrap exceeded, get a newline into the text
                if ($length >= $line_max) {
                    $cur_conv_line .= $c;

                    // read only up to the whitespace for the current line
                    $whitesp_diff = $i - $whitespace_pos + $addtl_chars;

                    /* the text after the whitespace will have to be read
                    * again ( + any additional characters that came into
                    * existence as a result of the encoding process after the whitespace)
                    *
                    * Also, do not start at 0, if there was *no* whitespace in
                    * the whole line */
                    if (($i + $addtl_chars) > $whitesp_diff) {
                        if ($subject == "subject") {
                            $output .= "=?ISO-8859-1?Q?".substr($cur_conv_line, 0,
                                    (strlen($cur_conv_line) - $whitesp_diff))."?=";
                        } else {
                            $output .= substr($cur_conv_line, 0,
                                    (strlen($cur_conv_line) - $whitesp_diff)).$linebreak;
                        }
                        $i = $i - $whitesp_diff + $addtl_chars;
                    } else {
                    /* emit continuation --mirabilos */
                        if ($subject == "subject") {
                            $output .= "=?ISO-8859-1?Q?".$cur_conv_line."?=";
                        } else {
                            $output .= $cur_conv_line. '=' . $linebreak;
                        }
                    }

                    $cur_conv_line = "";
                    $length = 0;
                    $whitespace_pos = 0;
                } else {
                // length for wordwrap not reached, continue reading
                    $cur_conv_line .= $c;
                }
            } // end of for

            $length = 0;
            $whitespace_pos = 0;
            if ($subject == "subject") {
                $output .= "=?ISO-8859-1?Q?".$cur_conv_line."?=";
            } else {
                $output .= $cur_conv_line;
                if ($j<=count($lines)-1) {
                    $output .= $linebreak;
                }
            }
            $cur_conv_line = "";

            } // end for

        return trim($output);
    } // end my_quoted_printable_encode 

?>