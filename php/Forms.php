<?php

function echoMsg($id, $content = '') {
    echo
    "<span class='msg' id='$id'>"
    . "$content"
    . "</span>";
}

function echoRightLabel($id, $content = '') {
    echo "<label class='right'>";
    echoMsg("l_$id", $content);
    echo "</label>\n\n";
}

function echoLeftLabel($id, $content = '') {
    echo "<label class='left'>";
    echoMsg("l_$id", $content);
    echo "</label>\n\n";
}

function echo_input($name, $formInputs) {

    $object = $formInputs[$name];

    switch ($object['rendering']) {

        case 'radio':
            if (isset($object['label'])) {
                $label = $object['label'];
                echoLeftLabel($name, $label);
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
                    $ischecked = "checked='true'";

                echo "<input type='radio' name='$name' id='radio_$id' value='$val' $ischecked />\n";
                echoRightLabel($id, $label);
            }
            break;

        case 'hidden':
            $val = $object['default'];
            echo "<input name = '$name' type = 'hidden' id = 'input_$name' value = '$val' />\n\n";
            break;

        case 'text':
            $val = '';
            if (isset($object['default']))
                $val = $object['default'];

            $strsize = '';
            if (isset($object['size'])) {
                $size = $object['size'];
                $strsize = "size = '$size'";
            }
            if (!isset($object['nolabel']) or ! $object['nolabel'] === 'true') {
                echoLeftLabel($name, '');
            }
//                echo "<label class = 'left msg' id = 'l_$name'></label>\n";
            echo "<input name = '$name' type = 'text' id = '$name' value = '$val' $strsize />\n\n";

            break;

        case 'checkbox':
            $checked = "";
            if ($object['value'] == 1) {
                $checked = "checked ";
            }
            $label = $object['label'];
            echo "<input type='checkbox' name='$name' $checked/> $label </br>\n";

            break;

        case 'menu':
            echoLeftLabel($name, $object['label']);
            echo "<select name=$name>";
            foreach($object['values'] as $value){
                echo "\t<option value=$value[0]>$value[1]</option>\n";
            }
            echo '</select>';

            break;

        case 'orderedMChoice':
            echo $object['label'] . " :";
            echo "<ul class='sortable'>\n";
            foreach ($object['choices'] as $nom => $choice) {
                $checked = "";
                if ($choice['checked']) {
                    $checked = "checked='checked' ";
                }
                $label = $choice['label'];
                //$value = $choice['value'];
                //echo "\t<li class='ui-state-default'>";
                echo "\t<li>";
                echo "<span class='ui-icon ui-icon-arrowthick-2-n-s'></span>";
                echo "<input type='checkbox' "
                . "name='$name$nom' $checked/> $label";
                echo "</li>\n";
            }
            echo "</ul>\n";
    }
}
