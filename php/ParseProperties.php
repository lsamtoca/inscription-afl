<?php

function parseProperties($fileName, $path = 'bundle/') {
    $text = file_get_contents($path . $fileName . '.properties');
    $encoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1', true);
    $txtProperties = mb_convert_encoding($text,'UTF-8',$encoding);

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
            $pos = strrpos($value, $matches[0]);
            $value = substr($value, 0, $pos);
            $isWaitingOtherLine = true;
        } else {
            $value = str_replace('\n', "\n", $value);
            $value = str_replace('\p', "\n", $value);
            $value = str_replace('\t', "\t", $value);
            $result[$key] = $value;
            $isWaitingOtherLine = false;
        }
        unset($lines[$i]);
    }
    return $result;
}

function fixVariablesInProperties($string, $dictionary) {
    preg_match_all('/{([^}]*)}/', $string, $matches);
    foreach ($matches[1] as $match) {
        if (isset($dictionary[$match])) {
            $string = str_replace("{{$match}}", $dictionary[$match], $string);
        }
    }
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
