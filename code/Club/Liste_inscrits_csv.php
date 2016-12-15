<?php

/**
 * This module produces a csv file 
 * suitable to be read by FREG
 * the scoring program used by 
 * the Federation Francaise de Voile
 */

//$csv_sep = ";";
define('CSV_SEP', ';');
//$csv_newline = "\r\n"; // we use DOS-WINDOWS
define('CSV_NEWLINE', "\r\n");

function add_csv_field($line, $str) {
    return $line . $str . CSV_SEP;
}

function addCsvNewline($csv) {
    return $csv . CSV_NEWLINE;
}

// Compute the year of the race
$regate = Regate_selectById($_SESSION['ID_regate']);
$raceYear = substr($regate['date_debut'], 0, 4);



/**
 * This describes the structure of the CSV file
 * for the 'Bateaux'
 */
$bat_fields = array(
    3 => array('VOILE', 'C', '9'),
    9 => array('GROUPE', 'C', '3'),
    10 => array('CLASSE_CAT', 'C', '5'),
    11 => array('E1_EMAIL', 'C', '80')
);

/**
 * This describes the structure of the CSV file
 * for the 'Equipier'
 */
$equip_fields = array(
    3 => array('E1_LIC', 'C', '8'),
    4 => array('E1_NOM', 'C', '30'),
    5 => array('E1_PRE', 'C', '15'),
    6 => array('E1_NAIS', 'C', '4'),
    7 => array('E1_SEXE', 'C', '1'),
    8 => array('E1_PAYS', 'C', '3'),
    9 => array('E1_CLUB', 'C', '5'),
    11 => array('E1_ISAF', 'C', '12')
);

/**
 * This describes how to assiciate fields
 * from our DB
 */
$correspondence = array(
    'VOILE' => array('num_voile', ''),
    'CODE_PAYS' => array('prefix_voile', ''),
    'INS_EN' => array('', 'VL'),
    'BAT_TYPE' => array('serie', ''),
    'NB_EQUIP' => array('', '1'),
    'GROUPE' => array('serie', ''),
    'CLASSE_CAT' => array('CLASSE_CAT', ''),
    'E1_NOM' => array('nom', ''),
    'E1_PRE' => array('prenom', ''),
    'E1_SEXE' => array('sexe', ''),
    'E1_NAIS' => array('E1_NAIS', ''),
    'E1_LIC' => array('num_lic', ''),
    'E1_ISAF' => array('isaf_no', ''),
    'E1_CLUB' => array('num_club', ''),
    'E1_CLUB_LI' => array('nom_club', ''),
    'E1_PAYS' => array('prefix_voile', ''),
    'E1_EMAIL' => array('mail', '')
);

/**
 * These are the usual age categories for Lasers
 */
$categories = array(
    array(12, 14, 'MIN'),
    array(15, 16, 'CAD'),
    array(17, 18, 'JUN'),
    array(19, 34, 'SEN'),
    array(35, 44, 'AMA'),
    array(45, 54, 'MAS'),
    array(55, 64, 'GMA'),
    array(65, 110, 'GGM')
);

function compute_age_cat($bornYear) {

    global $raceYear, $categories;
//    echo $bornYear;
//    echo $raceYear."\n";
    $age = $raceYear - $bornYear;
    $category = '???';
    foreach ($categories as $cat) {
        if ($age >= $cat[0] and $age <= $cat[1]) {
            $category = $cat[2];
            break;
        }
    }
// Next for testing
//    echo "$bornYear -> $age -> $category (".$cat[0]. "--".$cat[1]. ") <br />";
    return $category;
}

function compute_csv_value($field, $row) {

    global $correspondence;

    $ascii_str = '';
    if (isset($correspondence[$field[0]])) {

        $value = $correspondence[$field[0]];
        $ascii_str = $value[1];

        if ($value[1] == '') {
            // If this is not defined in the 
            // $correspondence array
            // look for it in the db

            if ($value[0] == 'CLASSE_CAT') {
                //                  $sexe = $row['sexe'];
//                echo $row['E1_NAIS'] . "\n";
//                list($an, $mois, $jour) = sscanf($row['naissance'], '%4d-%2d-%4d');
                //               echo $an;
                $agecat = compute_age_cat($row['E1_NAIS']);
                $ascii_str = $row['sexe'] . $agecat;
            }
            // Il faudra voir si ca vaut la peine creer les
            // deux classes LASM et LASF
            else
                $ascii_str = $row[$value[0]];

            if ($value[0] == 'num_voile')
                $ascii_str = $row['prefix_voile'] . $ascii_str;

            $ascii_str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $ascii_str);
            $ascii_str = str_replace(array('\'', '^', '"'), array(), $ascii_str);

            if (($value[0] == 'prenom')
                    or ( $value[0] == 'nom')
                    or ( $value[0] == 'num_voile')
            )
                $ascii_str = strtoupper($ascii_str);
        }
    }

    return $ascii_str;
}

function doBateaux($row, $cle) {
    global $bat_fields;

    $line = '';
    $line = add_csv_field($line, 'BAT');
    $line = add_csv_field($line, $cle);

    for ($i = 3; $i <= 11; $i++) {
        $ascii_str = '';
        if (isset($bat_fields[$i])) {
            $field = $bat_fields[$i];
            $ascii_str = compute_csv_value($field, $row);
        }
        $line = add_csv_field($line, $ascii_str);
    }
    return $line;
}

function doEquipier($row, $cle) {
    global $equip_fields;
    global $correspondence;

    $line = '';
    $line = add_csv_field($line, 'E01');
    $line = add_csv_field($line, $cle);
    for ($i = 3; $i <= 11; $i++) {
        $ascii_str = '';
        if (isset($equip_fields[$i])) {
            $field = $equip_fields[$i];
            $ascii_str = compute_csv_value($field, $row);
        }
        $line = add_csv_field($line, $ascii_str);
    }
    return $line;
}

function doEntete() {
    $line = add_csv_field('', 'CodeLigne');
    $line = add_csv_field($line, 'Cle');
    for ($i = 1; $i <= 10; $i++) {
        $line = add_csv_field($line, sprintf('Rubrique%d', $i));
    }
    return $line;
}

function doAuteur() {
    $line = add_csv_field('', 'AUT');
    $line = add_csv_field($line, '0');
    $line = add_csv_field($line, 'Luigi Santocanale, luigi.santocanale@lif.univ-mrs.fr,inscriptions-afl');
    return $line;
}

function generateCsv() {

    setlocale(LC_ALL, 'fr_FR');

    $condition = 'WHERE  `ID_regate` =:ID_REGATE';
    if (isset($_GET['confirme']) and $_GET['confirme'] == '1')
        $condition.=' AND `conf`=\'1\'';
    $sql = "SELECT *, DATE_FORMAT(`naissance`,'%Y') as `E1_NAIS` FROM `Inscrit` "
            . $condition;
    $assoc = array('ID_REGATE' => $_SESSION['ID_regate']);
    $req = executePreparedQuery($sql, $assoc);

    $csv = doEntete();
    $csv .= CSV_NEWLINE;
    $csv .= doAuteur();
    $csv .= CSV_NEWLINE;

    $cle = 1;
    while ($row = $req->fetch()) {
        //Parcours du résultat de la requête
        $csv.=doBateaux($row, $cle);
        $csv.= CSV_NEWLINE;
        $csv.=doEquipier($row, $cle);
        $csv.= CSV_NEWLINE;
        $cle++;
    }

    return $csv;
}

$csv = generateCsv();

require_once 'php/StringsCrunch.php';
$nomRegate = 'REGATE-EXPORT-POUR-FREG'; //nom_normaliser($regate['titre']);
$date = date("d-m-Y_h\hi");
$nomFichier="${nomRegate}_${date}.csv";

// For testing
//echo $nomFichier;
//exit(0);

// Direct output to a client’s web browser (Excel5)
header('Content-Type: text/csv');
header("Content-Disposition: attachment;filename=\"$nomFichier\"");
header('Cache-Control: max-age=0');
echo $csv;

exit(0);
