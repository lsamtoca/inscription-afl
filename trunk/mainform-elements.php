<?php
$mainform_elements = array(
    'serie' => array(
        'rendering' => 'radio',
        'values' => array(
            array('LA4', '4.7'),
            array('LAR', 'Laser Radial'),
            array('LAS', 'Laser Stanfard')
        )
    ),
        );

function put_element($name) {

    global $mainform_elements;
    global $formData;
    
    $object = $mainform_elements[$name];

    switch ($object['rendering']) {

           case 'radio':
            foreach($object['values'] as $value){
               $val=$value[0];
               $label=$value[1];
               echo "<input type=\"radio\" name=\"$name\" id=\"radio_$val\" value=\"$val\"" ; 
                   echo $formData[$val]  ;
                   echo " />";
                   echo "<label for=\"radio_$val\">$label</label>";

            break;
            }
            
    }
}

?>


<input type="radio" name="serie" id="radio_LA4" value="LA4" 
<?php echo $formData['LA4']; ?> />
<label for="radio_LA4">Laser 4.7</label>

<input type="radio" name="serie" id="radio_LAR" value="LAR" 
<?php echo $formData['LAR']; ?> />
<label for="radio_LAR">Laser Radial</label>

<input type="radio" name="serie" id="radio_LAS" class="required"
       value="LAS" <?php echo $formData['LAS']; ?>/>
<label for="radio_LAS">Laser Standard</label>
