<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Input
 *
 * @author lsantoca
 */
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

class Input {

//    static 
    private $inputTypes = array(
        'text', 'textArea', 'menu', 'radios', 'multipleChoice',
        'sortableMultipleChoice',
        'singleRadio', 'checkBox', 'hidden', 'menu', 'submit',
        'uniqueChoice', 'captcha', 'file', 'datePicker', 'separator'
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

        $attributesPre = $this->getAttrStr(['labelclass', 'labelid']);
        $attributes = str_replace('label', '', $attributesPre);

        if (isset($this->help) && $this->help) {
            $this->printer->echoOpen("<label $attributes>");
            $this->printer->echoWithTabs($label);
            $this->printer->echoOpen('<span class="help">');
            $this->printer->echoWithTabs('<span class="normalwidth">' . $this->help . '</span>');
            $this->printer->echoClose('</span>');
            $this->printer->echoClose('</label>');
        } else {
            $stringToPrint = $this->tagWithContent('label', $label, $attributes);
            $this->printer->echoWithTabs($stringToPrint);
        }
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

        $attributes = "src='$url?rand=$rand&captchaName=$this->name' id='captchaimg'";
        $this->printer->echoWithTabs($this->closedTag('img', $attributes));
        $input = new Input('text', $this->name, [
            'label' => 'Rentrez le code à la gauche',
            'size' => 6,
            'nobreak' => true]);
        $input->display($this->printer->noTabs);
    }

    private function echoText() {
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
        if (!isset($this->nolabel) or ! $this->nolabel == true) {
            $this->labelclass = 'left';
            $this->echoLabel();
        }
        $attributes = $this->getAttrStr(['id', 'class', 'rows', 'cols', 'size']);
        $this->printer->echoWithTabs(
                "<textarea name='$this->name'$attributes>$this->value</textarea>");
    }

    private function echoHidden() {
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
                    $this->tagWithContent('span', '', "class='ui-icon ui-icon-arrowthick-2-n-s'"));
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
        if (isset($this->label)) {
            $this->labelclass = 'left';
            $this->echoLabel();
        }
        $this->printer->echoWithTabs(
                "<input type='submit' name='$this->name' value='$this->value'/>");
    }

    private function echoFile() {
        if (isset($this->maxFileSize)) {
            $hidden = new Input('hidden', 'MAX_FILE_SIZE');
            $hidden->value = $this->maxFileSize;
            $hidden->display($this->printer->noTabs);
        }
        $this->echoLabel();
        $this->printer->echoWithTabs(
                "<input type='file' name='$this->name'/>");
    }

    private function echoDatePicker() {
        $this->value = $this->default;
        $this->echoText();
    }

    private function echoSeparator() {
        if (isset($this->value) && $this->value != '') {
            //$this->printer->echoWithTabs("<hr />$this->value<hr />");
            $this->printer->echoWithTabs("<hr /><b>$this->value</b><br />");
        } else {
            $this->printer->echoWithTabs("<hr />");
        }
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
            case 'file':
                $this->echoFile();
                break;
            case 'datePicker':
                $this->echoDatePicker();
                break;
            case 'separator':
                $this->echoSeparator();
                break;
            default:
            case 'text':
                $this->echoText();
                break;
        }
    }

}

