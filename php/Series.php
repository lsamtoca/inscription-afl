<?php

// The goal of this module is to export the global
// $availableSeries
// This is an array of Series


function array_push_assoc(array &$array, $key, $value) {
    $array[$key] = $value;
}

function initializeAvailableSeries() {
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

    array_push_assoc(
            $availableSeries, 'LA4', $LA4);
    array_push_assoc(
            $availableSeries, 'LAR', $LAR);
    array_push_assoc(
            $availableSeries, 'LAS', $LAS);
    array_push_assoc(
            $availableSeries, 'OPT', $OPT);
    array_push_assoc(
            $availableSeries, 'FINN', $FINN);

    return $availableSeries;
}

$availableSeries = initializeAvailableSeries();
$availableSeriesString = implode(",", array_keys($availableSeries));