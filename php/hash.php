<?php

//$hashGetString = 'hash';
$hashGetString = 'k';
$lengthOfHash = 16;

function decodeIdFromHashString($hashstring) {
    global $lengthOfHash;
    return substr($hashstring, $lengthOfHash);
}

function decodeHashFromHashString($hashstring) {
    global $lengthOfHash;
    return substr($hashstring, 0, $lengthOfHash);
}

function encodeHashId($hash, $id) {
    global $lengthOfHash;
    $hash = substr($hash, 0, $lengthOfHash);
    $patch = $lengthOfHash - strlen($hash);
    $hash.=str_repeat('_', $patch);
    return $hash . $id;
}

function generateHash() {
    global $lengthOfHash;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $taille=  strlen($characters) - 1;
    
    $ret = '';
    for ($i = 0; $i < $lengthOfHash; $i++) {
        $ret.=$characters[rand(0,$taille)];
    }
    return $ret;
}


