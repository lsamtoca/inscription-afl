<?php
  require "partage.php";
  html_pre("Régates ouvertes à l'inscription");
  
  try
  {
	// On se connecte à MySQL
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	//$bdd = new PDO('mysql:host=localhost;dbname=LASER', 'root', 'root', $pdo_options);
	$bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
	
	
	$sql = 'SELECT `ID_regate`,`titre`,`lieu`,
	 DATE_FORMAT(`date_debut`, \'%d-%m-%Y\') as `date_debut`,
	 DATE_FORMAT(`date_fin`, \'%d-%m-%Y\') as `date_fin` FROM `Regate` 
	 WHERE 1 order by `date_debut`';
	$req = $bdd->query($sql);
	
	
	echo '<ul>';
	while($row=$req->fetch()){
	 
	 echo "<li>";
	 
	 if($row['date_debut'] != "00-00-0000" and $row['date_fin'] != "00-00-0000")
	   printf("Du %s au %s : ",$row['date_debut'],$row['date_fin']);
	 printf("<a href=\"%s\">%s</a>",format_url_regate($row['ID_regate']),$row['titre']);
	 
	 if($row['lieu'] != "")
	   printf(" à %s",$row['lieu']);
     
     echo ".</li>\n";
     
	}
    echo '</ul>';


}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

  
  
  
  
  
  html_post();
?>