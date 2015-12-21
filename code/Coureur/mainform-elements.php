<?php

$mainform_elements = array(
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
        'rendering' => 'radio',
        'values' => array(
            array('LA4', '4.7'),
            array('LAR', 'Laser Radial'),
            array('LAS', 'Laser Standard')
        ),
        'default' => 'LA4'
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
        'label' => 'Adhérant AFL',
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

function put_element($name) {

    global $mainform_elements;
    global $formData;

    $object = $mainform_elements[$name];

    switch ($object['rendering']) {

        case 'radio':
            if (isset($object['label'])) {
                $label = $object['label'];
                echo "<label class='left' id='l_$name'>$label</label>\n\n";
            }

            foreach ($object['values'] as $value) {

                if (is_array($value)) {
                    $val = $value[0];
                    $label = $value[1];
                } else {
                    $val = $label = $value;
                }

                $id = "$name$val";
//                $id = "radio_$val";

                $ischecked = '';
                if (isset($object['default']) && $object['default'] == $val)
                    $ischecked = 'checked';

                echo "<input type='radio' name='$name' id='radio_$id' value='$val' $ischecked />\n";
                echo "<label for='radio_$id' id='l_$id' >$label</label>\n\n";
            }
            break;

        case 'hidden':
            $val = $object['default'];
            echo "<input name='$name' type='hidden' id='input_$name' value='$val' />\n\n";
            break;

        case 'text':
            $val = '';
            if (isset($object['default']))
                $val = $object['default'];

            $strsize = '';
            if (isset($object['size'])) {
                $size = $object['size'];
                $strsize = "size='$size'";
            }
            if (!isset($object['nolabel']) or !$object['nolabel'] === 'true')
                echo "<label class='left' id='l_$name'></label>\n";
            echo "<input name='$name' type='text' id='$name' value='$val' $strsize />\n\n";
    }
}

