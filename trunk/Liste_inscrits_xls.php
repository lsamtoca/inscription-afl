<?php
session_start();
if(!isset($_SESSION["ID_regate"]))
{
		header("Location: LoginClub.php");
}

require "partage.php";
require "classes/PHPExcel.php";

//define("FILENAME",sprintf("%sinscrits.xlsx",$racine)); //constante: nom du fichier à générer

$fields=array(
  'nom' => 'Nom',
  'prenom' => 'Prénom',
  //
  'prefix_voile' => 'Code pays',
  'num_voile' => 'Numero voile',
  'serie' => 'Série',
  //    
  'naissance' => 'Date de naissance',
  'sexe' => 'Sexe',
  'mail' => 'Courriel',
  //
  'statut' =>'Statut coureur',
  'num_lic' => 'Licence no.',
  'isaf_no' => 'No. ISAF',
  'nom_club' => 'Club',
  'num_club' => 'Club no.',
  'adherant' => 'Adhérant AFL (1=ou, 0=non)',
  //
  'conf' => 'Confirmé'
);

function generate_excel(){

    global $fields;
    
    $excel=new PHPExcel();
	$excel->getProperties()->setCreator("Luigi Santocanale")
							 ->setLastModifiedBy("Luigi Santocanale")
							 ->setTitle("Liste des inscrits à ma regate");

    $condition=sprintf("WHERE  `ID_regate` = '%s'",$_SESSION['ID_regate']);
    if(isset($_GET['confirme']) and $_GET['confirme']=='1')
        $condition.=' AND `conf`=\'1\'';
    $query="SELECT * FROM `Inscrit` " .$condition;
    
    $conn=connect();
    $res=mysql_query($query,$conn) or die('Problème lors de la réception des enregistrements '.$query);//Exécution de la requête


    $row_no=1;
    $column_no=65;

    $i = 0; 
//     while ($i < mysql_num_fields($res)) { 
//       $meta = mysql_fetch_field($res, $i); 
//         
//        $excel->setActiveSheetIndex(0)
//  		    ->setCellValue(chr($column_no+$i).$row_no,$meta->name);
// 
//       $i++; 
//     }  

	foreach($fields as $key => $value)
    {
		  	$excel->setActiveSheetIndex(0)
		                ->setCellValue(chr($column_no).$row_no,$value);
		    $column_no++;
    }

	$row_no++;
	while($row=mysql_fetch_assoc($res)){//Parcours du résultat de la requête
		
		$column_no=65;//C'est ici qu'on va jouer sur les codes ascii
		
	    foreach($fields as $field => $value)
		{
		  	$excel->setActiveSheetIndex(0)
		                ->setCellValue(chr($column_no).$row_no,$row[$field]);
		    $column_no++;
		}
/*		foreach($row as $key => $value)
		{
		  	$excel->setActiveSheetIndex(0)
		                ->setCellValue(chr($column_no).$row_no,$value);
		    $column_no++;
		}*/
		$row_no++;
	}

  mysql_close($conn);
  return $excel;
}


$excel=generate_excel();

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="liste_inscrits.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$objWriter->save('php://output');
exit;



?>