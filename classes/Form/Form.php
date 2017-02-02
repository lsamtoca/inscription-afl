<?php


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
    private $withinFieldset = true;

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
        if (isset($this->noBreaks) && $this->noBreaks) {
            
        } else {
            $this->printer->echoWithTabs("<br /><hr />");
        }
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
            $captcha = new Input('captcha', 'captcha_' . $this->submitName);
            $captcha->display($this->printer->noTabs);
            $this->printer->echoWithTabs("<br />");
        }
        $submit->display($this->printer->noTabs);

        $this->printer->echoClose("</fieldset>");
        $this->printer->echoClose("</form><!--form id=$this->id -->");
    }

    public function displayUl($tabs = 0) {
        $this->printer->noTabs = $tabs;
        if (isset($this->noSubmit) && $this->noSubmit) {
            //$attributes = $this->getAttrStr(['class']);
            ;
        } else {
            $submit = new Input('submit', $this->submitName);
            $submit->value = $this->submitValue;
            array_push($this->inputs, $submit);
        }


        $this->printer->echoOpen(
                "<form class='ulist' id='$this->id' action='$this->action' method='$this->method'>");

        if (isset($this->withinFieldset) && $this->withinFieldset) {
            $this->printer->echoOpen("<fieldset>");
        }

        if (isset($this->label)) {
            if (isset($this->withinFieldset) && $this->withinFieldset) {
                $this->printer->echoWithTabs("<legend>$this->label</legend>");
            } else {
                $this->noBreaks = true;
                echo $this->echoLabel();
            }
        }

        $this->printer->echoOpen("<ul>");
        foreach ($this->inputs as $input) {
            $this->printer->echoOpen("<li>");
            $input->nobreak = true;
            $input->display($this->printer->noTabs);
            $this->printer->echoClose("</li>");
        }
        if (isset($this->captcha) && $this->captcha == true) {
            $captcha = new Input('captcha', 'captcha_' . $this->submitName);
            $this->printer->echoOpen("<li>");
            $captcha->display($this->printer->noTabs);
            $this->printer->echoClose("</li>");
        }
        $this->printer->echoOpen("<li>");
        $this->printer->echoClose("</li>");
        $this->printer->echoClose("</ul>");

        if (isset($this->withinFieldset) && $this->withinFieldset) {
            $this->printer->echoClose("</fieldset>");
        }

        $this->printer->echoClose("</form><!--form $this->id -->");
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
// Default value is true if it has not been set
                    if (!isset($validation['value'])) {
                        $validation['value'] = 'true';
                    }
                    $rule[$validation['type']] = $validation['value'];
                    $message[$validation['type']] = "\"$validation[message]\"";
                }
                $rules[$input->name] = $rule;
                $messages[$input->name] = $message;
            }
            if ($input->name == 'captcha') {
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

    public function displayJQueryDatePickers($tabs = 0) {
        $this->printer->noTabs = $tabs;
        $this->printer->echoOpen("<script type='text/javascript'>");
        $this->printer->echoOpen("$(document).ready(function () {");
        foreach ($this->inputs as $input) {
            if ($input->type == 'datePicker') {
                $jQuerySelector = "form input[name='$input->name']";
                $this->printer->echoOpen("$(\"$jQuerySelector\").datepicker({");
                $this->printer->echoWithTabs("dateFormat: \"$input->format\",");
                $this->printer->echoWithTabs("defaultDate: \"$input->default\",");
                $this->printer->echoWithTabs("changeYear: true,");
                $this->printer->echoWithTabs("yearRange:\"c-20:c+20\"");
                $this->printer->echoClose("});");
            }
        }
        $this->printer->echoClose("});");
        $this->printer->echoClose("</script>\n");
    }

    public function isCompleted() {
        return isset($_POST[$this->submitName]);
    }

    public function isValidCaptcha() {
        $captchaName = 'captcha_' . $this->submitName;
        return isset($_POST[$captchaName]) && $_POST[$captchaName] == $_SESSION[$captchaName];
    }

    private function fromPostMChoice($input) {
        $values = array();
        $name = $input->name;
        foreach ($input->values as $key => $value) {
            if (filter_has_var(INPUT_POST, $name - $value->name)) {
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
        foreach ($keys as $key) {
            $pKeys[$key] = "$name-$key";
        }
        foreach ($_POST as $pKey => $value) {
            if (in_array($pKey, $pKeys)) {
                $key = substr($pKey, strlen($name) + 1);
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

    public function fromPost($defaultFilter = FILTER_DEFAULT) {
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
                        $this->inputs[$name]->value = filter_input(INPUT_POST, $name, $defaultFilter);
                    }
            }
        }
    }

    public function toDb($id) {
        if (isset($this->table)) {
            $fields = array();
            $values = array();
            foreach ($this->inputs as $input) {
                if (isset($input->tableField)) {
                    array_push($fields, $input->tableField . "=:" . $input->tableField);
                    if (isset($input->tableFieldTransform)) {
                        if (!is_callable($input->tableFieldTransform)) {
                            throw new Exception($input->tableFieldTransform . " is not a callable function");
                        }
                        $value = call_user_func($input->tableFieldTransform, $input->value);
                    } else {
                        $value = $input->value;
                    }
                    $values[$input->tableField] = $value;
                }
            }
            if (count($fields) > 0) {
                $sql0 = "SHOW columns FROM $this->table";
                $req = executePreparedQuery($sql0, array());
                $idField = $req->fetch(PDO::FETCH_COLUMN);
                //$idField='ID_'.strtolower($this->table);
                $sql = "UPDATE `$this->table` SET "
                        . implode(',', $fields) . " WHERE $idField = $id";
                executePreparedQuery($sql, $values);
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
