<?php

include_once('Debugger.php');

class attributesHandler {

    public function getAttributes($object, $keys) {
        if (count($keys) == 0) {
            return "";
        }
        $retstr = "";
        foreach ($keys as $key) {
//echo " $key ";
            if (isset($object->$key)) {
                $value = (string) ($object->$key);
                $retstr.=" $key='$value'";
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

class Input {

    private $inputTypes = array(
        'text', 'textArea', 'menu', 'radios', 'orderedMChoice',
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

    private function echoMsg($content = '') {
        if (isset($this->id)) {
            $lid = " id='l_$this->id'";
        } else {
            $lid = '';
        }
        echo
        "<span class='msg'$lid>"
        . "$content"
        . "</span>";
    }

    private function echoLabelMsg() {
        $attributes = $this->getAttrStr(['labelclass', 'labelid']);
        $modAttributes = str_replace('label', '', $attributes);
        echo "<label$modAttributes>";
        $this->echoMsg($this->label);
        echo "</label>\n\n";
    }

    private function echoLabel() {
        $attributes = $this->getAttrStr(['labelclass', 'labelid']);
        $modAttributes = str_replace('label', '', $attributes);
        $this->printer->echoWithTabs("<label$modAttributes>$this->label : </label>");
        if (isset($this->nobreak) && $this->nobreak) {
            
        } else {
            $this->printer->echoWithTabs("<br /><br />");
        }
    }

    private function echoCaptcha() {
        if (isset($this->url)) {
            $url = $this->url;
        } else {
            $url = 'captcha';
        }
        $rand = rand();

        echo "\n";
        $this->printer->echoWithTabs("<img src='$url?rand=$rand' id='captchaimg' />");
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
        $attributes = $this->getAttrStr([
            'id', 'class', 'size', 'value',
            'style', 'disabled']);
        $this->printer->echoWithTabs("<input type='text' name='$this->name'$attributes/>");
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
        $attributes = $this->getAttrStr(['checked']);
        echo "<input type='checkbox' name='$this->name' $attributes/>$this->label</br>\n";
    }

// Multiplechoices
    private function echoOrderedMChoice() {
//choices
        echo $this->label . " :";
        echo "<ul class='sortable'>\n";
        foreach ($this->values as $value) {
            $checkBox = new Input('checkBox', "$this->name$_value[name]");
            if ($value->selected) {
                $checkBox->checked = true;
            }
            $checkBox->label = $value->label;
            echo "\t<li>";
            echo "<span class='ui-icon ui-icon-arrowthick-2-n-s'></span>";
            $checkBox->display();
            echo "</li>\n";
        }
        echo "</ul>\n";
    }

// Only one choice available
    private function echoMenu() {
        if (isset($this->label)) {
            $this->labelclass = 'left';
            $this->echoLabel();
        }
        echo "<select name=$this->name>\n";
        foreach ($this->values as $value) {
            echo "\t<option value='$value->name'>$value->label</option>"
            . "\n";
        }
        echo "</select>\n";
    }

    private function echoSingleRadio() {
        $attributes = $this->getAttrStr(['checked']);
        echo "<input type='radio' name='$this->name' value='$this->value'$attributes/>"
        . "\n";
        $this->labelclass = 'right';
        $this->echoLabel();
    }

    private function echoRadios() {
        if (isset($this->label)) {
            $this->echoLabel('left');
        }
        foreach ($this->values as $value) {
            $radioButton = new Input('singleRadio', "$this->name" . "_$value->name");
            $radioButton->label = $value->label;
            $radioButton->value = $value->name;

            if (isset($this->value) && $this->value == $value->name) {
                $radioButton->checked = true;
            } else {
                unset($radioButton->checked);
            }
            $radioButton->display();
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

    public function __construct($type, $name, $parameters = array()) {
        $this->name = $name;
        if (!in_array($type, $this->inputTypes)) {
            throw new Exception("$type : type de input non inconnu");
        }
        $this->type = $type;
        foreach ($parameters as $key => $value) {
            $this->$key = $value;
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
            case 'orderedMChoice':
                $this->echoOrderedMChoice();
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

    public function valuesFromArray($arrayOfValues) {
        foreach ($arrayOfValues as $val) {
            $name = $val['name'];
            $label = $val['label'];
            $value = new Value($name, $label);
            if (isset($val['selected'])) {
                $value->selected = $val['selected'];
            }
            array_push($this->values, $value);
        }
    }

}

class PPrinter {

    public $noTabs = 0;

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
        $maxIndex = count($array)-1;
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
            $rule=array();
            $message=array();
            if (isset($input->validations)) {
                foreach ($input->validations as $key => $validation) {
                    if (!isset($validation['value'])) {
                        $validation['value'] = 'true';
                    }
                    $rule[$validation['type']]=$validation['value'];
                    $message[$validation['type']]="\"$validation[message]\"";
                }
                $rules[$input->name]=$rule;
                $messages[$input->name]=$message;
            }
            if($this->captcha){
                $rules['captcha'] = ['required'=>'true'];
                $messages['captcha'] =['required' => '"Ce champ est obligatoire"'];
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

    public function fromPost() {
        $i = 0;
        foreach ($this->inputs as $input) {
            if (isset($_POST[$input->name])) {
                $this->inputs[$i]->value = $_POST[$input->name];
            }
            $i++;
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

}

class MailForm {

    public $form;
    private $subject;
    private $expediteur;
    private $message;

    public function __construct($parameters = array()) {
        $this->form = new Form('mailform', $parameters);
        $this->form->submitValue = 'Envoyer';

        $this->subject = new Input('text', 'subject', [
            'label' => 'Objet',
            'value' => 'Installer WebRegatta',
            'style' => 'width:50%',
            'disabled' => true,
            'validations' => [
                ['type' => 'required', 'message' => 'Ce champ est obligatoire']
            ]
        ]);
        $this->expediteur = new Input(
                'text', 'sender', [
            'label' => 'Votre courriel',
            'style' => 'width:50%',
            'validations' => [
                ['type' => 'required', 'message' => 'Ce champ est obligatoire'],
                ['type' => 'email', 'message' => 'Ceci n\'est pas un adresse email valide']
            ]
                ]
        );
        $this->message = new Input(
                'textArea', 'message', ['label' => 'Votre message', 'rows' => 10, 'cols' => '120']);

        $this->form->inputs = [$this->expediteur, $this->subject, $this->message];
        $this->form->captcha = true;
    }

    public function display() {
        $this->form->display();
    }

}

//$mailform = new MailForm('mailform');
//$mailform->display();
/*

  $menu = new Input('uniqueChoice', 'menu');
  $arrayOfValues = [
  ['name' => 'coucou', 'label' => 'caca'],
  ['name' => 'pipi', 'label' => 'pipi'],
  ['name' => 'poupu', 'label' => 'poupu'],
  ];
  $menu->valuesFromArray($arrayOfValues);
  $menu->value = 'pipi';
  $menu->rendering = 'radios';

  $form = new Form('test');
  array_push($form->inputs, $menu);

  $form->display();

 */