<?php

include_once('Debugger.php');

class attributesHandler {

    public function getAttributes($object, $keys) {
        if (count($keys) == 0) {
            return "";
        }
        $retstr = "";
        foreach ($keys as $key) {
            if (isset($object->$key)) {
                $value = (string) ($object->$key);
                $retstr.=" $key=\"$value\"";
            }
        }
        return $retstr;
    }

}

class Value {
    public $name = '';
    public $label = '';
    public $selected = false;

    public function __construct($name, $label, $selected = false) {
        $this->name = $name;
        $this->label = $label;
        $this->selected = $selected;
    }
}

class PPrinter {

    public $noTabs = 0; // We need this to be public

    public function __construct($tabs = 0) {
        $this->noTabs = $tabs;
    }

    public function echoWithTabs($msg) {
        $tabs = str_repeat("\t", $this->noTabs);
        echo "$tabs$msg\n";
    }

    public function echoOpen($str) {
        $this->echoWithTabs($str);
        $this->noTabs+=1;
    }

    public function echoClose($str) {
        $this->noTabs-=1;
        $this->echoWithTabs($str);
    }

    public function jsEchoArray($name, $array) {
        $maxIndex = count($array) - 1;
        $sep = ',';
        $this->echoOpen("$name:{");
        $index = 0;
        foreach ($array as $key => $value) {
            if ($index == $maxIndex) {
                $sep = '';
            }
            if (!is_array($value)) {
                $this->echoWithTabs("$key:$value$sep");
            } else {
                $this->jsEchoArray($key, $value);
                $this->echoWithTabs($sep);
            }
            $index++;
        }
        $this->echoClose("}");
    }

}

class Input {
    
//    static 
    private $inputTypes = array(
        'text', 'textArea', 'menu', 'radios', 'multipleChoice',
        'sortableMultipleChoice',
        'singleRadio', 'checkBox', 'hidden', 'menu', 'submit',
        'uniqueChoice', 'captcha'
    );
    private $printer;
    public $name;
    public $type;
    public $rendering;
    public $id;
    public $class;
    public $label;
    public $labelclass;
    public $labelid;
    public $values = array(); // Array of Values
    public $value; // Selected value if unique choice
    public $checked = false; // For buttons

    private function getAttrStr($keys) {
        $aH = new attributesHandler();
        return $aH->getAttributes($this, $keys);
    }

    private function tagOpen($tag, $attributes = '') {
        return "<$tag $attributes>";
    }

    private function tagClose($tag, $comments = '') {
        $addenda = '';
        if ($comments != '') {
            $addenda = " <!-- $comments -->";
        }
        return "</$tag >$addenda";
    }

    private function tagWithContent($tag, $content, $attributes = '') {
        $string = "<$tag $attributes>$content</$tag>";
        return $string;
    }

    private function closedTag($tag, $attributes = '') {
        $string = "<$tag $attributes/>";
        return $string;
    }

    private function echoLabel($label = '') {
        if ($label == '' && isset($this->label)) {
            $label = $this->label;
        }
        //
        $attributesPre = $this->getAttrStr(['labelclass', 'labelid']);
        $attributes = str_replace('label', '', $attributesPre);
        $stringToPrint = $this->tagWithContent('label', $label, $attributes);
        $this->printer->echoWithTabs($stringToPrint);
        //
        if (isset($this->nobreak) && $this->nobreak) {
            
        } else {
            $this->printer->echoWithTabs("<br /><br />");
        }
    }

    // MLMessage -- Multi Lingual Message
    private function mLMessage($content = '') {
        if (isset($this->id)) {
            $lid = " id='l_$this->id'";
        } else {
            $lid = '';
        }
        $attributes = "class='msg'" . $lid;
        return $this->tagWithContent('span', $content, $attributes);
    }

    private function echoLabelMLmessage($content = '') {
        $content = $this->mLMessage($this->label);
        $this->echoLabel($content);
    }

    private function echoCaptcha() {
        if (isset($this->url)) {
            $url = $this->url;
        } else {
            $url = 'captcha';
        }
        $rand = rand();

        echo "\n";
        $attributes = "src='$url?rand=$rand' id='captchaimg'";
        $this->printer->echoWithTabs($this->closedTag('img', $attributes));
        $input = new Input('text', 'captcha', [
            'label' => 'Rentrez le code à la gauche',
            'size' => 6,
            'nobreak' => true]);
        $input->display($this->printer->noTabs);
    }

    private function echoText() {
        echo "\n";
        if (!isset($this->nolabel) or ! $this->nolabel == true) {
            $this->labelclass = 'left';
            $this->echoLabel();
        }
        $attributes = "type='text' name='$this->name'" .
                $this->getAttrStr([
                    'id', 'class', 'size', 'value',
                    'style', 'disabled']);
        $this->printer->echoWithTabs("<input $attributes/>");
    }

    private function echoTextArea() {
        echo "\n";
        if (!isset($this->nolabel) or ! $this->nolabel == true) {
            $this->labelclass = 'left';
            $this->echoLabel();
        }
        $attributes = $this->getAttrStr(['id', 'class', 'rows', 'cols', 'size']);
        $this->printer->echoWithTabs(
                "<textarea name='$this->name'$attributes>$this->value</textarea>");
    }

    private function echoHidden() {
        echo "\n";
        $this->printer->echoWithTabs(
                "<input name ='$this->name' type='hidden' value='$this->value' />");
    }

    private function echoCheckBox() {
        //$attributes = $this->getAttrStr(['checked']);
        $checked = '';
        if ($this->checked) {
            $checked = ' checked';
        }
        $this->printer->echoWithTabs(
                "<input type='checkbox'"
                . " name='$this->name' value='$this->value'"
                . "$checked/>");
        $this->nobreak = true;
        $this->echoLabel();
    }

// Multiplechoices
    private function echoSortableMultipleChoice() {
        $this->echoLabel();
        $this->printer->echoOpen($this->tagOpen('ul', "class='sortable'"));
        foreach ($this->values as $value) {
            $checkBox = new Input('checkBox', "$this->name-$value->name");
            $checkBox->value = $value->name;
            if ($value->selected) {
                $checkBox->checked = true;
            }
            $checkBox->label = $value->label;
            //
            $this->printer->echoOpen($this->tagOpen('li'));
            $this->printer->echoWithTabs(
                    $this->tagWithContent('span','', "class='ui-icon ui-icon-arrowthick-2-n-s'"));
            $checkBox->display($this->printer->noTabs);
            $this->printer->echoClose($this->tagClose('li'));
        }
        $this->printer->echoClose($this->tagCLose('ul'));
    }

    private function echoMultipleChoice() {
        $this->echoLabel();
        $this->printer->echoOpen($this->tagOpen('ul'));
        foreach ($this->values as $value) {
            $checkBox = new Input('checkBox', "$this->name-$value->name");
            $checkBox->value = $value->name;
            if ($value->selected) {
                $checkBox->checked = true;
            }
            $checkBox->label = $value->label;
            //
            $this->printer->echoOpen($this->tagOpen('li'));
//            $this->printer->echoWithTabs(
//                    $this->closedTag('span', "class='ui-icon ui-icon-arrowthick-2-n-s"));
            $checkBox->display($this->printer->noTabs);
            $this->printer->echoClose($this->tagClose('li'));
        }
        $this->printer->echoClose($this->tagCLose('ul'));
    }

// Only one choice available
    private function echoMenu() {
        if (isset($this->label)) {
            $this->labelclass = 'left';
            $this->echoLabel();
        }
        $this->printer->echoOpen("<select name=$this->name>");
        foreach ($this->values as $value) {
            $this->printer->echoWithTabs("<option value='$value->name'>$value->label</option>");
        }
        $this->printer->echoClose("</select>");
    }

    private function echoSingleRadio() {
        $attributes = "type='radio' name='$this->name' value='$this->value'" . $this->getAttrStr(['checked']);
        $string = $this->closedTag('input', $attributes);
        $this->printer->echoWithTabs($string);
        $this->labelclass = 'right';
        $this->nobreak = true;
        $this->echoLabel();
    }

    private function echoRadios() {
        if (isset($this->label)) {
            $this->labelclassl = 'left';
            $this->echoLabel();
        }
        foreach ($this->values as $value) {
            $radioButton = new Input('singleRadio', "$this->name");
            $radioButton->label = $value->label;
            $radioButton->value = $value->name;

            if (isset($this->value) && $this->value == $value->name) {
                $radioButton->checked = true;
            } else {
                unset($radioButton->checked);
            }
            $radioButton->display($this->printer->noTabs);
        }
    }

    private function echoUniqueChoice() {
        if (isset($this->rendering) && $this->rendering == 'radios') {
            $this->echoRadios();
        } else {
            $this->echoMenu();
        }
    }

    private function echoSubmit() {
        echo "\n";
        $this->printer->echoWithTabs(
                "<input type='submit' name='$this->name' value='$this->value'/>");
    }

    private function valuesFromArray($arrayOfValues) {
        foreach ($arrayOfValues as $val) {
            $name = $val['name'];
            $label = $val['label'];
            $value = new Value($name, $label);
            if (isset($val['selected'])) {
                $value->selected = $val['selected'];
            }
            $this->values[$name] = $value;
        }
    }

    public function __construct($type, $name, $parameters = array()) {
        $this->name = $name;
        if (!in_array($type, $this->inputTypes)) {
            throw new Exception("$type : type de input non inconnu");
        }
        $this->type = $type;
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'values':
                    $this->valuesFromArray($value);
                    break;
                default:
                    $this->$key = $value;
            }
        }
        $this->printer = new PPrinter();
        return $this;
    }

    public function display($tabs = 0) {
        $this->printer->noTabs = $tabs;
        switch ($this->type) {
            case 'hidden':
                $this->echoHidden();
                break;
            case 'menu':
                $this->echoMenu();
                break;
            case 'multipleChoice':
                $this->echoMultipleChoice();
                break;
            case 'sortableMultipleChoice':
                $this->echoSortableMultipleChoice();
                break;
            case 'checkBox':
                $this->echoCheckBox();
                break;
            case 'radios':
                $this->echoRadio();
                break;
            case 'singleRadio':
                $this->echoSingleRadio();
                break;
            case 'submit':
                $this->echoSubmit();
                break;
            case 'uniqueChoice':
                $this->echoUniqueChoice();
                break;
            case 'textArea':
                $this->echoTextArea();
                break;
            case 'captcha':
                $this->echoCaptcha();
                break;
            default:
            case 'text':
                $this->echoText();
                break;
        }
    }

}

class Form {

    public $id;
    public $label;
    public $action = '';
    public $method = 'POST';
    public $submitName;
    public $submitValue = 'Submit';
    public $inputs = array();
    public $class;
    private $printer;

    public function __construct($id, $parameters = array()) {
        $this->id = $id;
        $this->submitName = "$this->id" . "_submit";
        foreach ($parameters as $key => $value) {
            $this->$key = $value;
        }
        $this->printer = new PPrinter();
    }

    private function getAttrStr($keys) {
        $aH = new attributesHandler();
        return $aH->getAttributes($this, $keys);
    }

    private function echoLabel() {
        $this->printer->echoWithTabs("$this->label");
        $this->printer->echoWithTabs("<br /><hr />");
    }

    public function display($tabs = 0) {
        $this->printer->noTabs = $tabs;

        $submit = new Input('submit', $this->submitName);
        $submit->value = $this->submitValue;
        $attributes = $this->getAttrStr(['class']);


        $this->printer->echoOpen(
                "<form id='$this->id' action='$this->action' method='$this->method'$attributes>");
        $this->printer->echoOpen("<fieldset>");

        if (isset($this->label)) {
            echo $this->echoLabel();
        }

        foreach ($this->inputs as $input) {
            $input->display($this->printer->noTabs);
            $this->printer->echoWithTabs("<br />\n");
        }
        if (isset($this->captcha) && $this->captcha == true) {
            $captcha = new Input('captcha', 'captcha');
            $captcha->display($this->printer->noTabs);
            $this->printer->echoWithTabs("<br />");
        }
        $submit->display($this->printer->noTabs);

        $this->printer->echoClose("</fieldset>");
        $this->printer->echoClose("</form><!--form id=$this->id -->");
    }

    public function displayValidation($tabs = 0) {
        $this->printer->noTabs = $tabs;
        echo "\n";
        $this->printer->echoOpen("<script type='text/javascript'>");
        $this->printer->echoOpen("$(document).ready(function () {");
        $this->printer->echoOpen("$(\"#$this->id\").validate({");
        $rules = array();
        $messages = array();
        foreach ($this->inputs as $input) {
            $rule = array();
            $message = array();
            if (isset($input->validations)) {
                foreach ($input->validations as $key => $validation) {
                    if (!isset($validation['value'])) {
                        $validation['value'] = 'true';
                    }
                    $rule[$validation['type']] = $validation['value'];
                    $message[$validation['type']] = "\"$validation[message]\"";
                }
                $rules[$input->name] = $rule;
                $messages[$input->name] = $message;
            }
            if ($this->captcha) {
                $rules['captcha'] = ['required' => 'true'];
                $messages['captcha'] = ['required' => '"Ce champ est obligatoire"'];
            }
        }
        $this->printer->jsEchoArray('rules', $rules);
        $this->printer->echoWithTabs(",");
        $this->printer->jsEchoArray('messages', $messages);

        $this->printer->echoClose("});");
        $this->printer->echoClose("});");
        $this->printer->echoClose("</script>\n");
    }

    public function isCompleted() {
        return isset($_POST[$this->submitName]);
    }

    public function isValidCaptcha() {
        return isset($_POST['captcha']) && $_POST['captcha'] == $_SESSION['captcha'];
    }

    private function fromPostMChoice($input) {
        $values = array();
        $name = $input->name;
        foreach ($input->values as $key => $value) {
            if (filter_has_var(INPUT_POST, $name-$value->name)) {
                $this->inputs[$name]->values[$key]->selected = true;
                array_push($values, $value->name);
            }
        }
        $this->inputs[$name]->value = implode(",", $values);
    }

    private function fromPostSMChoice($input) {
        $name = $input->name;
        $inPostKeys = array();
        $notInPostKeys = array();
        $newValues = array();

        $keys = array_keys($input->values);
        foreach($keys as $key){
            $pKeys[$key] = "$name-$key";
        }
        foreach ($_POST as $pKey => $value) {
            if (in_array($pKey, $pKeys)) {
                $key =  substr($pKey,  strlen($name) + 1);
                $newValues[$key] = $input->values[$key];
                $newValues[$key]->selected = true;
                array_push($inPostKeys, $key);
            }
        }
        foreach ($keys as $key) {
            if (!isset($newValues[$key])) {
                $newValues[$key] = $input->values[$key];
                $newValues[$key]->selected = false;
            }
        }
        $this->inputs[$name]->value = implode(",", $inPostKeys);
        $this->inputs[$name]->values = $newValues;
    }

    public function fromPost($defaultFilter=FILTER_DEFAULT) {
        foreach ($this->inputs as $name => $input) {
            switch ($input->type) {
                case 'sortableMultipleChoice':
                    $this->fromPostSMChoice($input);
                    break;
                case 'multipleChoice':
                    $this->fromPostMChoice($input);
                    break;
                default:
                    if (filter_has_var(INPUT_POST, $name)) {
                        $this->inputs[$name]->value = filter_input(INPUT_POST,$name,$defaultFilter);
                    }
            }
        }
    }

    public function toArray($assocList = array()) {
        $tmp = array();
        foreach ($this->inputs as $input) {
            $tmp[$input->name] = $input->value;
        }

        if (count($assocList) == 0) {
            return $tmp;
        } else {
            $ret = array();
            foreach ($assocList as $key => $value) {
                if (isset($tmp[$key])) {
                    $ret[$value] = $tmp[$key];
                }
            }
            return $ret;
        }
    }

    public function linkInputs() {
        foreach ($this as $property) {
            if ($property instanceOf Input) {
                $this->inputs[$property->name] = $property;
            }
        }
    }

}
