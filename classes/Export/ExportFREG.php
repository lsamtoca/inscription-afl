<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This module produces a csv file 
 * suitable to be read by FREG
 * the scoring program used by 
 * the Federation Francaise de Voile

 * @author lsantoca
 */
class ExportFREG extends ExportCSV {

    private $regate;
    private $raceYear;
    private $groupeClasse_cat = 'LaserCategory';
    private $confirmes = true;

    public function __construct($idRegate, $confirmes = 'confirmes', $methode = 'laser') {
        $this->contentType = 'text/csv';
        $this->idRegate = $idRegate;
        $this->regate = Regate::selectById($this->idRegate);
        $this->raceYear = substr($this->regate['date_debut'], 0, 4);

        // We need the following to be sure that date behaves
        setlocale(LC_ALL, 'fr_FR');
        $date = date("d-m-Y_H\hi");
        $raceName = 'REGATE-EXPORT-POUR-FREG';
        $this->name = "${raceName}_${date}.csv";

        if ($methode == 'laser') {
            $this->groupeClasse_cat = 'LaserCategory';
        } elseif ($methode == 'optimist') {
            $this->groupeClasse_cat = 'OptimistCategory';
        } else {
            $this->groupeClasse_cat = 'LaserCategory';
        }

        if ($confirmes == 'all') {
            $this->confirmes = false;
        } else {
            $this->confirmes = true;
        }
        $this->generate();
    }

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

    private function doQuery() {
        $condition = 'WHERE  `ID_regate` =:ID_REGATE';
        if ($this->confirmes) {
            $condition.=' AND `conf`=\'1\'';
        }
        $sql = "SELECT *, DATE_FORMAT(`naissance`,'%Y') as `E1_NAIS` FROM `Inscrit` "
                . $condition;
        $assoc = array('ID_REGATE' => $this->idRegate);
        $req = executePreparedQuery($sql, $assoc);

        return $req->fetchAll();
    }

    private function generate() {

        $rows = $this->doQuery();

        $this->doEntete();
        $this->newLine();
        $this->doAuteur();
        $this->newLine();

        $cle = 1;
        foreach ($rows as $row) {
            $row['raceYear'] = $this->raceYear;
            $bateau = new Bateau($row, $cle, $this->groupeClasse_cat);
            $this->doBateau($bateau);
            $this->newLine();
            $this->doBarreur($bateau);
            $this->newLine();
            $this->doEquipiers($bateau);
            $cle++;
        }
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

    public function __construct($row, $cle, $methode) {
        $this->translator = new Translator($methode);
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

    public function __construct($row, $cle, $groupeClasse_cat) {
        $this->translator = new Translator($groupeClasse_cat);
        $this->doBateau($row, $cle);
        $equipier = new Equipier($row, $cle,$groupeClasse_cat);
        $this->barreur = $equipier->line;
        // This need to be changed when we allow 
        // boats with more than one team mates
        //  $this->equipiers = [];
    }

}

class Translator {

    private $category;

    public function __construct($class = 'OptimistCategory') {
//        $debugger = new Debugger();
//        $debugger->dumpAndExit($class);
        $this->category = new $class();
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
        'GROUPE' => array('compute', 'GROUPE'),
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
                        case 'GROUPE':
                            $agecat = $this->category->computeGROUPE($row);
                            $ascii_str = $agecat;
                            break;
                        case 'CLASSE_CAT':
                            $agecat = $this->category->computeCLASSE_CAT($row);
                            $ascii_str = $agecat;
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

class Category {

    protected $categories = array();

    protected function getAGECAT($row) {
        $bornYear = $row['E1_NAIS'];
        $raceYear = $row['raceYear'];

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

    public function description() {
        $string = "Categories : \n";
        foreach ($categories as $category) {
            $string.="De $category[0] à $category[1] ans  : $category[2] ($category[3])\n";
        }
        $string .= "F pour femme et M pour homme\n";
    }

}

class LaserCategory extends Category {

    /**
     * These are the usual age categories for Lasers
     */
    protected $categories = array(
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

    public function computeGROUPE($row) {
        return $row['serie'];
    }

    public function computeCLASSE_CAT($row) {
        $category = self::getAGECAT($row);
        $sexe = $row['sexe'];

        return  $category. $sexe ;
    }

}

class OptimistCategory extends Category {

    /**
     * Categories for Optimist
     */
    protected $categories = array(
        'BEN' => [0, 11, 'BEN', 'benjamin'],
        'MIN' => [12, 14, 'MIN', 'minime'],
        'ESP' => [15, 15, 'ESP', 'espoir']
    );

    public function computeGROUPE($row) {
        $serie = $row['serie'];
        if ($serie != 'OPTI') {
            //throw new Exception('Not an optimist');
            return $serie;
        }
        $category = self::getAGECAT($row);
        if ($category == 'MIN') {
            return 'OPM';
        } else {
            return 'OPB';
        }
    }

    public function computeCLASSE_CAT($row) {
        $category = self::getAGECAT($row);
        $sexe = $row['sexe'];

        return $sexe . $category;
    }

}
