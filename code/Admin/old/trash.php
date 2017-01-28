<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Trash
 * 
 * 
 * 
 * 
 */

function updateCoureurManually() {
    global $localFile;
    /*
      $message = 'Je ne peux pas continuer, car ce serveur ne supporte pas la biliothèque dBase pour Php.'
      . "\nDemandez à l'administrateur système d'installer sur ce serveur les extensions PECL http://php.net/manual/en/install.pecl.php";
      pageErreur($message);
      exit(0);
      echo_dbf($localFile);
      exit(0);
     */

    $fdbf = fopen($localFile, 'r');

// Read the header
    $buf = fread($fdbf, 32);
    $header = unpack("VRecordCount/vFirstRecord/vRecordLength", substr($buf, 4, 8));

// Read the columns
    $columns = array();
    $goon = true;
    $unpackString = '';
    while ($goon && !feof($fdbf)) { // read fields: 
        $buf = fread($fdbf, 32);
        if (substr($buf, 0, 1) == chr(13)) {
            $goon = false;
        } // end of field list 
        else {
            $field = unpack("a11name/A1fieldtype/Voffset/Clength/Cfielddec", substr($buf, 0, 18));
            /*
              echo 'Field: ' . json_encode($field) . '<br/>';
              print_r($field);
              echo '<br />';
              $l = strlen(trim($field['name']));
              echo $field['name'].", length : $l.<br />";
             */
            $unpackString.="A$field[length]$field[name]/";
            array_push($columns, array('name' => trim($field['name']), 'length' => $field['length']));
        }
    }

    $rows = array();
    fseek($fdbf, $header['FirstRecord'] + 1); // move back to the start of the first record (after the field definitions)
//for ($i = 1; $i <= $header['RecordCount']; $i++) {
    for ($i = 1; $i <= 10; $i++) {
        $buf = fread($fdbf, $header['RecordLength']);
        $row = unpack($unpackString, $buf);
        array_push($rows, $row);
//        echo 'record: ' . json_encode($record) . '<br/>';
// echo $i . $buf . '<br/>';
    } //raw record

    fclose($fdbf);
//exit(0);

    updateCoureur($columns, $rows);
}

function updateCoureurUsingDBase() {

    global $localFile;

    $handle = dbase_open($localFile, 0);
    $columns = dbase_get_header_info($handle);
    $no_records = dbase_numrecords($handle);
    for ($i = 1; $i <= $no_records; $i++) {
        $rows[$i] = dbase_get_record_with_names($handle, $i);
        unset($rows[$i]['deleted']);
    }

    updateCoureur($columns, $rows);
// $columns = dbase_get_header_info($handle);
// $no_records = dbase_numrecords($handle);
// $rows;
}

function echo_dbf($dbfname) {
// From
// http://php.net/manual/en/book.dbase.php
    $fdbf = fopen($dbfname, 'r');
    $fields = array();
    $buf = fread($fdbf, 32);
    $header = unpack("VRecordCount/vFirstRecord/vRecordLength", substr($buf, 4, 8));
    echo 'Header: ' . json_encode($header) . '<br/>';
    $goon = true;
    $unpackString = '';
    while ($goon && !feof($fdbf)) { // read fields: 
        $buf = fread($fdbf, 32);
        if (substr($buf, 0, 1) == chr(13)) {
            $goon = false;
        } // end of field list 
        else {
            $field = unpack("a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf, 0, 18));
            echo 'Field: ' . json_encode($field) . '<br/>';
            $unpackString.="A$field[fieldlen]$field[fieldname]/";
            array_push($fields, $field);
        }
    }
    fseek($fdbf, $header['FirstRecord'] + 1); // move back to the start of the first record (after the field definitions) 
    for ($i = 1; $i <= $header['RecordCount']; $i++) {
        $buf = fread($fdbf, $header['RecordLength']);
        $record = unpack($unpackString, $buf);
        echo 'record: ' . json_encode($record) . '<br/>';
        echo $i . $buf . '<br/>';
    } //raw record 
    fclose($fdbf);
}

