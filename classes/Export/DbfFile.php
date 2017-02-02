<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DbfFile
 *
 * @author lsantoca
 */
class DbfFile {

    private $useDBase = false;
    private $handle;
    public $no_records = 0;
    public $header = array();
    private $unpackString = '';

    function __destruct() {
        if ($this->useDBase) {
            dbase_close($this->handle);
        } else {
            fclose($this->handle);
        }
    }

    function __construct($fileName) {
        if (function_exists('dbase_open')) {
            $this->useDBase = true;
        }

        if ($this->useDBase) {
            $this->handle = dbase_open($fileName, 0);
            $this->header = dbase_get_header_info($this->handle);
            $this->no_records = dbase_numrecords($this->handle);
        } else {
            $this->handle = fopen($fileName, 'r');
            if ($this->handle == NULL) {
                return NULL;
            }
            $buf = fread($this->handle, 32);
            $fileHeader = unpack("VRecordCount/vFirstRecord/vRecordLength", substr($buf, 4, 8));
            $this->no_records = $fileHeader['RecordCount'];
            $this->firstRecord = $fileHeader['FirstRecord'];
            $this->recordLength = $fileHeader['RecordLength'];

            $goon = true;
            while ($goon && !feof($this->handle)) { // read fields: 
                $buf = fread($this->handle, 32);
                if (substr($buf, 0, 1) == chr(13)) {
                    $goon = false;
                } // end of field list 
                else {
                    $field = unpack("a11name/A1fieldtype/Voffset/Clength/Cfielddec", substr($buf, 0, 18));
                    $this->unpackString.="A$field[length]$field[name]/";
                    array_push($this->header, array_map('trim', $field));
                }
            }
        }
    }

    public function getRow($recordNo) {
        if ($this->useDBase) {
            return dbase_get_record_with_names($this->handle, $recordNo);
        } else {
            fseek($this->handle, $this->firstRecord + 1 + ($recordNo - 1) * $this->recordLength);
            $buf = fread($this->handle, $this->recordLength);
            $row = unpack($this->unpackString, $buf);
            return array_map('trim', $row);
        }
    }

    static function toTable($localFile, $table) {

        function quoteString($str) {
            return '`' . $str . '`';
        }

        $dbfFile = new DbfFile($localFile);
        try {
            $bd = newBd();

            // Creation de la table MYSQL
            $sql = "DROP TABLE IF EXISTS `$table`";
            $req = $bd->prepare($sql);
            $req->execute();

            // Create the table with the columns
            foreach ($dbfFile->header as $key => $column) {
                $colNames[$key] = $column['name'];
                $sqls[$key] = quoteString($column['name']) . ' VARCHAR(' . $column['length'] . ')';
            }
            $sql = "CREATE TABLE `$table` ("
                    . implode(", ", $sqls)
                    . ");";
            executePreparedQuery($sql, array(), $bd);


            $columns = implode(',', $colNames);
            $values = ":" . implode(',:', $colNames);
            $sql = "insert into `$table` ($columns) VALUES ($values)";
            $req = $bd->prepare($sql);

            for ($i = 1; $i <= $dbfFile->no_records; $i++) {
                $row = $dbfFile->getRow($i);
                $req->execute($row);
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

}
