<?php

require "externals/PHPExcel/Classes/PHPExcel.php";

$fields = array(
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
    'statut' => 'Statut coureur',
    'num_lic' => 'Licence no.',
    'isaf_no' => 'No. ISAF',
    'nom_club' => 'Club',
    'num_club' => 'Club no.',
    'adherant' => 'Adhérant AFL (1=ou, 0=non)',
    'taille_polo' => 'Taille polo (à ignorer)',
    //
    'conf' => 'Confirmé',
    'date preinscription' => 'Date préinscription',
    'date confirmation' => 'Date confirmation',
);


function generate_excel() {

    global $fields;

    $excel = new PHPExcel();
    $excel->getProperties()->setCreator("Luigi Santocanale")
            ->setLastModifiedBy("Luigi Santocanale")
            ->setTitle("Liste des inscrits à ma regate");


    if (isset($_GET['confirme']) and $_GET['confirme'] == '1') {
        $postfix = ' AND `conf`=\'1\'';
    } else {
        $postfix = "";
    }    $sql = "SELECT * FROM Inscrit WHERE (ID_regate =:ID_REGATE)$postfix";
    $assoc = array('ID_REGATE' => $_SESSION['ID_regate']);
    $req = executePreparedQuery($sql, $assoc);

    $row_no = 1;
    $column_no = 65;

    //Header
    $i = 0;
    foreach ($fields as $key => $value) {
        $excel->setActiveSheetIndex(0)
                ->setCellValue(chr($column_no) . $row_no, $value);
        $column_no++;
    }

    // Content
    $row_no++;
    while ($row = $req->fetch()) {//Parcours du résultat de la requête
        $column_no = 65; //C'est ici qu'on va jouer sur les codes ascii

        foreach ($fields as $field => $value) {
            $excel->setActiveSheetIndex(0)
                    ->setCellValue(chr($column_no) . $row_no, $row[$field]);
            $column_no++;
        }
        /* Ce code obselete ... mais il sert à quoi ? */
        /*
        foreach($row as $key => $value)
          {
          $excel->setActiveSheetIndex(0)
          ->setCellValue(chr($column_no).$row_no,$value);
          $column_no++;
          }
          */
        
        $row_no++;
    }

    return $excel;
}

$excel = generate_excel();

// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="liste_inscrits.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$objWriter->save('php://output');
exit(0);
