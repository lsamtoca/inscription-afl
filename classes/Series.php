<?php

// The goal of this module is to export the global
// $availableSeries
// This is an array of Series

class Series {

    public $available = array();
    public $available_string = '';

    private function getSeriesFromDb() {
        $sql = 'SELECT CODE AS nom,CB_LIBELLE AS nomLong, 1 as noEquipiers FROM `SERIE.DBF`  WHERE NB_EQUIPIE = 1.000000 AND NOT CODE = \'\'';
        $req = executePreparedQuery($sql, array());
        $result = $req->fetchall();
        $ret = array();
        foreach ($result as $bateau) {
            $ret[$bateau['nom']] = $bateau;
        }
        return $ret;
    }

    /*
      private function array_push_assoc(array &$array, $key, $value) {
      $array[$key] = $value;
      }

      private function initializeAvailableSeries() {
      $availableSeries = array();

      $LAR = array(
      'nom' => 'LAR',
      'nomLong' => 'Laser Radial',
      'noEquipiers' => 1
      );

      $LA4 = array(
      'nom' => 'LA4',
      'nomLong' => 'Laser 4.7',
      'noEquipiers' => 1);

      $LAS = array(
      'nom' => 'LAS',
      'nomLong' => 'Laser Standard',
      'noEquipiers' => 1);

      $OPT = array(
      'nom' => 'OPT',
      'nomLong' => 'Optimist',
      'noEquipiers' => 1);

      $FINN = array(
      'nom' => 'FINN',
      'nomLong' => 'Finn',
      'noEquipiers' => 1);



      $this->array_push_assoc(
      $availableSeries, 'LA4', $LA4);
      $this->array_push_assoc(
      $availableSeries, 'LAR', $LAR);
      $this->array_push_assoc(
      $availableSeries, 'LAS', $LAS);
      $this->array_push_assoc(
      $availableSeries, 'OPT', $OPT);
      $this->array_push_assoc(
      $availableSeries, 'FINN', $FINN);

      return $availableSeries;
      }
     */

    function __construct() {
        $this->available = $this->getSeriesFromDb();
        $this->available_string = implode(",", array_keys($this->available));
    }

}
