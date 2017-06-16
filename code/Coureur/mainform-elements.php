<?php

global $regate;

$series = array();
foreach (Regate::availableSeries($regate) as $serie) {
    array_push($series, array($serie['nom'], $serie['nomLong']));
}

$mainformInputs = array(
    'Nom' => array(
        'rendering' => 'text'
    ),
    'Prenom' => array(
        'rendering' => 'text'
    ),
    'naissance' => array(
        'rendering' => 'text',
        'default' => '01/01/1998'
    ),
    'taillepolo' => array(
        'label' => 'Taille polo',
        'rendering' => 'hidden',
        'values' =>
        array('XS', 'S', 'M', 'L', 'XL', 'XXL'),
        'default' => 'M'
    ),
    'mail' => array(
        'rendering' => 'text'
    ),
    'nom_club' => array(
        'rendering' => 'text'
    ),
    'num_club' => array(
        'rendering' => 'text',
        'size' => '5'
    ),
    'sexe' => array(
        'rendering' => 'radio',
        'values' => array(
            array('F', 'Femme'),
            array('M', 'Homme')
        ),
        'default' => 'F'
    ),
    'serie' => array(
        // This is not updated when re-reading from for confirmation
        'rendering' => 'radio',
        'values' => $series,
        'default' => $series[0][0]
    ),
    'Cvoile' => array(
        'rendering' => 'text',
        'size' => '3',
        'nolabel' => true
    ),
    'Nvoile' => array(
        'rendering' => 'text',
        'size' => '6',
        'nolabel' => true
    ),
    'statut' => array(
        'rendering' => 'radio',
        'values' => array(
            'Licencie',
            'Etranger',
            'Autre'
        ),
        'default' => 'Autre'
    ),
    'adherant' => array(
        'label' => 'Adhérant à la classe',
        'rendering' => 'radio',
        'values' => array('1', '0'),
        'default' => '0'
    ),
    'lic' => array(
        'rendering' => 'text',
        'size' => '8'
    ),
    'isaf_no' => array(
        'rendering' => 'text',
        'size' => '10'
    ),
    // Hidden elements to handle control
    'lang' => array(
        'rendering' => 'hidden',
        'default' => 'fr'
    ),
    'IDR' => array(
        'rendering' => 'hidden',
        'default' => '0'
    ),
    'ID_inscrit' => array(
        'rendering' => 'hidden',
        'default' => '0'
    ),
    'conf' => array(
        'rendering' => 'hidden',
        'default' => '0'
    )
);

// Adaptations
// 
// Si c'est une regate Laser L
if (Regate::estLaser($regate)) {
    $mainformInputs['adherant'] = array(
        // TODO : adapter pour LaPelle
        'label' => 'Adhérant AFL',
        'rendering' => 'radio',
        'values' => array('1', '0'),
        'default' => '0'
    );
}

if(count($series) > 4){
    $mainformInputs['serie']['rendering'] = 'menu';
    $mainformInputs['serie']['label'] = 'Série';
}