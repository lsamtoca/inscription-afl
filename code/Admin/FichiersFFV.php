<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FichierFFV
 *
 * @author lsantoca
 */
class FichiersFFV extends AnswerToForm {

    //protected $form;

    function __construct() {
        $this->form = new Form('fichiersFFV', [
            'label' => 'Mettre à jour les données de la FFV',
            'withinFieldSet' => 'true',
        ]);

        $this->form->coureurDbf = new Input('submit', 'coureurDbf', [
            'value' => 'Mettre à jour',
            'label' => 'Fichier COUREUR.DBF ',
            'help' => 'Ce fichier contient les informations '
            . ' sur les licencié(e)s français(es).'
                ]
        );

        $this->form->serieDbf = new Input('submit', 'serieDbf', [
            'value' => 'Mettre à jour',
            'label' => 'Fichier SERIE.DBF ',
            'help' => 'Ce fichier contient les informations'
            . ' sur les dériveurs.'
                ]
        );
        
        /*
        $this->form->planchesDbf = new Input('submit', 'planchesDbf', [
            'value' => 'Mettre à jour',
            'label' => 'Fichier PLANCHES.DBF ',
            'help' => 'Ce fichier contient les informations'
            . ' sur les planches à voile.'
                ]
        );
        */
        
        $this->form->linkInputs();
        $this->form->noSubmit = true;
    }

    static function downloadFileFtpAnonymous($remoteFtpHost, $remoteFile, $localFile) {

        if (false) {
            /* Try via wget */
            $systemCall = "wget -O $localFile -q ftp://$remoteFtpHost/$remoteFile";
            $returnValue = system($systemCall);
            if (!$returnValue) {
                $message = "Problème avec l'execution de la commande : " . $systemCall;
                Layouts::pageErreur($message);
                exit(0);
            }
        } else {/* try with ftp_get */
            // set up basic connection
            $conn_id = ftp_connect($remoteFtpHost);
            $ftp_user_name = $ftp_user_pass = 'anonymous';
            // login with username and password
            if (!ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
                $message = "Je ne peux pas me connecter au "
                        . "serveur $remoteFtpHost en tant que $ftp_user_name";
                ftp_close($conn_id);
                Layouts::pageErreur($message);
                exit(0);
            }
            ftp_pasv($conn_id, true); // Turn on passiv mode for ftp
            if (!ftp_get($conn_id, $localFile, $remoteFile, FTP_BINARY)) {
                // try to download $server_file and save to $local_file
                ftp_close($conn_id);
                $message = "Un problème est survenu en essayant  de télécharger le fichier $remoteFile.\n" .
                        "Idée pour resoudre ce problème : donnez les permissions 646 au fichier $localFile en local";
                Layouts::pageErreur($message);
                exit(0);
            }
            ftp_close($conn_id); // close the connection
        }
    }

    function isActive() {
        return isset($_POST['serieDbf']) || isset($_POST['coureurDbf']) || isset($_POST['planchesDbf']);
    }

    private function treatFile($dbfFile) {
        DbfFile::toTable("dbf/$dbfFile", $dbfFile);
        $msg = "Le fichier $dbfFile a été mis à jour";
        pageAnswer($msg, 'Admin.php');
        exit(0);
    }

    function execute() {
// Where are supposed to be the informations about informations about windsurfing ? 
//       $planchesNameOfFile = 'Table_HN.dbf';
///        $planchesNameOfFile = 'CAR_GLOB.dbf';

        if (self::isActive()) {
            if (isset($_POST['serieDbf'])) {
                self::downloadFileFtpAnonymous('ftp.ffvoile.net', 'tmpDbf/Tbl_VL_N.dbf', 'dbf/SERIE.DBF');
                $this->treatFile('SERIE.DBF');
            } else if (isset($_POST['coureurDbf'])) {
                self::downloadFileFtpAnonymous('ftp.ffvoile.net', 'tmpDbf/coureur.dbf', 'dbf/COUREUR.DBF');
                $this->treatFile('COUREUR.DBF');
            } else if (isset($_POST['planchesDbf'])) {
                self::downloadFileFtpAnonymous('ftp.ffvoile.net', "tmpDbf/$planchesNameOfFile", 'dbf/PLANCHES.DBF');
                $this->treatFile('PLANCHES.DBF');
            }
        }
        exit(0);
    }

    private function fileInfos($fileName) {
        $completeFileName = 'dbf/' . $fileName;

        $lastModified = new DateTime();
        $lastModified->setTimestamp(filemtime($completeFileName));
        $lastModified->modify('+1 month');
        // Below testing purposes
        //$lastModified->modify('+1 hour');
        $now = new DateTime();
        $isOld = $now > $lastModified;


        if (file_exists($completeFileName)) {
            $date = date("d/m/Y à H:i:s", filemtime($completeFileName));
            $text = "dernière mise à jour le $date";
            if ($isOld) {
                $infos = "<span style='color:red'>ce fichier est vieux, vous devriez le mettre à jour</span>  ($text)";
            } else {
                $infos = $text;
            }
        } else {
            $infos = 'ce fichier n\'existe pas sur ce serveur';
        }
        return "Fichier $fileName : $infos. <br />\n";
    }

    function html($noTabs = 0) {
        echo $this->fileInfos('COUREUR.DBF');
        echo $this->fileInfos('SERIE.DBF');
        echo '<br />';

        parent::html($noTabs);
    }

}
