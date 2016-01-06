<?php

error_reporting(-1);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Paris');

// Can this be placed in php/Mailer.php ?
$courrielDeveloppeur = 'luigi.santocanale@lif.univ-mrs.fr';
define('PWDRECOVERYON', true);
//define('PWDRECOVERYON', false);


// Bootstrap
// This is code to be executed to setup everything...
include('bootstrap/log.php');
include('bootstrap/develOrProduction.php');
include('bootstrap/assert.php');
include('bootstrap/noLG.php');

// These are modules to loaded that shall (maybe used)
require_once('databases/bds.php');
require_once('php/Layouts.php');
require_once('php/Login.php');
require_once('php/Urls.php');
require_once('php/Formats.php');
//require_once('php/SuperAdmin.php');
require_once('php/StringsCrunch.php');
