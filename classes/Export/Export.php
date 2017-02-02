<?php

// Why is ths needed
// require_once 'php/StringsCrunch.php';

class Export {

    public $name = '';
    public $contentType = '';
    protected $content = '';

    public function display() {
        header("Content-Type: $this->contentType");
        header("Content-Disposition: attachment;filename=\"$this->name\"");
        header('Cache-Control: max-age=0');
        echo $this->content;
    }

}
