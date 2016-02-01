<?php
$config=parse_ini_file('config.ini');

$languages= explode(',',$config['availableLanguages']);
unset($config['availableLanguages']);

$config['availableLanguages'] = array();
foreach ($languages as $language){
    array_push($config['availableLanguages'],
                explode('-', $language)); 
}

 //print_r($config['availableLanguages']);
 //exit(0);