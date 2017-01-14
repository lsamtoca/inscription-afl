<?php

// Why is ths needed
// require_once 'php/StringsCrunch.php';

class Export {

    public $name;
    public $contentType;
    protected $content = '';

    public function display() {
        header("Content-Type: $this->contentType");
        header("Content-Disposition: attachment;filename=\"$this->name\"");
        header('Cache-Control: max-age=0');
        echo $this->content;
    }

}

class ExportCsv extends Export {

    public $csvSep = ';';
    public $csvNewLine = "\r\n";

    protected function addField($str) {
        $this->content .= $str . $this->csvSep;
    }

    protected function newLine() {
        $this->content.=$this->csvNewLine;
    }

    protected function arrayToLine($array) {
        foreach ($array as $str) {
            $this->addField($str);
        }
    }

    protected function arrayToLineLn($array) {
        $this->arrayToLine($array);
        $this->newLine();
    }

}

/*
  interface Category {

  public function compute($bornYear, $raceYear);
  }

 */

class LaserCategory {

    /**
     * These are the usual age categories for Lasers
     */
    private $categories = array(
        'MIN' => [12, 14, 'MIN', 'minime'],
        'CAD' => [15, 16, 'CAD', 'cadet'],
        'JUN' => [17, 18, 'JUN', 'junior'],
        'SEN' => [19, 34, 'SEN', 'senior'],
        'AMA' => [35, 44, 'AMA', 'Apprenti master'],
        'MAS' => [45, 54, 'MAS', 'Master'],
        'GMA' => [55, 64, 'GMA', 'Grand master'],
        'GGM' => [65, 74, 'GGM', 'Grand grand master'],
        'LEG' => [75, 110, 'LEG', 'Legend']
    );

    public function compute($bornYear, $raceYear) {
        $age = $raceYear - $bornYear;
        $category = '???';
        foreach ($this->categories as $key => $values) {
            if ($age >= $values[0] and $age <= $values[1]) {
                $category = $key;
                break;
            }
        }
        return $category;
    }

    public function __construct() {
        
    }

}

class Translator {

    private $category;

    public function __construct() {
        $this->category = new LaserCategory();
    }

    /**
     * This describes how to associate fields
     * from our DB
     */
    private $correspondence = array(
        'VOILE' => array('compute', 'num_voile'),
        'CODE_PAYS' => array('db', 'prefix_voile'),
        'INS_EN' => array('const', 'VL'),
        'BAT_TYPE' => array('db', 'serie'),
        'NB_EQUIP' => array('const', '1'),
        'GROUPE' => array('db', 'serie'),
        'CLASSE_CAT' => array('compute', 'CLASSE_CAT'),
        'E1_NOM' => array('db', 'nom'),
        'E1_PRE' => array('db', 'prenom'),
        'E1_SEXE' => array('db', 'sexe'),
        // The following we have hacked in the mysql query
        'E1_NAIS' => array('db', 'E1_NAIS'),
        'E1_LIC' => array('db', 'num_lic'),
        'E1_ISAF' => array('db', 'isaf_no'),
        'E1_CLUB' => array('db', 'num_club'),
        'E1_CLUB_LI' => array('db', 'nom_club'),
        'E1_PAYS' => array('db', 'prefix_voile'),
        'E1_EMAIL' => array('db', 'mail')
    );

    public function getValue($field, $row) {
        $ascii_str = '';

        if (isset($this->correspondence[$field])) {
            $method = $this->correspondence[$field][0];
            $colInDb = $this->correspondence[$field][1];
            switch ($method) {
                case 'const':
                    $ascii_str .= $field[1];
                    break;
                case 'compute':
                    switch ($field) {
                        case 'CLASSE_CAT':
                            $agecat = $this->category->compute($row['E1_NAIS'], $row['raceYear']);
                            $ascii_str = $row['sexe'] . $agecat;
                            break;
                        case 'VOILE':
                            $ascii_str = $row['prefix_voile'] . $row[$colInDb];
                            break;
                    }
                    break;
                default:
                case 'db':
                    $ascii_str.= $row[$colInDb];
                    break;
            }

// SANITIZE THE STRING 
// SO THAT ALL THE INPUT NAMES GET BETTER 
            $ascii_str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $ascii_str);
            $ascii_str = str_replace(array('\'', '^', '"'), array(), $ascii_str);
//PUT NAMES AND NUM VOILE IN UPPERCASE
            if (in_array($field, array('VOILE', 'CODE_PAYS', 'E1_NOM', 'E1_PRE'))) {
                $ascii_str = strtoupper($ascii_str);
            }
        }

        return $ascii_str;
    }

}

class Equipier {

    private $translator;
    public $line = array();

    /**
     * This describes the structure of the CSV file
     * for the 'Equipier'
     */
    private $equip_fields = array(
        3 => array('E1_LIC', 'C', '8'),
        4 => array('E1_NOM', 'C', '30'),
        5 => array('E1_PRE', 'C', '15'),
        6 => array('E1_NAIS', 'C', '4'),
        7 => array('E1_SEXE', 'C', '1'),
        8 => array('E1_PAYS', 'C', '3'),
        9 => array('E1_CLUB', 'C', '5'),
        11 => array('E1_ISAF', 'C', '12')
    );

    private function doEquipier($row, $cle) {
        $this->line[1] = 'E01';
        $this->line[2] = $cle;
        for ($i = 3; $i <= 11; $i++) {
            $ascii_str = '';
            if (isset($this->equip_fields[$i])) {
                $field = $this->equip_fields[$i][0];
                $ascii_str = $this->translator->getValue($field, $row);
            }
            $this->line[$i] = $ascii_str;
        }
    }

    public function __construct($row, $cle) {
        $this->translator = new Translator();
        $this->doEquipier($row, $cle);
    }

}

class Bateau {

    public $line = array(); // The CSV LINE CONCERNING THE BOAT
    public $barreur = array();
    public $equipiers = array();
    private $translator;

    /**
     * This describes the structure of the CSV file
     * for the 'Bateaux'
     */
    private $bat_fields = array(
        3 => array('VOILE', 'C', '9'),
        9 => array('GROUPE', 'C', '3'),
        10 => array('CLASSE_CAT', 'C', '5'),
        11 => array('E1_EMAIL', 'C', '80')
    );

    private function doBateau($row, $cle) {
        $this->line[1] = 'BAT';
        $this->line[2] = $cle;
        for ($i = 3; $i <= 11; $i++) {
            $ascii_str = '';
            if (isset($this->bat_fields[$i])) {
                $field = $this->bat_fields[$i][0];
                $ascii_str = $this->translator->getValue($field, $row);
            }
            $this->line[$i] = $ascii_str;
        }
    }

    public function __construct($row, $cle) {
        $this->translator = new Translator();
        $this->doBateau($row, $cle);
        $equipier = new Equipier($row, $cle);
        $this->barreur = $equipier->line;
//        $db = new Debugger();
//        $db->dumpAndExit($this);
// This need to be changed when we allow 
// boats with more than one team mates
//  $this->equipiers = [];
    }

}

/**
 * This module produces a csv file 
 * suitable to be read by FREG
 * the scoring program used by 
 * the Federation Francaise de Voile
 */
class ExportFreg extends ExportCsv {

    private $regate;
    private $raceYear;
    private $category;

    function doBateau($bateau) {
        $this->arrayToLine($bateau->line);
    }

    function doBarreur($bateau) {
        $this->arrayToLine($bateau->barreur);
    }

    function doEquipiers($bateau) {
        foreach ($bateau->equipiers as $equipier) {
            $this->arrayToLine($equipier->line);
            $this->newLine();
        }
    }

    function doEntete() {
        $this->addField('CodeLigne');
        $this->addField('Cle');
        for ($i = 1; $i <= 10; $i++) {
            $this->addField(sprintf('Rubrique%d', $i));
        }
    }

    private function doAuteur() {
        $this->addField('AUT');
        $this->addField('0');
        $this->addField('Luigi Santocanale, luigi.santocanale@lif.univ-mrs.fr');
    }
    
    function doQuery($confirme){
        $condition = 'WHERE  `ID_regate` =:ID_REGATE';
        if ($confirme) {
            $condition.=' AND `conf`=\'1\'';
        }
        $sql = "SELECT *, DATE_FORMAT(`naissance`,'%Y') as `E1_NAIS` FROM `Inscrit` "
                . $condition;
        $assoc = array('ID_REGATE' => $this->idRegate);
        $req = executePreparedQuery($sql, $assoc);
                
        return $req->fetchAll(); 
    }

    function generate($confirme = true) {
// Why do we need that ?
        setlocale(LC_ALL, 'fr_FR');
        
        $rows = $this->doQuery($confirme);
        
        $this->doEntete();
        $this->newLine();
        $this->doAuteur();
        $this->newLine();

        $cle = 1;
        foreach ($rows as $row) {
            $row['raceYear'] = $this->raceYear;
            $bateau = new Bateau($row, $cle);
            $this->doBateau($bateau);
            $this->newLine();
            $this->doBarreur($bateau);
            $this->newLine();
            $this->doEquipiers($bateau);
            $cle++;
        }
    }

    public function __construct($idRegate) {
        $this->contentType = 'text/csv';
        $this->idRegate = $idRegate;
        $this->regate = Regate_selectById($this->idRegate);
        $this->raceYear = substr($this->regate['date_debut'], 0, 4);

        $date = date("d-m-Y_h\hi");
        $raceName = 'REGATE-EXPORT-POUR-FREG';
        $this->name = "${raceName}_${date}.csv";

        $this->generate();
    }

}
