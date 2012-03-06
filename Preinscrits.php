<?php

require "partage.php";

function cell($text){
  echo '<td>' .$text.'</td>
  ';
}

function rcell($text){
  echo '<td align="right">' .$text.'</td>
  ';
}


function affiche_serie($title,$serie){
    global $conn;


    $query="SELECT `nom`,`prenom`,`num_voile`,`prefix_voile` FROM `Inscrit` ";
    $condition=sprintf(" WHERE  `ID_regate` = '%s' ",$_GET['regate']);
    $condition.=' AND `conf`=\'1\' ';
    $condition.=" AND `serie`='$serie' ";
    $orderby=' ORDER BY `num_voile` ';
    $query.= $condition.$orderby;

    $res=mysql_query($query,$conn) or die('Problème lors de la réception des enregistrements '.$query);//Exécution de la requête

    if(mysql_num_rows($res) > 0)
    {
      echo "<div id=\"$serie\" style=\"margin-left:auto;margin-right:auto;width:200;margin-top:10mm;margin-bottom:10mm\">"."\n";
    
      echo '<table border="1" align="center">'."\n";
      echo '<tr><th colspan="5">'.$title.'</th></tr>';
      echo '<tr>';
      cell('');
      cell('Pays');
      cell('No. Voile');
      cell('Nom');
      cell('Prénom');
      echo '</tr>';

      $i=1;
      while($row=mysql_fetch_assoc($res))
      {
        echo '<tr>';
        rcell($i++);
        rcell($row['prefix_voile']);
        rcell($row['num_voile']);
        cell(nom_normaliser($row['nom']));
        cell(nom_normaliser($row['prenom']));
        echo '</tr>'."\n";
      }
    echo '</table>'."\n";
    
    echo "</div><!-- $serie -->"."\n\n";
    
    }
}


$conn=connect();

if(!isset($_GET['regate']))
{
  echo 'Il faut choisir une regate';
  html_post();
  exit;
  }
    
$query='SELECT * FROM `Regate` WHERE `ID_Regate`=\''.$_GET['regate'].'\'';
$res=mysql_query($query,$conn) or die('Problème lors de la réception des enregistrements '.$query);



if(mysql_num_rows($res)==0)
{
  xhtml_pre('Liste des préinscrits');
  echo 'Je n\'ai pas trouvé cette régate :-(';
  xhtml_post();
  exit;
  }

$row=mysql_fetch_assoc($res);

xhtml_pre('Liste des préinscrits à la régate');

echo '<h1>'.$row['titre'].'</h1>'."\n\n";

affiche_serie('Laser 4.7','LA4');
affiche_serie('Laser Radial','LAR');
affiche_serie('Laser Standard','LAS');

mysql_close($conn);


xhtml_post();
?>