<?php


function parseProperties($fileName, $path = 'bundle/') {
    $text=file_get_contents($path . $fileName . '.properties');
    $txtProperties = utf8_encode($text);
//    mb_convert_encoding(
//            $text,
//            'ISO-8859-1',
//            'UTF-8');

    $result = array();
    $lines = explode("\n", $txtProperties);
    $key = "";
    $isWaitingOtherLine = false;
    foreach ($lines as $i => $line) {
        if (lineIsBlanc($line) || (!$isWaitingOtherLine && strpos($line, "#") === 0))
            continue;

        if (!$isWaitingOtherLine) {
            $key = trim(substr($line, 0, strpos($line, '=')));
            $value = ltrim(substr($line, strpos($line, '=') + 1, strlen($line)));
        } else {
            $value .= $line;
        }

        /* Check if ends with single '\' ---followed by blancks*/ 
        $matches=array();
        if(preg_match('/\\\[ \t]*$/',$line,$matches) > 0){
            //        if (strrpos($value, "\\") === strlen($value) - strlen("\\")) {
            $pos=strrpos($value, $matches[0]);
            $value = substr($value, 0, $pos);
            $isWaitingOtherLine = true;
        } else {
 //           echo "$key->'$value'\n" ;
            $result[$key] = $value;
            $GLOBALS[$key] = $value;        
            $isWaitingOtherLine = false;
        }
        unset($lines[$i]);
    }
    // exit(0);
   
    return $result;
}

function fixVariablesInProperties($string) {
    preg_match_all('/{([^}]*)}/', $string, $matches);
    foreach ($matches[1] as $match) {
        //echo "'$match'";
        if (isset($GLOBALS[$match])) {
            $string = str_replace("{{$match}}", $GLOBALS[$match], $string);
        } elseif (isset($$match)) {
            $string = str_replace("{{$match}}", $$match, $string);
        }
    }
    $string = str_replace('\n', "\n", $string);
    $string = str_replace('\t', "\t", $string);
    
//  echo "$string";
//    exit(0);
    return $string;
}

function lineIsBlanc($line){
    return preg_match("/^[ \t]*$/",$line) > 0 ;
}