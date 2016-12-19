<?php

global $config;

$regate = Regate_selectById($_SESSION['ID_regate']);
//$series = array_keys($regate['series']);

if (
        !isset($_GET['serie'])
        or ( !isset($regate['series'][$_GET['serie']]))
) {
    $series = implode('/', array_keys($regate['series']));
    pageErreur("Il faut spécifier une série ($series)");
    exit(1);
}


require_once 'externals/PHPExcel/Classes/PHPExcel.php';
require_once 'php/mailer.php';

$spaces = str_repeat(' ', 30);
$faita = "Fait à $spaces le $spaces ,";
$engagement = 'Je m\'engage à me soumettre aux Règles de Course à la Voile et à toutes autres règles qui régissent cette épreuve. '
        . 'Il appartient à chaque coureur, sous sa seule responsabilité, de décider s’il doit prendre le départ.' . "\n\n"
        . $faita . "\n\n"
        . 'Signature :' . "\n\n";

$decl_parentale = "Déclaration parentale.\n\n"
        . 'Je soussigné, M. ' . $spaces
        . ', autorise mon enfant %s '
        . 'à participer à la régate `' . $_SESSION['titre_regate']
        . '\' et dégage la responsabilité des organisateurs quant aux risques inhérents à cette participation.' . "\n\n"
        . $faita . "\n\n"
        . 'Signature de l’un des parents (mention nécessaire écrite : Bon pour autorisation parentale) :' . "\n\n";

// Dates
function est_enfant($string_naissance, $string_regate) {
    $date_naissance = new DateTime($string_naissance);
    $date_regate = new DateTime($string_regate);
    $date_naissance->modify('+18 years');
    return ($date_naissance > $date_regate);
}

// Excel stuff
function set($value) {//
    global $excel, $column_no, $line_no;

    $excel->getActiveSheet()
            ->setCellValue(chr($column_no++) . $line_no, $value);
}

function new_line() {
    global $column_no, $line_no;
    $line_no++;
    $column_no = 65; //A
}

$style_Form_Title = array(
    'font' => array(
        'bold' => true,
        'size' => 18,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ),
    'borders' => array(
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK,
        ),
    ),
);

$style_Justify = array(
    /* 	'font' => array(
      'bold' => true,
      ), */
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
    ),
    'borders' => array(
        'inside' => array(
            'style' => PHPExcel_Style_Border::BORDER_NONE,
        ),
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_NONE,
        ),
    ),
);

$style_Center = array(
    /* 	'font' => array(
      'bold' => true,
      ), */
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
    ),
        /* 	'borders' => array(
          'inside' => array(
          'style' => PHPExcel_Style_Border::BORDER_NONE,
          ),
          'outline' => array(
          'style' => PHPExcel_Style_Border::BORDER_NONE,
          ),
          ), */
);

$style_Empty = array(
    'borders' => array(
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_NONE,
        ),
        'inside' => array(
            'style' => PHPExcel_Style_Border::BORDER_NONE,
        ),
    ),
);

// Données du formulaire
$donnees = array(
    'Serie' => 'serie',
    'Nom/Surname' => 'nom',
    'Prénom/Name' => 'prenom',
//    'Confirmé' => 'conf',
    'Pays/Country' => 'prefix_voile',
    'No voile/Sail number' => 'num_voile',
    'No licence' => 'num_lic',
    'No ISAF/ISAF id' => 'isaf_no',
//    'AFL' => 'adherant',  
//    'AFL OK ?' => '',
//    'Déclaration' => '',
//    'Signature' => '',
//    'Autorisation parentale'=> '',
);

if ($config['whoAmI'] == 'AFL') {
    $donnees_sur_lieu = array(
        "Carte publicité\n(oui/non)" => '',
        "No tél à rejoindre\nen cas de besoin" => '',
        "Repas\n(oui/non)" => '',
        'Visa médical OK ?' => '',
        'Licence OK ?' => '',
        'AFL OK ?' => '',
        'Carte ILCA OK ?' => '',
    );
} else {
    $donnees_sur_lieu = array(
        "Carte publicité\n(oui/non)" => '',
        "No tél à rejoindre\nen cas de besoin" => '',
        "Repas\n(oui/non)" => '',
        'Visa médical OK ?' => '',
        'Licence OK ?' => '',
        'Association à la classe OK ?' => '',
        'Carte ILCA OK ?' => '',
    );
}

function do_form($participant) {
    global $engagement, $decl_parentale, $donnees, $donnees_sur_lieu;
    global $excel, $line_no, $sheet_no;
    global $style_Center, $style_Justify, $style_Form_Title, $style_Empty;


    $excel->createSheet();
    $excel->setActiveSheetIndex($sheet_no++);
    $sheet = $excel->getActiveSheet();

    $nom0 = $participant['nom'];
    $prenom0 = $participant['prenom'];
    $nom = nom_normaliser($nom0);
    $prenom = nom_normaliser($prenom0);

    $invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');
    $sheetTitle = str_replace($invalidCharacters, '', $nom . ' ' . $prenom);
    try {
        $sheet->setTitle($sheetTitle);
    } catch (Exception $e) {
        throw new Exception($e . "\nProblem with title $sheetTitle");
    }

    //   $column_no = 65; //A
    $line_no = 1; //1
// PAGE options
    $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    $sheet->getPageSetup()->setFitToWidth(1);
    $sheet->getPageSetup()->setFitToHeight(1);
    $sheet->getPageSetup()->setHorizontalCentered(true);
    $sheet->getPageSetup()->setVerticalCentered(true);
//
//    $sheet->setShowGridlines(false);
    $sheet->getPageMargins()->setTop(1);
    $sheet->getPageMargins()->setRight(0.75);
    $sheet->getPageMargins()->setLeft(0.75);
    $sheet->getPageMargins()->setBottom(1);

    $sheet->mergeCells('A' . $line_no . ':G' . $line_no);
    $sheet->getStyle('A' . $line_no . ':G' . $line_no)->applyFromArray($style_Empty);

    new_line();


// Title
    $titre = $nom . " " . $prenom;
    if ($participant['conf'] == 0) {
        $titre.=" (pas confirmé)";
    }
    set($titre);

    $sheet->mergeCells('A' . $line_no . ':G' . $line_no);
    $sheet->getRowDimension($line_no)->setRowHeight(40);
    $sheet->getStyle('A' . $line_no . ':G' . $line_no)->applyFromArray($style_Form_Title);

    new_line();

    $first_line = $line_no;
    new_line();
//      set('');
    foreach ($donnees as $key => $value)
        set($key);
    $sheet->getRowDimension($line_no)->setRowHeight(20);

    new_line();
//      set('');
    foreach ($donnees as $key => $value)
        set($participant[$value]);
    $sheet->getRowDimension($line_no)->setRowHeight(30);

    new_line();

    set('Corrections : ');
    $sheet->getRowDimension($line_no)->setRowHeight(30);
    new_line();

//      set('');
    foreach ($donnees_sur_lieu as $key => $value) {
        /*        $range=chr($column_no).$line_no.':'.chr($column_no).($line_no +1);
          //echo $range;
          $sheet->mergeCells($range); */
        set($key);
    }
    $sheet->getRowDimension($line_no)->setRowHeight(30);

    new_line();

    set('');
    $sheet->getRowDimension($line_no)->setRowHeight(30);
    $sheet->getStyle('A' . $first_line . ':G' . $line_no)->applyFromArray($style_Center);

    new_line();
    $first_line = $line_no;

    set('Déclaration : ');
    set($engagement);
    $sheet->mergeCells('B' . $line_no . ':G' . $line_no);
    $sheet->getStyle('B' . $line_no)->getAlignment()->setWrapText(true);
    $sheet->getRowDimension($line_no)->setRowHeight(120);

    // Gestion des enfants ...
    if (est_enfant($participant['naissance'], $_SESSION['debut_regate'])) {
        new_line();
        set("Déclaration\nparentale OK ?\n(Sinon, faire compléter\nla déclaration à droite)");

        set(sprintf($decl_parentale, $participant['nom'] . " " . $participant['prenom']));
        $sheet->mergeCells('B' . $line_no . ':G' . sprintf("%s", $line_no + 1));
        $sheet->getStyle('B' . $line_no)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($line_no)->setRowHeight(55);
        new_line();
        set('');
        $sheet->getRowDimension($line_no)->setRowHeight(75);
    }

    $sheet->getStyle('A' . $first_line . ':A' . $line_no)->applyFromArray($style_Center);

    $sheet->getStyle('B' . $first_line . ':G' . $line_no)->applyFromArray($style_Justify);
//      $sheet->getStyle('A'.$first_line.':A'.$line_no)->applyFromArray($style_Form_Left);
// SIZE OF COLUMNS
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
}

function make_xls($ID_regate, $serie) {

    $sql = 'SELECT *,CONCAT(`prefix_voile`,`num_voile`) as `voile` '
            . 'FROM Inscrit WHERE (ID_regate =?) AND (serie=?) ORDER BY nom';
    $assoc = array($ID_regate, $serie);
    $req = executePreparedQuery($sql, $assoc);

    $exceptionsrecues = '';
    while ($row = $req->fetch()) {
        try {
            do_form($row);
        } catch (Exception $e) {
            $exceptionsrecues.= 'Exception reçue : ' . $e->getMessage() . "\n";
            //$exceptionsrecues.= "with row :\n" . serialize($row) . "\n\n";
        }
    }
    if ($exceptionsrecues != '') {
        $to = $sender = 'luigi.santocanale@lif.univ-mrs.fr';
        $subject = 'Problem with PhpExcel in inscriptions AFL';
        $message = $exceptionsrecues;
        send_mail_text($sender, $to, $subject, $message);
    }
    $req->closeCursor();
}

// Creer une nouvelle feuille de calcul
$serie = $_GET['serie'];

$excel = new PHPExcel();
$excel->getProperties()->setTitle($_SESSION['titre_regate'] . ", série $serie");

$column_no = 65; //A
$line_no = 1; //1
$sheet_no = 0;

make_xls($_SESSION["ID_regate"], $serie);

header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment;filename=\"participants_$serie.xls\"");
//header('Content-Type: application/application/pdf');
//header("Content-Disposition: attachment;filename=\"participants_$serie.pdf\"");
header('Cache-Control: max-age=0');



//$objWriter = PHPExcel_IOFactory::createWriter($excel, 'PDF');
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
//$objWriter->writeAllSheets();
$objWriter->save('php://output');
exit(0);
