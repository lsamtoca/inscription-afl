<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Menus
 *
 * @author lsantoca
 */
class Menus {

    private $moduleDonate = true;
    private $moduleAdvertise = true;
    private $availableLanuguages = [];
    private $menuUsual = [];
    private $menuHomeAdmin = [];
    private $menuHomeCLub = [];

    private static function languageSelector($lang, $msg) {
        return "<span class='languageSelector' title='$lang'>$msg</span>";
    }

    private static function multipleLanguage($name, $defaultMsg = '') {
        return "<span class='msg' id='$name'>$defaultMsg</span>";
    }

    public function __construct() {
        global $config;
        if (isset($config['moduleDonate'])) {
            $this->moduleDonate = $config['moduleDonate'];
        }
        if (isset($config['moduleAdvertise'])) {
            $this->moduleDonate = $config['moduleAdvertise'];
        }
        if (isset($config['availableLanguages'])) {
            $this->availableLanguages = $config['availableLanguages'];
        }
        $sousMenuHome = [
            [
                'message' => $this->multipleLanguage('about', 'A propos'),
                'link' => 'about#aPropos',
            ],
            [
                'message' => $this->multipleLanguage('oRace', 'Ouvrir une régate'),
                'link' => 'about#openRace',
            ]
        ];
        if ($this->moduleDonate) {
            array_push($sousMenuHome, [
                'message' => $this->multipleLanguage('donate', 'Faire un don'),
                'link' => 'about#faireUnDon'
                    ]
            );
        }
        if ($this->moduleAdvertise) {
            array_push($sousMenuHome, [
                'message' => $this->multipleLanguage('ceLogiciel', 'Obtenir ce logiciel'),
                'link' => 'about#ceLogiciel'
                    ]
            );
        }

        $menuItem_Home = [
            'message' => $this->multipleLanguage('home', 'Accueil'),
            'link' => 'index',
            'submenu' => $sousMenuHome
        ];

        $menuItem_Club = ['message' => 'Accueil Club', 'link' => 'Regate'];
        $menuItem_Admin = ['message' => 'Accueil Administrateur', 'link' => 'Admin'];
        $menuItem_Login = ['message' => 'Connexion', 'link' => 'Login'];
        $menuItem_Logout = ['message' => 'Deconnexion', 'link' => 'Logout'];
        $menuItem_ChPwd = ['message' => 'Modifiez le mot de passe', 'link' => 'changePwd'];

        $sousMenuLanguage = [];
        foreach ($this->availableLanguages as $language) {
            array_push(
                    $sousMenuLanguage, ['message' => languageSelector($language[0], $language[1])]
            );
        }

        $menuItem_Language = [
            'message' => 'Choose your language',
            'subMenu' => $sousMenuLanguage
        ];


        $this->menuUsual = [$menuItem_Home, $menuItem_Login];
        $this->menuLanguage = [$menuItem_Home, $menuItem_Language];
        $this->menuHome = [$menuItem_Home];
        $this->menuHomeClub = [$menuItem_Home,
            $menuItem_Club,
            $menuItem_ChPwd,
            $menuItem_Logout
        ];

        $this->menuHomeAdmin = [
            $menuItem_Home,
            $menuItem_Admin,
            $menuItem_ChPwd,
            $menuItem_Logout
        ];
    }

    private function htmlAdmin() {
        $menu = new Menu($this->menuHomeAdmin);
        $menu->echoMenu();
    }

    private static function htmlClub() {
        $menu = new Menu($this->menuHomeClub);
        $menu->echoMenu();
    }

    private function htmlUsual() {
        $menu = new Menu($this->menuUsual);
        $menu->echoMenu();
    }

    function html($menuA = []) {

        if (empty($menuA)) {
            $login = new Login();
            if ($login->adminCorrectlyLogged()) {
                //$menu = $menuHomeAdmin;
                $this->htmlAdmin();
            } elseif ($login->clubCorrectlyLogged()) {
                $this->htmlClub();
                //$menu = $menuHomeClub;
            } else {
                $this->htmlUsual();
//                $menu = $menuUsual;
            }
        } else {
            $menu = new Menu($menuA);
            $menu->echoMenu();
        }
    }

    public $sousMenuHome = array();

}
