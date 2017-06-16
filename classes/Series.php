<?php

// The goal of this module is to export the global
// $availableSeries
// This is an array of Series

class Series {

    public $available = array();
    public $available_string = '';
    private $otherSeries = array(
            /*        array('RCO', 'RaceBoard > 3.80'),
              array('RSX','RaceBoard 8.5'),
              array('BIC','BIC 293D'),
              array('SLA','Slalom')

             */
    );

    private function getSeriesFromDb() {
        //$codeColumn = 'CODE';
        $codeColumn = 'CODE_BATEA';

        $sql = "SELECT CODE,CODE_BATEA,CB_LIBELLE AS nomLong, 1 as noEquipiers FROM `SERIE.DBF`"
                . ' WHERE NB_EQUIPIE = 1.000000'
                //. " AND NOT $codeColumn = ''"
        ;
        $req = executePreparedQuery($sql, array());
        $result = $req->fetchall();
        $ret = array();
        foreach ($result as $bateau) {
            if($bateau['CODE']==''){
                $bateau['nom'] = $bateau['CODE_BATEA'];      
            } elseif ($bateau['CODE_BATEA']==''){
                $bateau['nom'] = $bateau['CODE'];   
            } elseif ($bateau['CODE_BATEA'] == $bateau['CODE']){
                $bateau['nom'] = $bateau['CODE'];
            } else {
                $bateau['nom'] = "$bateau[CODE]-$bateau[CODE_BATEA]";
            }
            
            //$bateau['nom']=$bateau['nom'];
            $index = $bateau['nom'];
            $ret[$index] = $bateau;
            $ret[$index]['nomLong'] = iconv('ISO-8859-1', 'UTF-8', $bateau['nomLong']);
        }
        return $ret;
    }

    private function add_serie($nom, $nomLong) {
        $this->available[$nom] = array('nom' => $nom, 'nomLong' => $nomLong);
    }


    function __construct() {
        $this->available = $this->getSeriesFromDb();
        foreach ($this->otherSeries as $array) {
            self::add_serie($array[0], $array[1]);
        }
        $this->available_string = implode(",", array_keys($this->available));
    }

}
