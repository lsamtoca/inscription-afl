<?php

/* RemoteFile is ftp://ftp.ffvoile.net/tmpDbf/coureur.dbf */
$remoteFile = 'tmpDbf/coureur.dbf';
$remoteFtpHost = "ftp.ffvoile.net";
$localFile = 'dbf/COUREUR.DBF';

function echoLineInHtml($str) {
    echo "<br />$str<br />";
}

function htmldump($obj) {
    echo '<pre>';
    print_r($obj);
    echo '</pre>';
}

function downLoadDbf($remoteFile, $localFile) {
    global $remoteFtpHost;

    if (false) {
        /* Try via wget */
        $systemCall = "wget -O $localFile -q ftp://$remoteFtpHost/$remoteFile";
        $returnValue = system($systemCall);


        if (!$returnValue) {
            $message = "Problème avec l'execution de la commande : " . $systemCall;
            pageErreur($message);
            exit(0);
        }
    } else {/* try with ftp_get */

// set up basic connection
        $conn_id = ftp_connect($remoteFtpHost);
        $ftp_user_name = $ftp_user_pass = 'anonymous';

// login with username and password
        if (!ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
            $message = 'Je ne peux pas me connecter au serveur de la FFV';
            ftp_close($conn_id);
            pageErreur($message);
            exit(0);
        }
        ftp_pasv($conn_id, true); // Turn on passiv mode for ftp
        if (!ftp_get($conn_id, $localFile, $remoteFile, FTP_BINARY)) {
            // try to download $server_file and save to $local_file

            ftp_close($conn_id);
            $message = "Un problème est survenu en essayant  de télécharger le fichier $remoteFile depuis la FFV.\n" .
                    "Idée pour resoudre ce problème : donnez les permissions 646 au fichier $localFile en local";
            pageErreur($message);
            exit(0);
        }

        ftp_close($conn_id); // close the connection
    }
}

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

}

function updateFromDbf($localFile, $table) {


    function quoteString($str) {
        return '`' . $str . '`';
    }

    $dbfFile = new DbfFile($localFile);
    //
    // htmldump($dbfFile);
    //

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
        //echo "<br />" . $sql . "<br />";
        executePreparedQuery($sql, array(), $bd);


        $columns = implode(',', $colNames);
        $values = ":" . implode(',:', $colNames);
        $sql = "insert into `$table` ($columns) VALUES ($values)";
        $req = $bd->prepare($sql);

        for ($i = 1; $i <= $dbfFile->no_records; $i++) {
//        for ($i = 1; $i <= 200; $i++) {

            $row = $dbfFile->getRow($i);
            //            htmldump($row);
            $req->execute($row);
        }
    } catch (Exception $e) {
// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}

function updateCoureur() {
    downLoadDbf('tmpDbf/coureur.dbf', 'dbf/COUREUR.DBF');
    updateFromDbf('dbf/COUREUR.DBF', 'COUREUR.DBF');
}

function updateSerie() {
    global $locaFile, $remoteFile;

    downLoadDbf('tmpDbf/Tbl_VL_N.dbf', 'dbf/SERIE.DBF');
    updateFromDbf('dbf/SERIE.DBF', 'SERIE.DBF');
}

function giveAnswer($fic) {
    $msg = "Le fichier $fic a été mis à jour";
//redirect($msg, 3000, 'Admin.php');
    pageAnswer($msg, 'Admin.php');
}

if (isset($_GET['serie'])) {
    updateSerie();
    giveAnswer('SERIE.DBF');
} else {
    updateCoureur();
    giveAnswer('COUREUR.DBF');
}


