<?php

require_once './php/mailer.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CourrielUtilisateurs {

    private $mailsClubs;
    private $mailsCoureurs;
    private $mailsAdmins;

    public function isActived() {
        return isset($_POST['envoyer_mail']);
    }

    public function sendMail() {
        global $config;

        //  get Infos about who is th Administrator
        $sql = 'SELECT * FROM `Administrateur` WHERE `ID_administrateur`=:ID';
        $assoc = array('ID' => $_SESSION['ID_administrateur']);
        $req = executePreparedQuery($sql, $assoc);
        $admin = $req->fetch();

        $errMsg = '';
        $warningMsg = '';
        $ackMsg = '';

        //  Prepare sending of mail(s)
        
        $mail = new MyMailer();
        $mail->setFrom("$admin[prenom] $admin[nom] <$config[webMasterEmail]>");
        $mail->addAddressTo("$admin[prenom] $admin[nom] <$admin[courriel]>");
        $mail->subject=filter_input(INPUT_POST, 'objet');
        $mail->messageText = ilter_input(INPUT_POST, 'message');
        $mail->addAddressCc(filter_input(INPUT_POST, 'cc'));
        
        /*
        $sender = $config['webMasterEmail']; 
        $to = $admin['courriel'];
        $subject = clean_post_var($_POST['objet']);
        $message = clean_post_var($_POST['message']);

        //$cc = $_POST['cc'];
        $cc0 = filter_input(INPUT_POST, 'cc', FILTER_SANITIZE_EMAIL);
        $cc = filter_var($cc0, FILTER_VALIDATE_EMAIL);
        if (!$cc) {
            $warningMsg .= "Warning. Le courriel en CC : '$cc' n'est pas valide.\n";
            $cc = '';
        }
        */
        
        $destinataires = explode(',', $_POST['to']); // destinataires en BCC
        $max = count($destinataires);
        $step = 50;
        for ($i = 0; $i < $max; $i+=$step) {
            $courriels = array_slice($destinataires, $i, $step);
            // SANITIZE AND VALIDATE ALL THE COURRIELS
 /*           foreach ($courriels as $index => $courriel) {
                $email0 = filter_var($courriel, FILTER_SANITIZE_EMAIL);
                $email = filter_var($email0, FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    $noDestinataire = $i +$index +1;
                    $warningMsg .= "Warning. Le courriel '$courriel' n'est pas valide "
                            . "(Déstinataire $noDestinataire de $max).\n";
                    unset($courriels[$index]);
                } else {
                    $courriels[$index] = $email;
                }
  
  
            } */
            //$bcc = implode(',', $courriels);
            // Skip this for testing purposes
            $ret = send_mail_text($sender, $to, $subject, $message, $cc, $bcc);
            //$ret = true;
            if (!$ret) {
                $errMsg .= "Error. Problème avec l'envoye à ces adresses :"
                        . " '$bcc'\n";
            } else {
                $ackMsg .= "Ack. Courriel envoyé à ces adresses : '$bcc'\n";
            }
        }

        // NOTIFY
        $newSender = $config['webMasterEmail'];
        $newTo = $admin['courriel'];
        $newSubject = 'Notification WebRegatta sur l\'envoye de votre message';
        $newMessage = "Vous avez envoyé ce message via WebRegatta:\n"
                . "Sujet : $subject\n"
                . "Votre Message : \n"
                . "$message\n"
                . "\n\nRapport sur des possibles erreurs.\n"
                . "Avertissements :\n$warningMsg\n"
                . "Courriels envoyés :\n$ackMsg\n"
                . "Erreurs :\n$errMsg\n";
        send_mail_text($newSender, $newTo, $newSubject, $newMessage);

        if ($errMsg != '') { // S'il ya eut un problème
            $msg = "Problème avec l'envoye du courriel.\n"
                    . "Vous allez etre notifié par courriel sur ce problème";
            pageErreur();
        } else { //Sinon
            // prepare and give an asnwer on the web
            $bcc = implode(',', $destinataires);
            if (strlen($bcc) >= 40) {
                $mails = explode(',', $bcc);
                $bccShort = $mails[0] . ",...," . $mails[count($destinataires) - 1];
            } else {
                $bccShort = $bcc;
            }
            $ackMessage = "Message envoyé de :\n\t$sender"
                    . "\n"
                    . "Message envoyé à:\n\t$bccShort";
            pageAnswer($ackMessage);
        }
        exit(0);
    }

    function __construct() {
        $sql = "SELECT DISTINCT `mail` from `Inscrit` where 1 ORDER BY `mail` ASC";
        $req = executePreparedQuery($sql, []);
        $this->mailsCoureurs = implode(',', $req->fetchAll(PDO::FETCH_COLUMN));

        $sql2 = "SELECT DISTINCT `courriel` from `Regate` where 1 ORDER BY `courriel` ASC";
        $req2 = executePreparedQuery($sql2, []);
        $this->mailsClubs = implode(',', $req2->fetchAll(PDO::FETCH_COLUMN));

        $sql3 = "SELECT DISTINCT `courriel` from `Administrateur` where 1 ORDER BY `courriel` ASC";
        $req3 = executePreparedQuery($sql3, []);
        $this->mailsAdmins = implode(',', $req3->fetchAll(PDO::FETCH_COLUMN));
    }

    public function html() {
        $replace = array('{mailsClubs}', '{mailsCoureurs}', '{mailsAdmins}', '{self}');
        $with = array($this->mailsClubs, $this->mailsCoureurs, $this->mailsAdmins, urlSelf());
        $template = file_get_contents(__DIR__ . '/Admin-html-courriel-utilisateurs.php');
        echo utf8_encode(str_replace($replace, $with, $template));
    }

}
