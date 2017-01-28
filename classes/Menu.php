<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Menu
 *
 * @author lsantoca
 */
class MenuItem {

    public $message = '';
    public $link = '';
    public $submenu = NULL;
    private $printer;

    public function __construct($array) {
        $this->printer = new PPrinter();
        if (isset($array['message'])) {
            $this->message = $array['message'];
        }
        if (isset($array['link'])) {
            $this->link = $array['link'];
        }
        if (isset($array['submenu'])) {
            if (!is_array($array['submenu'])) {
                throw new Exception('A given submenu is not an array');
            }
            $submenu = new Menu($array['submenu']);
            $this->submenu = $submenu;
        }
    }

    function echoContent() {
        $msg = $this->message;
        $pre = '<a>';
        if ($this->link != '') {
            $pre = "<a href=\"$this->link\">";
        }
        $post = '</a>';
        $this->printer->echoWithTabs($pre . $msg . $post);
    }

    public function echoItem($noTabs=0) {
        $this->printer->noTabs=$noTabs;
        $this->echoContent();
        if (!empty($this->submenu)) {
            $this->submenu->echoUl($this->printer->noTabs);
        }
    }

}

class Menu {

    private $printer;
    public $items = [];

    public function __construct($itemsA = []) {
        $this->printer = new PPrinter();
        foreach ($itemsA as $itemA) {
            $item = new MenuItem($itemA);
            array_push($this->items, $item);
        }
    }

    public function echoUl($noTabs = 0) {
        if($noTabs != 0){
            $this->printer->noTabs = $noTabs;
        }
        $this->printer->echoOpen('<ul>');
        foreach ($this->items as $item) {
            $this->printer->echoOpen('<li>');
            $item->echoItem($this->printer->noTabs);
            $this->printer->echoClose('</li>');
        }
        $this->printer->echoClose('</ul>');
    }

    public function echoMenu($noTabs = 0) {
        $this->printer->noTabs = $noTabs;
        echo "\n";
        $this->printer->echoOpen('<div id="menu" class="white_over_dark">');
        $this->echoUl();
        $this->printer->echoClose('</div><!--menu-->');
    }

}

