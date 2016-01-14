<?php

function parseProperties($fileName, $path = 'bundle/') {
    $text = file_get_contents($path . $fileName . '.properties');
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
            // WE do not want spaces from left
            $value .= ltrim($line);
        }

        /* Check if ends with single '\' ---followed by blancks */
        $matches = array();
        if (preg_match('/\\\[ \t]*$/', $line, $matches) > 0) {
            //        if (strrpos($value, "\\") === strlen($value) - strlen("\\")) {
            $pos = strrpos($value, $matches[0]);
            $value = substr($value, 0, $pos);
            $isWaitingOtherLine = true;
        } else {
            //           echo "$key->'$value'\n" ;
            $value = str_replace('\n', "\n", $value);
            $value = str_replace('\p', "\n", $value);
            $string = str_replace('\t', "\t", $value);
            $result[$key] = $value;
            $isWaitingOtherLine = false;
        }
        unset($lines[$i]);
    }
    // exit(0);

    return $result;
}

function fixVariablesInProperties($string, $dictionary) {
    preg_match_all('/{([^}]*)}/', $string, $matches);
    foreach ($matches[1] as $match) {
        //echo "'$match'";
        if (isset($dictionary[$match])) {
            $string = str_replace("{{$match}}", $dictionary[$match], $string);
        } //elseif (isset($$match)) {
//            $string = str_replace("{{$match}}", $$match, $string);
//        }
    }

//  echo "$string";
//    exit(0);
    return $string;
}

function lineIsBlanc($line) {
    return preg_match("/^[ \t]*$/", $line) > 0;
}

function echoProvidedWords($propertyFile, $path = 'bundle/') {
    $dict = parseProperties($propertyFile, $path);
    $l = count($dict);
    $i = 1;
    echo "// provides $l words\n";
    echo '$provides=array(' . "\n";
    foreach ($dict as $key => $value) {
        echo "\t'$key'";
        if ($i != $l) {
            echo ",\n";
        } else {
            echo "\n";
        }
        $i++;
    }
    echo ");\n";
}

function echoProvidedWordsWithContents($propertyFile, $path = 'bundle/') {
    $dict = parseProperties($propertyFile, $path);
    $l = count($dict);
    $i = 1;
    echo "// provides $l words\n";
    echo '$providedDict=array(' . "\n";
    foreach ($dict as $key => $value) {
        echo "\t'$key' => '$value'";
        if ($i != $l) {
            echo ",\n";
        } else {
            echo "\n";
        }
        $i++;
    }
    echo ");\n";
}
