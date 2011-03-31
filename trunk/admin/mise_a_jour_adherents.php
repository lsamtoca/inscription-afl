<?php
session_start();

require_once '../partage.php';
require_once 'excel_reader2.php';

/// Parametrage

// Fichier source de adherents
$url_de_Corinne_Jullion="http://www.lif.univ-mrs.fr/~lsantoca/test.xls/";
// Données sur la BD
$serveur="http://localhost//";
$utilisateur='cantoine';
$mdp='laser';
$bd='Inscriptions Afl';

$fichierxls="adherents.xls";
$fichiercsv="adherents.csv";

function formulaire(){
  echo '<form action="" method="POST">';
  echo '<label>Faire une copie de la BD de Corinne Jullion</label>';
  echo '<input type="submit" value="Go !!!">';
  echo '</form>';
}

function copie_adherents_in_bd_mysql(){

global $url_de_Corinne_Jullion,$fichierxls,$fichiercsv;
global $serveur,$utilisateur,$mdp,$nombd;

// Telecharger le fichier de CJ
copy($url_de_Corinne_Jullion,$fichierxls);

// Le transformer en .csv, 
// comme expliqué à
// http://www.ehow.com/how_7335821_convert-xls-csv-php.html

$xls = new Spreadsheet_Excel_Reader($fichierxls, false);
$csv = ''; 
$cols = $xsl->colcount();
$rows = $xls->rowcount();

for ($r = 1; $r <= $rows; $r++) { //go through each row of the spreadsheet
  for ($c = 1; $c <= $cols; $c++) { //go through each column
    $csv .= $xls->raw($r, $c); //get the raw data in the current cell.
  if ($c = $cols) { 
    $csv .= "\n"; //create a new line after we've finished this row
  }
  else 
 {
    $csv .= ","; //Put a comma between each item
  }

  }

} 

$fh = fopen($fichiercsv, 'w') or die("can't open file");
fwrite($fh, $csv);
fclose($fh);



// Faire une copie du csv dans la Base de donnés


$lien= mysql_connect($serveur,$utilisateur,$mdp);
mysql_select_db($nomdb);

$sql="LOAD DATA INFILE $fichiercsv INTO TABLE `Adherents`"; 
mysql_query($sql,$lien);
mysql_close($lien);

}


function byebye () {
  echo 'Terminé';
}


?>




<?php

html_pre('Mise à jour des adhérents AFL');

if(!isset($SESSION['action']))
  $SESSION['action']='start';
  
  switch($SESSION['action'])
 {
    case 'start':
      $SESSION['action']='copie';
      formulaire();
      break;
    case 'copie':
      copie_adherents_in_bd_mysql();
      byebye();
  }

html_post();
?>

