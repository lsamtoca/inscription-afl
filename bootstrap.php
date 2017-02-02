<?php

error_reporting(-1);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Paris');
if(PHP_VERSION_ID < 50600){// if PHP < 5.6
    //echo mb_internal_encoding();
    //exit(0);
    mb_internal_encoding("UTF-8");
    iconv_set_encoding("internal_encoding", "UTF-8");
    iconv_set_encoding("output_encoding", "UTF-8");
    iconv_set_encoding("input_encoding", "UTF-8");
}

// Bootstrap -- the very least
include('classes/autoload.php');
include('bootstrap/assert.php');
// Read configuration file
// this builds the global array $config
include('bootstrap/readConfig.php');

function testModule($moduleName){
    global $config;
    $mName="module".ucfirst($moduleName);
    return (isset($config[$mName]) 
            and ($config[$mName]));
}


// Modules, conditionally included according to config.ini 
// they should be moved to 
// This is code to be executed to setup everything...
testModule('undergoingWorks') and include('bootstrap/undergoingWorks.php') ; //We exit from here
testModule('log') and include('bootstrap/log.php');
testModule('develOrProduction') and include('bootstrap/develOrProduction.php');
// Here I do not see yet how to get rid of this module
testModule('language') and include('bootstrap/language.php');
testModule('noLG') and include('bootstrap/noLG.php');
testModule('chooseBackground') and include('bootstrap/chooseBackground.php');


// These are modules to loaded that shall (maybe used)
//define('$config['pwdRecoveryOn']', $config['pwdRecoveryOn']);
require_once('databases/bds.php');
require_once('php/Layouts.php');

// Below not needed anymore as we have set an autoload
//require_once('php/Login.php'); 

require_once('php/Urls.php');
require_once('php/Formats.php');
//require_once('php/SuperAdmin.php');
require_once('php/StringsCrunch.php');
