<?php

// From PHP 5.6.1
// As of PHP 5.6.1 can also be specified as INI_SCANNER_TYPED.
// Should we use 
//$config=parse_ini_file('config.ini',false,INI_SCANNER_TYPED);
//$config = parse_ini_file('config.ini', false, INI_SCANNER_RAW);

if (file_exists('bootstrap/config.ini')) {
    $config = parse_ini_file('config.ini');
} else {
    $config = parse_ini_file('config.ini.default');
}

/*
  foreach ($config as $key => $value) {
  //echo $key; echo $value;
  if (strcmp($value, "true") == 0) {
  $config[$key] = true;
  } else {
  // Do we actually need this ?
  if(strcmp($value,"false") == 0){
  $config[$key] = false;
  }
  }
  }
 */
/*
  print_r($config);
  var_dump($config);
 */


$languages = explode(',', $config['availableLanguages']);
unset($config['availableLanguages']);

$config['availableLanguages'] = array();
foreach ($languages as $language) {
    array_push($config['availableLanguages'], explode('-', $language));
}

//$debugger = new debugger();
//$debugger->config();
